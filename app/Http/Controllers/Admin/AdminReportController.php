<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Attendance;
use App\Models\Batch;
use App\Models\Centre;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\MarketingExpense;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    /**
     * Reports Executive Dashboard Hub
     */
    public function index()
    {
        $totalResults = ExamResult::count();
        $passedResults = ExamResult::whereIn('result_status', ['passed', 'pass'])->count();
        $passRate = $totalResults > 0 ? round(($passedResults / $totalResults) * 100, 1) : 0;

        $stats = [
            'total_students'       => Student::count(),
            'active_students'      => Student::where('status', 'active')->count(),
            'total_centres'        => Centre::where('status', 'active')->count(),
            'total_collected_fees' => (float) FeePayment::sum('amount'),
            'total_dues'           => max(0.00, (float) Admission::sum('net_fee') - (float) Admission::sum('total_paid')),
            'total_refunds'        => (float) FeePayment::onlyTrashed()->sum('amount'),
            'total_expenses'       => (float) (Expense::sum('amount') + MarketingExpense::sum('amount_spent')),
            'total_exams'          => Exam::count(),
            'total_results'        => $totalResults,
            'pass_rate'            => $passRate,
        ];

        return view('admin.reports.index', compact('stats'));
    }

    // ==========================================
    // 1. STUDENT & ADMISSIONS REPORT
    // ==========================================

    public function students(Request $request)
    {
        $query = Student::with(['admissions.course', 'admissions.centre', 'admissions.batch']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('full_name', 'like', "%{$s}%")
                    ->orWhere('roll_no', 'like', "%{$s}%")
                    ->orWhere('registration_no', 'like', "%{$s}%")
                    ->orWhere('mobile', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('centre_id')) {
            $query->whereHas('admissions', function ($q) use ($request) {
                $q->where('centre_id', $request->centre_id);
            });
        }

        if ($request->filled('course_id')) {
            $query->whereHas('admissions', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $students = $query->latest('id')->paginate(20)->withQueryString();

        $stats = [
            'total'    => (clone $query)->count(),
            'active'   => (clone $query)->where('status', 'active')->count(),
            'male'     => (clone $query)->where('gender', 'male')->count(),
            'female'   => (clone $query)->where('gender', 'female')->count(),
        ];

        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();
        $courses = Course::where('status', true)->orderBy('course_name')->get();

        return view('admin.reports.students', compact('students', 'stats', 'centres', 'courses'));
    }

    public function exportStudents(Request $request): StreamedResponse
    {
        $query = Student::with(['admissions.course', 'admissions.centre']);

        if ($request->filled('centre_id')) {
            $query->whereHas('admissions', function ($q) use ($request) {
                $q->where('centre_id', $request->centre_id);
            });
        }
        if ($request->filled('course_id')) {
            $query->whereHas('admissions', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $filename = 'students_report_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Roll No', 'Registration No', 'Student Name', 'Mobile', 'Email', 'Gender', 'Centre', 'Course', 'Admission Date', 'Status']);

            $query->chunk(250, function ($students) use ($handle) {
                foreach ($students as $s) {
                    $latestAdm = $s->admissions->first();
                    fputcsv($handle, [
                        $s->roll_no ?? '-',
                        $s->registration_no ?? '-',
                        $s->full_name,
                        $s->mobile,
                        $s->email ?? '-',
                        ucfirst($s->gender ?? '-'),
                        $latestAdm && $latestAdm->centre ? $latestAdm->centre->centre_name : '-',
                        $latestAdm && $latestAdm->course ? $latestAdm->course->course_name : '-',
                        ($latestAdm && $latestAdm->admission_date) ? \Carbon\Carbon::parse($latestAdm->admission_date)->toDateString() : ($s->created_at ? $s->created_at->toDateString() : '-'),
                        ucfirst($s->status),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ==========================================
    // 2. FEES & REVENUE COLLECTIONS REPORT
    // ==========================================

    public function fees(Request $request)
    {
        $query = FeePayment::with(['student', 'centre']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('receipt_no', 'like', "%{$s}%")
                    ->orWhere('transaction_ref', 'like', "%{$s}%")
                    ->orWhereHas('student', function ($sq) use ($s) {
                        $sq->where('full_name', 'like', "%{$s}%")
                            ->orWhere('roll_no', 'like', "%{$s}%");
                    });
            });
        }

        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        }

        if ($request->filled('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->latest('payment_date')->latest('id')->paginate(20)->withQueryString();

        $stats = [
            'total_collected' => (float) (clone $query)->sum('amount'),
            'total_refunded'  => (float) FeePayment::onlyTrashed()->sum('amount'),
            'total_dues'      => max(0.00, (float) Admission::sum('net_fee') - (float) Admission::sum('total_paid')),
            'online_mode'     => (float) (clone $query)->whereIn('payment_mode', ['online', 'upi'])->sum('amount'),
            'offline_mode'    => (float) (clone $query)->whereIn('payment_mode', ['cash', 'cheque', 'bank_transfer', 'card'])->sum('amount'),
        ];

        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.reports.fees', compact('payments', 'stats', 'centres'));
    }

    public function exportFees(Request $request): StreamedResponse
    {
        $query = FeePayment::with(['student', 'centre']);

        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        }
        if ($request->filled('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }

        $filename = 'fees_collection_report_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Receipt No', 'Student Name', 'Roll No', 'Centre Name', 'Amount', 'Payment Mode', 'Date', 'Transaction Ref']);

            $query->chunk(250, function ($payments) use ($handle) {
                foreach ($payments as $p) {
                    fputcsv($handle, [
                        $p->receipt_no ?? ('RCP-' . $p->id),
                        $p->student ? $p->student->full_name : '-',
                        $p->student ? $p->student->roll_no : '-',
                        $p->centre ? $p->centre->centre_name : '-',
                        number_format($p->amount, 2, '.', ''),
                        strtoupper($p->payment_mode),
                        $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->toDateString() : '-',
                        $p->transaction_ref ?? '-',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ==========================================
    // 3. ATTENDANCE REPORT
    // ==========================================

    public function attendance(Request $request)
    {
        $query = Attendance::with(['student', 'batch', 'centre']);

        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('attendance_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('attendance_date', '<=', $request->date_to);
        }

        $attendances = $query->latest('attendance_date')->latest('id')->paginate(25)->withQueryString();

        $totalRecords = (clone $query)->count();
        $presentRecords = (clone $query)->where('status', 'present')->count();
        $absentRecords = (clone $query)->where('status', 'absent')->count();
        $lateRecords = (clone $query)->where('status', 'late')->count();

        $stats = [
            'total'        => $totalRecords,
            'present'      => $presentRecords,
            'absent'       => $absentRecords,
            'late'         => $lateRecords,
            'present_rate' => $totalRecords > 0 ? round(($presentRecords / $totalRecords) * 100, 1) : 0,
        ];

        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();
        $batches = Batch::where('status', 'active')->orderBy('batch_name')->get();

        return view('admin.reports.attendance', compact('attendances', 'stats', 'centres', 'batches'));
    }

    public function exportAttendance(Request $request): StreamedResponse
    {
        $query = Attendance::with(['student', 'batch', 'centre']);

        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        }
        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        $filename = 'attendance_report_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Student Name', 'Roll No', 'Batch Name', 'Centre Name', 'Status', 'In Time', 'Out Time']);

            $query->chunk(250, function ($attendances) use ($handle) {
                foreach ($attendances as $a) {
                    fputcsv($handle, [
                        $a->attendance_date ? $a->attendance_date->toDateString() : '-',
                        $a->student ? $a->student->full_name : '-',
                        $a->student ? $a->student->roll_no : '-',
                        $a->batch ? $a->batch->batch_name : '-',
                        $a->centre ? $a->centre->centre_name : '-',
                        ucfirst($a->status),
                        $a->in_time ?? '-',
                        $a->out_time ?? '-',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ==========================================
    // 4. EXAMINATIONS & RESULTS REPORT
    // ==========================================

    public function exams(Request $request)
    {
        $query = ExamResult::with(['student', 'exam', 'centre', 'course']);

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        }

        if ($request->filled('result_status')) {
            $query->where('result_status', $request->result_status);
        }

        $results = $query->latest('id')->paginate(20)->withQueryString();

        $totalAppeared = (clone $query)->count();
        $totalPassed = (clone $query)->whereIn('result_status', ['passed', 'pass'])->count();
        $totalFailed = (clone $query)->whereIn('result_status', ['failed', 'fail'])->count();
        $avgMarks = (float) (clone $query)->avg('percentage');

        $stats = [
            'total'      => $totalAppeared,
            'passed'     => $totalPassed,
            'failed'     => $totalFailed,
            'pass_rate'  => $totalAppeared > 0 ? round(($totalPassed / $totalAppeared) * 100, 1) : 0,
            'avg_marks'  => round($avgMarks, 1),
        ];

        $exams = Exam::orderBy('exam_title')->get();
        $courses = Course::where('status', true)->orderBy('course_name')->get();
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.reports.exams', compact('results', 'stats', 'exams', 'courses', 'centres'));
    }

    public function exportExams(Request $request): StreamedResponse
    {
        $query = ExamResult::with(['student', 'exam', 'centre', 'course']);

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $filename = 'exam_results_report_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Exam Title', 'Student Name', 'Roll No', 'Centre Name', 'Course', 'Marks Obtained', 'Max Marks', 'Percentage', 'Grade', 'Result Status']);

            $query->chunk(250, function ($results) use ($handle) {
                foreach ($results as $r) {
                    fputcsv($handle, [
                        $r->exam ? $r->exam->exam_title : '-',
                        $r->student ? $r->student->full_name : '-',
                        $r->student ? $r->student->roll_no : '-',
                        $r->centre ? $r->centre->centre_name : '-',
                        $r->course ? $r->course->course_name : '-',
                        $r->total_marks_obtained,
                        $r->total_max_marks,
                        $r->percentage . '%',
                        $r->grade ?? '-',
                        ucfirst($r->result_status),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ==========================================
    // 5. EXPENSES & FINANCIAL OUTFLOW REPORT
    // ==========================================

    public function expenses(Request $request)
    {
        $query = Expense::with(['centre', 'recorder']);

        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->latest('expense_date')->latest('id')->paginate(20)->withQueryString();

        $operatingDisbursed = (float) (clone $query)->sum('amount');
        $marketingSpent = (float) MarketingExpense::sum('amount_spent');

        $stats = [
            'operating_total' => $operatingDisbursed,
            'marketing_total' => $marketingSpent,
            'combined_total'  => $operatingDisbursed + $marketingSpent,
            'top_categories'  => Expense::select('category', DB::raw('SUM(amount) as cat_total'))
                ->groupBy('category')
                ->orderByDesc('cat_total')
                ->take(5)
                ->get(),
        ];

        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.reports.expenses', compact('expenses', 'stats', 'centres'));
    }

    public function exportExpenses(Request $request): StreamedResponse
    {
        $query = Expense::with(['centre', 'recorder']);

        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $filename = 'expenses_report_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Expense ID', 'Date', 'Expense Title', 'Category', 'Centre Name', 'Amount', 'Payment Mode', 'Reference No', 'Recorded By']);

            $query->chunk(250, function ($expenses) use ($handle) {
                foreach ($expenses as $e) {
                    fputcsv($handle, [
                        $e->id,
                        $e->expense_date ? $e->expense_date->toDateString() : '-',
                        $e->title,
                        strtoupper($e->category),
                        $e->centre ? $e->centre->centre_name : 'Central Head Office',
                        number_format($e->amount, 2, '.', ''),
                        strtoupper($e->payment_mode),
                        $e->reference_no ?? '-',
                        $e->recorder ? $e->recorder->name : '-',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ==========================================
    // 6. FINANCIAL P&L & NET CASHFLOW REPORT
    // ==========================================

    public function financial(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $centreId = $request->input('centre_id');

        // 1. Gross Collections
        $feeQuery = FeePayment::query()->whereBetween('payment_date', [$dateFrom, $dateTo]);
        if ($centreId) {
            $feeQuery->where('centre_id', $centreId);
        }
        $grossCollections = (float) (clone $feeQuery)->sum('amount');

        // 2. Refunds
        $refundQuery = FeePayment::onlyTrashed()->whereBetween('deleted_at', [
            $dateFrom . ' 00:00:00',
            $dateTo . ' 23:59:59'
        ]);
        if ($centreId) {
            $refundQuery->where('centre_id', $centreId);
        }
        $totalRefunds = (float) (clone $refundQuery)->sum('amount');

        // 3. Net Collections
        $netCollections = max(0.00, $grossCollections - $totalRefunds);

        // 4. Operating Expenses
        $expenseQuery = Expense::query()->whereBetween('expense_date', [$dateFrom, $dateTo]);
        if ($centreId) {
            $expenseQuery->where('centre_id', $centreId);
        }
        $operatingExpenses = (float) (clone $expenseQuery)->sum('amount');

        $topCategories = (clone $expenseQuery)->select('category', DB::raw('SUM(amount) as cat_total'))
            ->groupBy('category')
            ->orderByDesc('cat_total')
            ->get();

        // 5. Marketing Expenses
        $mktQuery = MarketingExpense::query()->whereBetween('start_date', [$dateFrom, $dateTo]);
        if ($centreId) {
            $mktQuery->where('centre_id', $centreId);
        }
        $marketingExpenses = (float) (clone $mktQuery)->sum('amount_spent');

        $topPlatforms = (clone $mktQuery)->select('platform', DB::raw('SUM(amount_spent) as plat_total'))
            ->groupBy('platform')
            ->orderByDesc('plat_total')
            ->get();

        // 6. Total Expenses & Net Profit
        $totalExpenses = $operatingExpenses + $marketingExpenses;
        $netProfit = $netCollections - $totalExpenses;
        $profitMargin = $grossCollections > 0 ? round(($netProfit / $grossCollections) * 100, 1) : 0.0;

        $stats = [
            'gross_collections'   => $grossCollections,
            'total_refunds'       => $totalRefunds,
            'net_collections'     => $netCollections,
            'operating_expenses'  => $operatingExpenses,
            'marketing_expenses'  => $marketingExpenses,
            'total_expenses'      => $totalExpenses,
            'net_profit'          => $netProfit,
            'profit_margin'       => $profitMargin,
            'top_categories'      => $topCategories,
            'top_platforms'       => $topPlatforms,
        ];

        // 7. Date-wise timeline breakdown
        $feesByDate = (clone $feeQuery)->select('payment_date as txn_date', DB::raw('SUM(amount) as collections'))
            ->groupBy('payment_date')
            ->pluck('collections', 'txn_date')
            ->toArray();

        $expensesByDate = (clone $expenseQuery)->select('expense_date as txn_date', DB::raw('SUM(amount) as exp_total'))
            ->groupBy('expense_date')
            ->pluck('exp_total', 'txn_date')
            ->toArray();

        $marketingByDate = (clone $mktQuery)->select('start_date as txn_date', DB::raw('SUM(amount_spent) as mkt_total'))
            ->groupBy('start_date')
            ->pluck('mkt_total', 'txn_date')
            ->toArray();

        $allDates = array_unique(array_merge(
            array_keys($feesByDate),
            array_keys($expensesByDate),
            array_keys($marketingByDate)
        ));
        rsort($allDates);

        $dailyTimeline = [];
        foreach ($allDates as $d) {
            $col = (float) ($feesByDate[$d] ?? 0.0);
            $op = (float) ($expensesByDate[$d] ?? 0.0);
            $mk = (float) ($marketingByDate[$d] ?? 0.0);
            $totExp = $op + $mk;
            $dailyTimeline[] = [
                'date'               => $d,
                'gross_collections'  => $col,
                'operating_expenses' => $op,
                'marketing_expenses' => $mk,
                'total_expenses'     => $totExp,
                'net_surplus'        => $col - $totExp,
            ];
        }

        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.reports.financial', compact('stats', 'dailyTimeline', 'centres', 'dateFrom', 'dateTo'));
    }

    public function exportFinancial(Request $request): StreamedResponse
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $centreId = $request->input('centre_id');

        $feeQuery = FeePayment::query()->whereBetween('payment_date', [$dateFrom, $dateTo]);
        if ($centreId) { $feeQuery->where('centre_id', $centreId); }
        $feesByDate = $feeQuery->select('payment_date as txn_date', DB::raw('SUM(amount) as collections'))
            ->groupBy('payment_date')->pluck('collections', 'txn_date')->toArray();

        $expenseQuery = Expense::query()->whereBetween('expense_date', [$dateFrom, $dateTo]);
        if ($centreId) { $expenseQuery->where('centre_id', $centreId); }
        $expensesByDate = $expenseQuery->select('expense_date as txn_date', DB::raw('SUM(amount) as exp_total'))
            ->groupBy('expense_date')->pluck('exp_total', 'txn_date')->toArray();

        $mktQuery = MarketingExpense::query()->whereBetween('start_date', [$dateFrom, $dateTo]);
        if ($centreId) { $mktQuery->where('centre_id', $centreId); }
        $marketingByDate = $mktQuery->select('start_date as txn_date', DB::raw('SUM(amount_spent) as mkt_total'))
            ->groupBy('start_date')->pluck('mkt_total', 'txn_date')->toArray();

        $allDates = array_unique(array_merge(
            array_keys($feesByDate),
            array_keys($expensesByDate),
            array_keys($marketingByDate)
        ));
        rsort($allDates);

        $filename = 'financial_statement_pnl_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($allDates, $feesByDate, $expensesByDate, $marketingByDate) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Gross Collections (Rs)', 'Operating Expenses (Rs)', 'Marketing Expenses (Rs)', 'Total Expenses (Rs)', 'Net Surplus / Profit (Rs)']);

            $totalCol = 0; $totalOp = 0; $totalMk = 0; $totalNet = 0;
            foreach ($allDates as $d) {
                $col = (float) ($feesByDate[$d] ?? 0.0);
                $op = (float) ($expensesByDate[$d] ?? 0.0);
                $mk = (float) ($marketingByDate[$d] ?? 0.0);
                $tot = $op + $mk;
                $net = $col - $tot;

                $totalCol += $col; $totalOp += $op; $totalMk += $mk; $totalNet += $net;

                fputcsv($handle, [
                    $d,
                    number_format($col, 2, '.', ''),
                    number_format($op, 2, '.', ''),
                    number_format($mk, 2, '.', ''),
                    number_format($tot, 2, '.', ''),
                    number_format($net, 2, '.', ''),
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, [
                'TOTAL',
                number_format($totalCol, 2, '.', ''),
                number_format($totalOp, 2, '.', ''),
                number_format($totalMk, 2, '.', ''),
                number_format($totalOp + $totalMk, 2, '.', ''),
                number_format($totalNet, 2, '.', ''),
            ]);

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}

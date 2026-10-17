<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\AdmissionApplication;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\OnlineTransaction;
use App\Models\Student;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Real-time calculations with separate collected, pending, and failed amounts
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'enrolled')->count();
        $pendingAdmissions = AdmissionApplication::where('status', 'pending')->count();
        
        $collectedFees = (float) FeePayment::sum('amount');
        $totalAdmissionsFee = (float) Admission::sum('net_fee');
        $totalAdmissionsPaid = (float) Admission::sum('total_paid');
        $outstandingDues = max(0.00, $totalAdmissionsFee - $totalAdmissionsPaid);

        $onlineCollected = (float) OnlineTransaction::where('status', 'success')->sum('amount');
        $onlinePending = (float) OnlineTransaction::where('status', 'pending')->sum('amount');
        $onlineFailed = (float) OnlineTransaction::where('status', 'failed')->sum('amount');

        $totalExpenses = (float) Expense::sum('amount');
        $totalCentres = Centre::count();
        $activeCentres = Centre::where('status', 'active')->count();
        $totalCertificates = Certificate::count();
        $openTickets = SupportTicket::whereIn('status', ['open', 'in_progress'])->count();

        // 6-Month Collection Trend
        $monthlyCollections = [];
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $monthKey = $monthDate->format('M Y');
            $months[] = $monthKey;
            $monthSum = (float) FeePayment::whereYear('payment_date', $monthDate->year)
                ->whereMonth('payment_date', $monthDate->month)
                ->sum('amount');
            $monthlyCollections[] = $monthSum;
        }

        $recentPayments = FeePayment::with(['student', 'centre'])->latest()->take(6)->get();
        $recentAdmissions = Admission::with(['student', 'course', 'centre'])->latest()->take(6)->get();
        $recentAuditLogs = AuditLog::with('user')->latest()->take(8)->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'activeStudents',
            'pendingAdmissions',
            'collectedFees',
            'outstandingDues',
            'onlineCollected',
            'onlinePending',
            'onlineFailed',
            'totalExpenses',
            'totalCentres',
            'activeCentres',
            'totalCertificates',
            'openTickets',
            'months',
            'monthlyCollections',
            'recentPayments',
            'recentAdmissions',
            'recentAuditLogs'
        ));
    }
}

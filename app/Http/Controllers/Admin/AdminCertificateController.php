<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\ExamResult;
use App\Models\Marksheet;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminCertificateController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificate::with(['student.centre', 'course', 'result']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('certificate_no', 'like', "%{$s}%")
                    ->orWhereHas('student', function ($sq) use ($s) {
                        $sq->where('full_name', 'like', "%{$s}%")
                            ->orWhere('roll_no', 'like', "%{$s}%")
                            ->orWhere('registration_no', 'like', "%{$s}%");
                    });
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $certificates = $query->latest('issue_date')->paginate(15)->withQueryString();

        $stats = [
            'total_certificates' => Certificate::count(),
            'issued_count'       => Certificate::where('status', 'issued')->count(),
            'revoked_count'      => Certificate::where('status', 'revoked')->count(),
            'total_marksheets'   => Marksheet::count(),
            'pending_results'    => ExamResult::where('result_status', 'passed')->doesntHave('certificate')->count(),
        ];

        $courses = Course::where('status', true)->orderBy('course_name')->get();
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.certificates.index', compact('certificates', 'stats', 'courses', 'centres'));
    }

    public function create(Request $request)
    {
        // Eligible passed exam results without certificate yet
        $eligibleResults = ExamResult::with(['student.centre', 'course'])
            ->where('result_status', 'passed')
            ->doesntHave('certificate')
            ->latest()
            ->get();

        $selectedResult = null;
        if ($request->filled('exam_result_id')) {
            $selectedResult = ExamResult::with(['student.centre', 'course', 'subjectResults.subject'])->find($request->exam_result_id);
        }

        return view('admin.certificates.create', compact('eligibleResults', 'selectedResult'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_result_id'  => 'required|exists:exam_results,id|unique:certificates,exam_result_id',
            'certificate_no'  => 'nullable|string|max:60|unique:certificates,certificate_no',
            'marksheet_no'    => 'nullable|string|max:60|unique:marksheets,marksheet_no',
            'issue_date'      => 'required|date',
        ]);

        $result = ExamResult::with(['student', 'course', 'centre'])->findOrFail($validated['exam_result_id']);

        $year = date('Y', strtotime($validated['issue_date']));

        if (empty($validated['certificate_no'])) {
            $lastId = Certificate::max('id') + 1;
            $validated['certificate_no'] = sprintf('DCE/CERT/%s/%04d', $year, $lastId);
        }

        if (empty($validated['marksheet_no'])) {
            $lastMsId = Marksheet::max('id') + 1;
            $validated['marksheet_no'] = sprintf('DCE/MS/%s/%04d', $year, $lastMsId);
        }

        $certificate = DB::transaction(function () use ($result, $validated) {
            $certHash = hash('sha256', "{$validated['certificate_no']}-{$result->student->roll_no}-" . config('app.key'));
            $msHash = hash('sha256', "{$validated['marksheet_no']}-{$result->student->roll_no}-" . config('app.key'));
            $qrUrl = url('/verify/cert/' . urlencode($validated['certificate_no']));

            // Create Marksheet
            Marksheet::create([
                'exam_result_id' => $result->id,
                'student_id'     => $result->student_id,
                'centre_id'      => $result->centre_id,
                'course_id'      => $result->course_id,
                'marksheet_no'   => $validated['marksheet_no'],
                'issue_date'     => $validated['issue_date'],
                'security_hash'  => $msHash,
                'is_issued'      => true,
            ]);

            // Create Certificate
            $certificate = Certificate::create([
                'exam_result_id' => $result->id,
                'student_id'     => $result->student_id,
                'centre_id'      => $result->centre_id,
                'course_id'      => $result->course_id,
                'certificate_no' => $validated['certificate_no'],
                'issue_date'     => $validated['issue_date'],
                'security_hash'  => $certHash,
                'qr_code_url'    => $qrUrl,
                'status'         => 'issued',
            ]);

            AuditLog::record(
                'certificate_issued',
                $certificate,
                [],
                $certificate->toArray()
            );

            // Dispatch Certificate Issuance Notification
            try {
                \App\Services\NotificationDispatchService::dispatchCertificateIssued($certificate);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Notification dispatch failed for certificate {$certificate->certificate_no}: " . $e->getMessage());
            }

            return $certificate;
        });

        return redirect()->route('admin.certificates.show', $certificate)
            ->with('success', "Certificate #{$certificate->certificate_no} and Marksheet generated successfully.");
    }

    public function show(Certificate $certificate)
    {
        $certificate->load([
            'student.centre',
            'course',
            'centre',
            'result.subjectResults.subject',
        ]);

        return view('admin.certificates.show', compact('certificate'));
    }

    public function print(Certificate $certificate)
    {
        $certificate->load([
            'student.centre',
            'course',
            'centre',
            'result',
        ]);

        return view('admin.certificates.print', compact('certificate'));
    }

    public function marksheet(Certificate $certificate)
    {
        $certificate->load([
            'student.centre',
            'course',
            'centre',
            'result.subjectResults.subject',
        ]);

        $marksheet = Marksheet::where('exam_result_id', $certificate->exam_result_id)->first();

        return view('admin.certificates.marksheet', compact('certificate', 'marksheet'));
    }

    public function revoke(Request $request, Certificate $certificate)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $oldStatus = $certificate->status;
        $certificate->status = 'revoked';
        $certificate->save();

        AuditLog::record(
            'certificate_revoked',
            $certificate,
            ['status' => $oldStatus],
            ['status' => 'revoked', 'reason' => $request->reason ?? 'Administrative order']
        );

        return back()->with('success', "Certificate #{$certificate->certificate_no} has been revoked.");
    }

    public function reissue(Certificate $certificate)
    {
        $oldStatus = $certificate->status;
        $certificate->status = 'issued';
        $certificate->save();

        AuditLog::record(
            'certificate_reissued',
            $certificate,
            ['status' => $oldStatus],
            ['status' => 'issued']
        );

        return back()->with('success', "Certificate #{$certificate->certificate_no} has been reinstated to active status.");
    }
}

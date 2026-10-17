<?php

namespace App\Http\Controllers\Centre;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Batch;
use App\Models\Centre;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CentreBatchController extends Controller
{
    protected function getAuthCentre(): Centre
    {
        $user = Auth::user();
        $centre = $user->centre;

        if (!$centre && $user->isSuperAdmin()) {
            $centre = Centre::where('status', 'active')->first();
        }

        if (!$centre) {
            abort(403, 'No franchise study centre is linked to your user account.');
        }

        return $centre;
    }

    public function index(Request $request)
    {
        $centre = $this->getAuthCentre();
        $query = Batch::where('centre_id', $centre->id)->with(['course'])->withCount(['admissions', 'attendances']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where('batch_name', 'like', "%{$s}%");
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $batches = $query->latest('id')->paginate(15)->withQueryString();
        $courses = Course::where('status', true)->orderBy('course_name')->get();

        $stats = [
            'total' => Batch::where('centre_id', $centre->id)->count(),
            'active' => Batch::where('centre_id', $centre->id)->where('status', 'active')->count(),
            'completed' => Batch::where('centre_id', $centre->id)->where('status', 'completed')->count(),
        ];

        return view('centre.batches.index', compact('centre', 'batches', 'courses', 'stats'));
    }

    public function create()
    {
        $centre = $this->getAuthCentre();
        $courses = Course::where('status', true)->orderBy('course_name')->get();

        return view('centre.batches.create', compact('centre', 'courses'));
    }

    public function store(Request $request)
    {
        $centre = $this->getAuthCentre();

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'batch_name' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'days' => 'required|string|max:100',
            'status' => 'required|in:active,completed,inactive',
        ]);

        $validated['centre_id'] = $centre->id;

        $batch = Batch::create($validated);

        AuditLog::record('centre_batch_created', $batch, null, [
            'batch_name' => $batch->batch_name,
            'centre_id' => $centre->id,
            'course_id' => $batch->course_id,
        ]);

        return redirect()->route('centre.batches.index')
            ->with('success', "Batch [{$batch->batch_name}] created successfully.");
    }

    public function edit(Batch $batch)
    {
        $centre = $this->getAuthCentre();

        if ($batch->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: This batch belongs to another study centre.');
        }

        $courses = Course::where('status', true)->orderBy('course_name')->get();

        return view('centre.batches.edit', compact('centre', 'batch', 'courses'));
    }

    public function update(Request $request, Batch $batch)
    {
        $centre = $this->getAuthCentre();

        if ($batch->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: This batch belongs to another study centre.');
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'batch_name' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'days' => 'required|string|max:100',
            'status' => 'required|in:active,completed,inactive',
        ]);

        $oldValues = $batch->toArray();
        $batch->update($validated);

        AuditLog::record('centre_batch_updated', $batch, $oldValues, $batch->toArray());

        return redirect()->route('centre.batches.index')
            ->with('success', "Batch [{$batch->batch_name}] updated successfully.");
    }

    public function attendanceSheet(Request $request, Batch $batch)
    {
        $centre = $this->getAuthCentre();

        if ($batch->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: This batch belongs to another study centre.');
        }

        $batch->load(['course', 'admissions.student']);
        $attendanceDate = $request->input('date', date('Y-m-d'));

        // Load existing records for this batch on date
        $existingRecords = Attendance::where('batch_id', $batch->id)
            ->whereDate('attendance_date', $attendanceDate)
            ->get()
            ->keyBy('student_id');

        return view('centre.batches.attendance', compact('centre', 'batch', 'attendanceDate', 'existingRecords'));
    }

    public function markAttendance(Request $request, Batch $batch)
    {
        $centre = $this->getAuthCentre();

        if ($batch->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: This batch belongs to another study centre.');
        }

        $validated = $request->validate([
            'attendance_date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,late,leave,holiday',
            'remarks' => 'nullable|array',
        ]);

        $attendanceDate = $validated['attendance_date'];
        $attendanceData = $validated['attendance'];
        $remarksData = $validated['remarks'] ?? [];

        DB::transaction(function () use ($batch, $centre, $attendanceDate, $attendanceData, $remarksData) {
            foreach ($attendanceData as $studentId => $status) {
                Attendance::updateOrCreate(
                    [
                        'batch_id' => $batch->id,
                        'student_id' => $studentId,
                        'attendance_date' => $attendanceDate,
                    ],
                    [
                        'centre_id' => $centre->id,
                        'status' => $status,
                        'remarks' => $remarksData[$studentId] ?? null,
                    ]
                );
            }

            AuditLog::record('centre_attendance_marked', $batch, null, [
                'batch_id' => $batch->id,
                'centre_id' => $centre->id,
                'date' => $attendanceDate,
                'student_count' => count($attendanceData),
            ]);
        });

        return redirect()->route('centre.batches.attendance', [$batch->id, 'date' => $attendanceDate])
            ->with('success', "Attendance marked successfully for " . count($attendanceData) . " students on {$attendanceDate}.");
    }

    public function attendanceReport(Request $request)
    {
        $centre = $this->getAuthCentre();
        $batches = Batch::where('centre_id', $centre->id)->orderBy('batch_name')->get();

        $selectedBatchId = $request->input('batch_id');
        $month = $request->input('month', date('Y-m'));

        $reportData = null;
        if ($selectedBatchId) {
            $batch = Batch::where('centre_id', $centre->id)->findOrFail($selectedBatchId);
            $students = Student::whereHas('admissions', function ($q) use ($selectedBatchId) {
                $q->where('batch_id', $selectedBatchId);
            })->orderBy('full_name')->get();

            $attendances = Attendance::where('batch_id', $selectedBatchId)
                ->where('attendance_date', 'like', "{$month}%")
                ->get()
                ->groupBy('student_id');

            $reportData = [
                'batch' => $batch,
                'students' => $students,
                'attendances' => $attendances,
            ];
        }

        return view('centre.batches.attendance_report', compact('centre', 'batches', 'selectedBatchId', 'month', 'reportData'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\Course;
use App\Models\CourseSubject;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamResultSubject;
use App\Models\Question;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminExamController extends Controller
{
    // ==========================================
    // 1. EXAM SCHEDULES & SESSIONS
    // ==========================================

    public function index(Request $request)
    {
        $query = Exam::with(['course'])->withCount('results');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('exam_title', 'like', "%{$s}%")
                    ->orWhere('exam_code', 'like', "%{$s}%")
                    ->orWhere('session_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $exams = $query->orderBy('exam_date', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total'     => Exam::count(),
            'scheduled' => Exam::where('status', 'scheduled')->count(),
            'evaluated' => Exam::where('status', 'evaluated')->count(),
            'published' => Exam::where('status', 'published')->count(),
        ];

        $courses = Course::where('status', true)->orderBy('course_name')->get();

        return view('admin.exams.index', compact('exams', 'stats', 'courses'));
    }

    public function create()
    {
        $courses = Course::where('status', true)->orderBy('course_name')->get();
        return view('admin.exams.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id'    => 'required|exists:courses,id',
            'exam_title'   => 'required|string|max:191',
            'exam_code'    => 'nullable|string|max:50|unique:exams,exam_code',
            'session_name' => 'required|string|max:50',
            'exam_date'    => 'required|date',
            'status'       => 'required|in:scheduled,ongoing,evaluated,published',
        ]);

        if (empty($validated['exam_code'])) {
            $course = Course::findOrFail($validated['course_id']);
            $validated['exam_code'] = 'EXM-' . strtoupper($course->course_code ?? 'DCE') . '-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        }

        $exam = DB::transaction(function () use ($validated) {
            $exam = Exam::create($validated);

            AuditLog::record(
                'exam_created',
                $exam,
                [],
                $exam->toArray()
            );

            return $exam;
        });

        return redirect()->route('admin.exams.show', $exam)
            ->with('success', "Examination session '{$exam->exam_title}' created successfully.");
    }

    public function show(Exam $exam)
    {
        $exam->load([
            'course.subjects',
            'results.student.centre',
            'results.subjectResults.subject',
            'admitCards.student',
        ]);

        $results = $exam->results;
        $totalCandidates = $results->count();
        $passedCount = $results->where('result_status', 'passed')->count();
        $failedCount = $results->where('result_status', 'failed')->count();
        $withheldCount = $results->where('result_status', 'withheld')->count();
        $avgScore = $totalCandidates > 0 ? round($results->avg('percentage'), 2) : 0;

        return view('admin.exams.show', compact('exam', 'totalCandidates', 'passedCount', 'failedCount', 'withheldCount', 'avgScore'));
    }

    public function edit(Exam $exam)
    {
        $courses = Course::where('status', true)->orderBy('course_name')->get();
        return view('admin.exams.edit', compact('exam', 'courses'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'course_id'    => 'required|exists:courses,id',
            'exam_title'   => 'required|string|max:191',
            'exam_code'    => 'required|string|max:50|unique:exams,exam_code,' . $exam->id,
            'session_name' => 'required|string|max:50',
            'exam_date'    => 'required|date',
            'status'       => 'required|in:scheduled,ongoing,evaluated,published',
        ]);

        $oldData = $exam->toArray();

        DB::transaction(function () use ($exam, $validated, $oldData) {
            $exam->update($validated);

            AuditLog::record(
                'exam_updated',
                $exam,
                $oldData,
                $exam->toArray()
            );
        });

        return redirect()->route('admin.exams.show', $exam)
            ->with('success', "Examination session '{$exam->exam_title}' updated successfully.");
    }

    public function destroy(Exam $exam)
    {
        if ($exam->results()->count() > 0) {
            return back()->with('error', 'Cannot delete this exam session because student results have already been recorded.');
        }

        DB::transaction(function () use ($exam) {
            AuditLog::record(
                'exam_deleted',
                $exam,
                $exam->toArray(),
                []
            );

            $exam->delete();
        });

        return redirect()->route('admin.exams.index')
            ->with('success', 'Examination session deleted successfully.');
    }

    // ==========================================
    // 2. QUESTION BANK MANAGEMENT
    // ==========================================

    public function questions(Request $request)
    {
        $query = Question::with(['course', 'subject']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where('question_text', 'like', "%{$s}%");
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('status')) {
            $query->where('status', (bool) $request->status);
        }

        $questions = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total'  => Question::count(),
            'easy'   => Question::where('difficulty', 'easy')->count(),
            'medium' => Question::where('difficulty', 'medium')->count(),
            'hard'   => Question::where('difficulty', 'hard')->count(),
        ];

        $courses = Course::where('status', true)->orderBy('course_name')->get();

        return view('admin.exams.questions.index', compact('questions', 'stats', 'courses'));
    }

    public function createQuestion()
    {
        $courses = Course::with('subjects')->where('status', true)->orderBy('course_name')->get();
        return view('admin.exams.questions.create', compact('courses'));
    }

    public function storeQuestion(Request $request)
    {
        $validated = $request->validate([
            'course_id'         => 'required|exists:courses,id',
            'course_subject_id' => 'nullable|exists:course_subjects,id',
            'question_text'     => 'required|string',
            'option_a'          => 'required|string|max:255',
            'option_b'          => 'required|string|max:255',
            'option_c'          => 'required|string|max:255',
            'option_d'          => 'required|string|max:255',
            'correct_option'    => 'required|in:a,b,c,d',
            'marks'             => 'required|integer|min:1|max:100',
            'difficulty'        => 'required|in:easy,medium,hard',
            'status'            => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status');

        $question = Question::create($validated);

        AuditLog::record(
            'question_created',
            $question,
            [],
            $question->toArray()
        );

        return redirect()->route('admin.exams.questions')
            ->with('success', 'Question added to question bank successfully.');
    }

    public function editQuestion(Question $question)
    {
        $courses = Course::with('subjects')->where('status', true)->orderBy('course_name')->get();
        return view('admin.exams.questions.edit', compact('question', 'courses'));
    }

    public function updateQuestion(Request $request, Question $question)
    {
        $validated = $request->validate([
            'course_id'         => 'required|exists:courses,id',
            'course_subject_id' => 'nullable|exists:course_subjects,id',
            'question_text'     => 'required|string',
            'option_a'          => 'required|string|max:255',
            'option_b'          => 'required|string|max:255',
            'option_c'          => 'required|string|max:255',
            'option_d'          => 'required|string|max:255',
            'correct_option'    => 'required|in:a,b,c,d',
            'marks'             => 'required|integer|min:1|max:100',
            'difficulty'        => 'required|in:easy,medium,hard',
            'status'            => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status');

        $old = $question->toArray();
        $question->update($validated);

        AuditLog::record(
            'question_updated',
            $question,
            $old,
            $question->toArray()
        );

        return redirect()->route('admin.exams.questions')
            ->with('success', 'Question updated successfully.');
    }

    public function destroyQuestion(Question $question)
    {
        AuditLog::record(
            'question_deleted',
            $question,
            $question->toArray(),
            []
        );

        $question->delete();

        return redirect()->route('admin.exams.questions')
            ->with('success', 'Question deleted from question bank.');
    }

    // ==========================================
    // 3. EXAM RESULTS & MARKS ENTRY
    // ==========================================

    public function results(Request $request)
    {
        $query = ExamResult::with(['exam', 'student', 'centre', 'course']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->whereHas('student', function ($sq) use ($s) {
                $sq->where('full_name', 'like', "%{$s}%")
                    ->orWhere('roll_no', 'like', "%{$s}%")
                    ->orWhere('registration_no', 'like', "%{$s}%");
            });
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

        $results = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'    => ExamResult::count(),
            'passed'   => ExamResult::where('result_status', 'passed')->count(),
            'failed'   => ExamResult::where('result_status', 'failed')->count(),
            'withheld' => ExamResult::where('result_status', 'withheld')->count(),
        ];

        $courses = Course::where('status', true)->orderBy('course_name')->get();
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.exams.results.index', compact('results', 'stats', 'courses', 'centres'));
    }

    public function createResult(Request $request)
    {
        $exams = Exam::with('course.subjects')->orderBy('exam_date', 'desc')->get();
        $students = Student::with(['admissions.course', 'centre'])
            ->whereIn('status', ['enrolled', 'ongoing'])
            ->orderBy('full_name')
            ->get();

        $selectedExam = null;
        $selectedStudent = null;

        if ($request->filled('exam_id')) {
            $selectedExam = Exam::with('course.subjects')->find($request->exam_id);
        }

        if ($request->filled('student_id')) {
            $selectedStudent = Student::with(['admissions.course.subjects', 'centre'])->find($request->student_id);
        }

        return view('admin.exams.results.create', compact('exams', 'students', 'selectedExam', 'selectedStudent'));
    }

    public function storeResult(Request $request)
    {
        $validated = $request->validate([
            'student_id'     => 'required|exists:students,id',
            'course_id'      => 'required|exists:courses,id',
            'exam_id'        => 'nullable|exists:exams,id',
            'subjects'       => 'required|array|min:1',
            'subjects.*.id'  => 'required|exists:course_subjects,id',
            'subjects.*.theory_obtained'    => 'nullable|numeric|min:0',
            'subjects.*.practical_obtained' => 'nullable|numeric|min:0',
            'subjects.*.viva_obtained'      => 'nullable|numeric|min:0',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $course = Course::with('subjects')->findOrFail($validated['course_id']);
        $centreId = $student->centre_id ?? Centre::value('id');

        $result = DB::transaction(function () use ($validated, $student, $course, $centreId) {
            $totalObtained = 0.0;
            $totalMax = 0.0;
            $allPassed = true;

            $subjectEntries = [];

            foreach ($validated['subjects'] as $subInput) {
                $sub = CourseSubject::findOrFail($subInput['id']);
                $th = (float) ($subInput['theory_obtained'] ?? 0);
                $pr = (float) ($subInput['practical_obtained'] ?? 0);
                $va = (float) ($subInput['viva_obtained'] ?? 0);
                $subTot = $th + $pr + $va;
                $maxMarks = (float) ($sub->total_max ?: 100);

                $minPass = (float) ($sub->min_pass_marks ?: 40);
                $isPassed = ($subTot >= $minPass);
                if (!$isPassed) {
                    $allPassed = false;
                }

                $subPct = $maxMarks > 0 ? ($subTot / $maxMarks) * 100 : 0;
                $subGrade = ExamResult::calculateGrade($subPct);

                $totalObtained += $subTot;
                $totalMax += $maxMarks;

                $subjectEntries[] = [
                    'course_subject_id'  => $sub->id,
                    'theory_obtained'    => $th,
                    'practical_obtained' => $pr,
                    'viva_obtained'      => $va,
                    'total_obtained'     => $subTot,
                    'max_marks'          => $maxMarks,
                    'grade'              => $subGrade,
                    'is_passed'          => $isPassed,
                ];
            }

            $percentage = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 2) : 0.0;
            $grade = ExamResult::calculateGrade($percentage);
            $resultStatus = ($allPassed && $percentage >= 40.0) ? 'passed' : 'failed';

            $result = ExamResult::create([
                'exam_id'              => $validated['exam_id'] ?? null,
                'student_id'           => $student->id,
                'centre_id'            => $centreId,
                'course_id'            => $course->id,
                'total_marks_obtained' => $totalObtained,
                'total_max_marks'      => $totalMax,
                'percentage'           => $percentage,
                'grade'                => $grade,
                'result_status'        => $resultStatus,
                'approved_by'          => Auth::id(),
                'approved_at'          => now(),
            ]);

            foreach ($subjectEntries as $entry) {
                $entry['exam_result_id'] = $result->id;
                ExamResultSubject::create($entry);
            }

            AuditLog::record(
                'exam_result_recorded',
                $result,
                [],
                $result->toArray()
            );

            return $result;
        });

        return redirect()->route('admin.exams.results.show', $result)
            ->with('success', "Student exam results recorded and evaluated successfully.");
    }

    public function showResult(ExamResult $result)
    {
        $result->load([
            'student.centre',
            'course',
            'exam',
            'centre',
            'approver',
            'subjectResults.subject',
            'marksheet',
            'certificate',
        ]);

        return view('admin.exams.results.show', compact('result'));
    }

    public function updateResultStatus(Request $request, ExamResult $result)
    {
        $validated = $request->validate([
            'result_status' => 'required|in:passed,failed,withheld',
        ]);

        $oldStatus = $result->result_status;
        $result->result_status = $validated['result_status'];
        if ($validated['result_status'] !== 'withheld') {
            $result->approved_by = Auth::id();
            $result->approved_at = now();
        }
        $result->save();

        AuditLog::record(
            'result_status_updated',
            $result,
            ['result_status' => $oldStatus],
            ['result_status' => $result->result_status]
        );

        return back()->with('success', "Result status successfully updated to " . strtoupper($result->result_status) . ".");
    }

    // Helper AJAX endpoint to retrieve subjects of a course
    public function getCourseSubjects(Course $course)
    {
        $subjects = $course->subjects()->get();
        return response()->json($subjects);
    }
}

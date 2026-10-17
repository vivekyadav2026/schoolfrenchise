<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Marksheet;
use App\Models\Student;
use App\Models\StudyMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $student = $user->student ?? Student::where('user_id', $user->id)->first();

        if (!$student) {
            abort(403, 'No student record found linked to your account. Please contact your study centre.');
        }

        $admission = $student->admissions()->with(['course', 'batch'])->latest()->first();
        $fees = $student->fees()->orderBy('installment_no')->get();
        $payments = $student->payments()->latest()->get();
        $attendances = Attendance::where('student_id', $student->id)->latest()->take(10)->get();
        $certificate = Certificate::where('student_id', $student->id)->where('status', 'issued')->first();
        $marksheet = Marksheet::where('student_id', $student->id)->where('is_issued', true)->first();

        $studyMaterials = collect();
        if ($admission && $admission->course_id) {
            $studyMaterials = StudyMaterial::where(function ($q) use ($student) {
                $q->whereNull('centre_id')->orWhere('centre_id', $student->centre_id);
            })->where('course_id', $admission->course_id)->where('status', true)->get();
        }

        return view('student.dashboard', compact('student', 'admission', 'fees', 'payments', 'attendances', 'certificate', 'marksheet', 'studyMaterials'));
    }
}

<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Centre;
use App\Models\Course;
use App\Models\CourseSubject;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamResultSubject;
use App\Models\Question;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminExamModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $course;
    protected $subject1;
    protected $subject2;
    protected $centre;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'superadmin@drishticomputer.com'],
            [
                'name'      => 'Super Administrator',
                'password'  => bcrypt('SuperSecretAdmin@2026'),
                'role'      => 'super_admin',
                'is_active' => true,
            ]
        );

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Admin']);
        $this->admin->roles()->syncWithoutDetaching([$superAdminRole->id]);

        $this->centre = Centre::first() ?? Centre::create([
            'centre_code'   => 'DCE-TEST-EXM',
            'centre_name'   => 'Exam Test Centre',
            'director_name' => 'Test Director',
            'email'         => 'examcentre@drishti.com',
            'mobile'        => '9888877771',
            'address'       => 'Test Address',
            'state_id'      => 1,
            'district_id'   => 1,
            'status'        => 'active',
        ]);

        $this->course = Course::firstOrCreate(
            ['course_code' => 'TEST-EXM-01'],
            [
                'course_name'        => 'Test Examination Course',
                'slug'               => 'test-examination-course',
                'duration'           => '6 Months',
                'eligibility'        => '10th Standard',
                'default_course_fee' => 5000.00,
                'status'             => true,
            ]
        );

        $this->subject1 = CourseSubject::firstOrCreate(
            ['course_id' => $this->course->id, 'subject_code' => 'EXM-SUB-01'],
            [
                'subject_name'   => 'Module Paper 1',
                'theory_max'     => 70,
                'practical_max'  => 20,
                'viva_max'       => 10,
                'total_max'      => 100,
                'min_pass_marks' => 40,
                'sort_order'     => 1,
            ]
        );

        $this->subject2 = CourseSubject::firstOrCreate(
            ['course_id' => $this->course->id, 'subject_code' => 'EXM-SUB-02'],
            [
                'subject_name'   => 'Module Paper 2',
                'theory_max'     => 70,
                'practical_max'  => 20,
                'viva_max'       => 10,
                'total_max'      => 100,
                'min_pass_marks' => 40,
                'sort_order'     => 2,
            ]
        );

        $this->student = Student::firstOrCreate(
            ['roll_no' => 'ROLL-TEST-EXM-01'],
            [
                'centre_id'       => $this->centre->id,
                'registration_no' => 'REG-TEST-EXM-01',
                'full_name'       => 'Aakash Verma',
                'father_name'     => 'Rajesh Verma',
                'mother_name'     => 'Sunita Verma',
                'dob'             => '2001-05-15',
                'gender'          => 'male',
                'mobile'          => '9888877772',
                'state_id'        => 1,
                'district_id'     => 1,
                'city'            => 'Noida',
                'address'         => 'Sector 62, Noida',
                'pincode'         => '201301',
                'status'          => 'enrolled',
            ]
        );

        Admission::firstOrCreate(
            ['student_id' => $this->student->id, 'course_id' => $this->course->id],
            [
                'centre_id'      => $this->centre->id,
                'admission_date' => now()->toDateString(),
                'status'         => 'active',
                'course_fee'     => 5000.00,
                'net_fee'        => 5000.00,
            ]
        );
    }

    public function test_admin_can_view_exams_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.exams.index'));
        $response->assertStatus(200);
        $response->assertSee('Examinations & Assessment', false);
    }

    public function test_admin_can_create_exam_schedule()
    {
        $payload = [
            'course_id'    => $this->course->id,
            'exam_title'   => 'Annual Examination Cycle 2026',
            'exam_code'    => 'EXM-ANNUAL-2026',
            'session_name' => '2025-2026',
            'exam_date'    => '2026-11-20',
            'status'       => 'scheduled',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.exams.store'), $payload);
        $response->assertRedirect();

        $this->assertDatabaseHas('exams', [
            'exam_code'  => 'EXM-ANNUAL-2026',
            'exam_title' => 'Annual Examination Cycle 2026',
        ]);
    }

    public function test_admin_can_view_and_update_exam()
    {
        $exam = Exam::create([
            'course_id'    => $this->course->id,
            'exam_title'   => 'Term Assessment',
            'exam_code'    => 'EXM-TERM-' . uniqid(),
            'session_name' => '2026',
            'exam_date'    => '2026-12-01',
            'status'       => 'scheduled',
        ]);

        $showResponse = $this->actingAs($this->admin)->get(route('admin.exams.show', $exam));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($exam->exam_title);

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.exams.update', $exam), [
            'course_id'    => $this->course->id,
            'exam_title'   => 'Updated Term Assessment',
            'exam_code'    => $exam->exam_code,
            'session_name' => '2026-2027',
            'exam_date'    => '2026-12-15',
            'status'       => 'ongoing',
        ]);

        $updateResponse->assertRedirect(route('admin.exams.show', $exam));
        $this->assertDatabaseHas('exams', [
            'id'         => $exam->id,
            'exam_title' => 'Updated Term Assessment',
            'status'     => 'ongoing',
        ]);
    }

    public function test_admin_can_manage_question_bank()
    {
        $indexResponse = $this->actingAs($this->admin)->get(route('admin.exams.questions'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Question Bank');

        // Store question
        $storeResponse = $this->actingAs($this->admin)->post(route('admin.exams.questions.store'), [
            'course_id'         => $this->course->id,
            'course_subject_id' => $this->subject1->id,
            'question_text'     => 'What is the full form of RAM?',
            'option_a'          => 'Random Access Memory',
            'option_b'          => 'Read Access Memory',
            'option_c'          => 'Rapid Action Module',
            'option_d'          => 'Run Application Mode',
            'correct_option'    => 'a',
            'marks'             => 2,
            'difficulty'        => 'easy',
            'status'            => 1,
        ]);

        $storeResponse->assertRedirect(route('admin.exams.questions'));
        $this->assertDatabaseHas('questions', [
            'question_text'  => 'What is the full form of RAM?',
            'correct_option' => 'a',
        ]);

        $question = Question::where('question_text', 'What is the full form of RAM?')->first();

        // Update question
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.exams.questions.update', $question), [
            'course_id'         => $this->course->id,
            'course_subject_id' => $this->subject1->id,
            'question_text'     => 'What is the full form of RAM in Computing?',
            'option_a'          => 'Random Access Memory',
            'option_b'          => 'Read Access Memory',
            'option_c'          => 'Rapid Action Module',
            'option_d'          => 'Run Application Mode',
            'correct_option'    => 'a',
            'marks'             => 2,
            'difficulty'        => 'easy',
            'status'            => 1,
        ]);

        $updateResponse->assertRedirect(route('admin.exams.questions'));
        $this->assertDatabaseHas('questions', [
            'id'            => $question->id,
            'question_text' => 'What is the full form of RAM in Computing?',
        ]);

        // Delete question
        $delResponse = $this->actingAs($this->admin)->delete(route('admin.exams.questions.destroy', $question));
        $delResponse->assertRedirect(route('admin.exams.questions'));
        $this->assertSoftDeleted('questions', ['id' => $question->id]);
    }

    public function test_admin_can_enter_and_evaluate_student_marks()
    {
        $exam = Exam::create([
            'course_id'    => $this->course->id,
            'exam_title'   => 'Final Evaluation',
            'exam_code'    => 'EXM-FIN-' . uniqid(),
            'session_name' => '2026',
            'exam_date'    => '2026-10-15',
            'status'       => 'ongoing',
        ]);

        $payload = [
            'student_id' => $this->student->id,
            'course_id'  => $this->course->id,
            'exam_id'    => $exam->id,
            'subjects'   => [
                [
                    'id'                 => $this->subject1->id,
                    'theory_obtained'    => 60,
                    'practical_obtained' => 18,
                    'viva_obtained'      => 8, // Total: 86/100
                ],
                [
                    'id'                 => $this->subject2->id,
                    'theory_obtained'    => 55,
                    'practical_obtained' => 17,
                    'viva_obtained'      => 8, // Total: 80/100
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.exams.results.store'), $payload);
        $response->assertRedirect();

        $this->assertDatabaseHas('exam_results', [
            'student_id'           => $this->student->id,
            'course_id'            => $this->course->id,
            'total_marks_obtained' => 166.00,
            'total_max_marks'      => 200.00,
            'percentage'           => 83.00,
            'grade'                => 'A',
            'result_status'        => 'passed',
        ]);

        $result = ExamResult::where('student_id', $this->student->id)->latest()->first();

        // Check show result
        $showRes = $this->actingAs($this->admin)->get(route('admin.exams.results.show', $result));
        $showRes->assertStatus(200);
        $showRes->assertSee('83.00%');
        $showRes->assertSee('Module Paper 1');

        // Status update
        $statusRes = $this->actingAs($this->admin)->post(route('admin.exams.results.status', $result), [
            'result_status' => 'withheld',
        ]);
        $statusRes->assertRedirect();

        $this->assertDatabaseHas('exam_results', [
            'id'            => $result->id,
            'result_status' => 'withheld',
        ]);
    }
}

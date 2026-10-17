<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Centre;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseSubject;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamResultSubject;
use App\Models\Marksheet;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminCertificateModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected Course $course;
    protected CourseSubject $subject;
    protected Centre $centre;
    protected Student $student;
    protected ExamResult $examResult;

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
            'centre_code'   => 'DCE-TEST-CRT',
            'centre_name'   => 'Certificate Test Centre',
            'director_name' => 'Director CRT',
            'email'         => 'crtcentre@drishti.com',
            'mobile'        => '9888877774',
            'address'       => 'Test Address',
            'state_id'      => 1,
            'district_id'   => 1,
            'status'        => 'active',
        ]);

        $this->course = Course::firstOrCreate(
            ['course_code' => 'CRT-COURSE-01'],
            [
                'course_name'        => 'Certified Hardware & Network Course',
                'slug'               => 'certified-hardware-network-course',
                'duration'           => '6 Months',
                'eligibility'        => '12th Standard',
                'default_course_fee' => 6000.00,
                'status'             => true,
            ]
        );

        $this->subject = CourseSubject::firstOrCreate(
            ['course_id' => $this->course->id, 'subject_code' => 'CRT-SUB-01'],
            [
                'subject_name'   => 'Networking Fundamentals',
                'theory_max'     => 70,
                'practical_max'  => 20,
                'viva_max'       => 10,
                'total_max'      => 100,
                'min_pass_marks' => 40,
                'sort_order'     => 1,
            ]
        );

        $this->student = Student::firstOrCreate(
            ['roll_no' => 'ROLL-TEST-CRT-01'],
            [
                'centre_id'       => $this->centre->id,
                'registration_no' => 'REG-TEST-CRT-01',
                'full_name'       => 'Rohan Singhania',
                'father_name'     => 'Kunal Singhania',
                'mother_name'     => 'Anita Singhania',
                'dob'             => '2000-08-20',
                'gender'          => 'male',
                'mobile'          => '9888877775',
                'state_id'        => 1,
                'district_id'     => 1,
                'city'            => 'Noida',
                'address'         => 'Noida Sector 18',
                'pincode'         => '201301',
                'status'          => 'completed',
            ]
        );

        Admission::firstOrCreate(
            ['student_id' => $this->student->id, 'course_id' => $this->course->id],
            [
                'centre_id'      => $this->centre->id,
                'admission_date' => now()->subMonths(6)->toDateString(),
                'status'         => 'completed',
                'course_fee'     => 6000.00,
                'net_fee'        => 6000.00,
            ]
        );

        $this->examResult = ExamResult::create([
            'student_id'           => $this->student->id,
            'centre_id'            => $this->centre->id,
            'course_id'            => $this->course->id,
            'total_marks_obtained' => 88.00,
            'total_max_marks'      => 100.00,
            'percentage'           => 88.00,
            'grade'                => 'A+',
            'result_status'        => 'passed',
            'approved_by'          => $this->admin->id,
            'approved_at'          => now(),
        ]);

        ExamResultSubject::create([
            'exam_result_id'     => $this->examResult->id,
            'course_subject_id'  => $this->subject->id,
            'theory_obtained'    => 60.00,
            'practical_obtained' => 19.00,
            'viva_obtained'      => 9.00,
            'total_obtained'     => 88.00,
            'max_marks'          => 100.00,
            'grade'              => 'A+',
            'is_passed'          => true,
        ]);
    }

    public function test_admin_can_view_certificates_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.certificates.index'));
        $response->assertStatus(200);
        $response->assertSee('Certificates & Marksheets', false);
    }

    public function test_admin_can_view_create_certificate_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.certificates.create'));
        $response->assertStatus(200);
        $response->assertSee('Issue Official Certificate & Marksheet', false);
        $response->assertSee('Rohan Singhania');
    }

    public function test_admin_can_issue_certificate_and_marksheet(): void
    {
        $certNo = 'DCE/CERT/2026/TEST999';
        $msNo = 'DCE/MS/2026/TEST999';

        $payload = [
            'exam_result_id' => $this->examResult->id,
            'certificate_no' => $certNo,
            'marksheet_no'   => $msNo,
            'issue_date'     => now()->toDateString(),
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.certificates.store'), $payload);
        $response->assertRedirect();

        $this->assertDatabaseHas('certificates', [
            'exam_result_id' => $this->examResult->id,
            'student_id'     => $this->student->id,
            'certificate_no' => $certNo,
            'status'         => 'issued',
        ]);

        $this->assertDatabaseHas('marksheets', [
            'exam_result_id' => $this->examResult->id,
            'student_id'     => $this->student->id,
            'marksheet_no'   => $msNo,
            'is_issued'      => 1,
        ]);
    }

    public function test_admin_can_view_and_print_certificate(): void
    {
        $certificate = Certificate::create([
            'exam_result_id' => $this->examResult->id,
            'student_id'     => $this->student->id,
            'centre_id'      => $this->centre->id,
            'course_id'      => $this->course->id,
            'certificate_no' => 'DCE/CERT/2026/PRNT1',
            'issue_date'     => now()->toDateString(),
            'security_hash'  => hash('sha256', 'DCE/CERT/2026/PRNT1'),
            'qr_code_url'    => url('/verify/cert/DCE-CERT-2026-PRNT1'),
            'status'         => 'issued',
        ]);

        $showResponse = $this->actingAs($this->admin)->get(route('admin.certificates.show', $certificate));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($certificate->certificate_no);
        $showResponse->assertSee('Rohan Singhania');

        $printResponse = $this->actingAs($this->admin)->get(route('admin.certificates.print', $certificate));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('Certificate of Proficiency');
        $printResponse->assertSee('TECH ERA COMPUTER');
    }

    public function test_admin_can_view_marksheet(): void
    {
        $certificate = Certificate::create([
            'exam_result_id' => $this->examResult->id,
            'student_id'     => $this->student->id,
            'centre_id'      => $this->centre->id,
            'course_id'      => $this->course->id,
            'certificate_no' => 'DCE/CERT/2026/MS1',
            'issue_date'     => now()->toDateString(),
            'security_hash'  => hash('sha256', 'DCE/CERT/2026/MS1'),
            'qr_code_url'    => url('/verify/cert/DCE-CERT-2026-MS1'),
            'status'         => 'issued',
        ]);

        $marksheet = Marksheet::create([
            'exam_result_id' => $this->examResult->id,
            'student_id'     => $this->student->id,
            'centre_id'      => $this->centre->id,
            'course_id'      => $this->course->id,
            'marksheet_no'   => 'DCE/MS/2026/MS1',
            'issue_date'     => now()->toDateString(),
            'security_hash'  => hash('sha256', 'DCE/MS/2026/MS1'),
            'is_issued'      => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.certificates.marksheet', $certificate));
        $response->assertStatus(200);
        $response->assertSee('Statement of Marks');
        $response->assertSee('Networking Fundamentals');
    }

    public function test_admin_can_revoke_and_reissue_certificate(): void
    {
        $certificate = Certificate::create([
            'exam_result_id' => $this->examResult->id,
            'student_id'     => $this->student->id,
            'centre_id'      => $this->centre->id,
            'course_id'      => $this->course->id,
            'certificate_no' => 'DCE/CERT/2026/RVK1',
            'issue_date'     => now()->toDateString(),
            'security_hash'  => hash('sha256', 'DCE/CERT/2026/RVK1'),
            'qr_code_url'    => url('/verify/cert/DCE-CERT-2026-RVK1'),
            'status'         => 'issued',
        ]);

        // Revoke
        $revokeRes = $this->actingAs($this->admin)->post(route('admin.certificates.revoke', $certificate), [
            'reason' => 'Disciplinary audit discrepancy',
        ]);
        $revokeRes->assertRedirect();

        $certificate->refresh();
        $this->assertEquals('revoked', $certificate->status);

        // Reissue
        $reissueRes = $this->actingAs($this->admin)->post(route('admin.certificates.reissue', $certificate));
        $reissueRes->assertRedirect();

        $certificate->refresh();
        $this->assertEquals('issued', $certificate->status);
    }
}

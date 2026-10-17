<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Attendance;
use App\Models\Batch;
use App\Models\Centre;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminReportModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected Centre $centre;
    protected Course $course;
    protected Batch $batch;
    protected Student $student;

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
            'centre_code'      => 'DCE-TEST-REP01',
            'centre_name'      => 'Reports Test Centre',
            'director_name'    => 'Director Reports',
            'email'            => 'reportcentre@drishti.com',
            'mobile'           => '9888877776',
            'state_id'         => 1,
            'district_id'      => 1,
            'city'             => 'Delhi',
            'address'          => 'Reports Address',
            'pincode'          => '110001',
            'affiliation_date' => now()->toDateString(),
            'status'           => 'active',
        ]);

        $this->course = Course::first() ?? Course::create([
            'course_code' => 'ADCA-TEST',
            'title'       => 'Advanced Diploma in Computer Applications',
            'slug'        => 'adca-test',
            'duration'    => '12 Months',
            'fee'         => 12000.00,
            'status'      => true,
        ]);

        $this->batch = Batch::firstOrCreate(
            ['batch_name' => 'Reports Test Batch 2026'],
            [
                'centre_id'   => $this->centre->id,
                'course_id'   => $this->course->id,
                'start_time'  => '10:00:00',
                'end_time'    => '12:00:00',
                'status'      => 'active',
            ]
        );

        $this->student = Student::first() ?? Student::create([
            'centre_id'       => $this->centre->id,
            'roll_no'         => 'DCE-REP-001',
            'registration_no' => 'REG-REP-001',
            'full_name'       => 'Ananya Verma',
            'father_name'     => 'Sunil Verma',
            'gender'          => 'female',
            'dob'             => '2004-05-15',
            'mobile'          => '9876543299',
            'state_id'        => 1,
            'district_id'     => 1,
            'city'            => 'Noida',
            'address'         => 'Test Address',
            'pincode'         => '201301',
            'admission_date'  => now()->toDateString(),
            'status'          => 'active',
        ]);

        Admission::firstOrCreate(
            ['student_id' => $this->student->id, 'course_id' => $this->course->id],
            [
                'centre_id'       => $this->centre->id,
                'batch_id'        => $this->batch->id,
                'admission_date'  => now()->toDateString(),
                'course_fee'      => 12000.00,
                'discount_amount' => 0.00,
                'net_fee'         => 12000.00,
                'total_paid'      => 4000.00,
                'status'          => 'active',
            ]
        );
    }

    public function test_guest_cannot_access_reports()
    {
        $response = $this->get(route('admin.reports.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_reports_executive_hub()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Institutional Reports & Business Intelligence', false);
        $response->assertSee('Student & Admissions', false);
        $response->assertSee('Fees & Collections', false);
        $response->assertSee('Attendance Analytics');
    }

    public function test_admin_can_view_students_report()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.students'));

        $response->assertStatus(200);
        $response->assertSee('Student & Admissions Audit Report', false);
        $response->assertSee($this->student->full_name);
    }

    public function test_admin_can_export_students_report_csv()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.students.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_view_fees_financial_report()
    {
        $studentFee = StudentFee::firstOrCreate(
            ['student_id' => $this->student->id],
            [
                'admission_id'   => $this->student->admissions->first()->id,
                'centre_id'      => $this->centre->id,
                'installment_no' => 1,
                'title'          => 'Tuition Fee Term 1',
                'due_date'       => now()->toDateString(),
                'amount'         => 4000.00,
                'paid_amount'    => 4000.00,
                'status'         => 'paid',
            ]
        );

        FeePayment::firstOrCreate(
            ['receipt_no' => 'RCP-REP-001'],
            [
                'student_fee_id'  => $studentFee->id,
                'admission_id'    => $this->student->admissions->first()->id,
                'student_id'      => $this->student->id,
                'centre_id'       => $this->centre->id,
                'amount'          => 4000.00,
                'payment_date'    => now()->toDateString(),
                'payment_mode'    => 'upi',
                'transaction_ref' => 'UTR-REP-4000',
            ]
        );

        $response = $this->actingAs($this->admin)->get(route('admin.reports.fees'));

        $response->assertStatus(200);
        $response->assertSee('Fees & Financial Collection Report', false);
        $response->assertSee('4,000.00');
        $response->assertSee('RCP-REP-001');
    }

    public function test_admin_can_export_fees_report_csv()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.fees.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_view_attendance_report()
    {
        Attendance::create([
            'centre_id'       => $this->centre->id,
            'batch_id'        => $this->batch->id,
            'student_id'      => $this->student->id,
            'attendance_date' => now()->toDateString(),
            'status'          => 'present',
            'in_time'         => '10:05:00',
            'out_time'        => '12:00:00',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.attendance'));

        $response->assertStatus(200);
        $response->assertSee('Classroom Attendance & Engagement Report', false);
        $response->assertSee($this->student->full_name);
        $response->assertSee('Present');
    }

    public function test_admin_can_export_attendance_report_csv()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.attendance.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_view_exams_report()
    {
        $exam = Exam::firstOrCreate(
            ['exam_code' => 'EXAM-REP-01'],
            [
                'exam_title'   => 'ADCA Term Assessment 2026',
                'course_id'    => $this->course->id,
                'session_name' => '2025-2026',
                'exam_date'    => now()->toDateString(),
                'status'       => 'published',
            ]
        );

        ExamResult::firstOrCreate(
            ['exam_id' => $exam->id, 'student_id' => $this->student->id],
            [
                'centre_id'            => $this->centre->id,
                'course_id'            => $this->course->id,
                'total_marks_obtained' => 88.00,
                'total_max_marks'      => 100.00,
                'percentage'           => 88.00,
                'grade'                => 'A+',
                'result_status'        => 'passed',
            ]
        );

        $response = $this->actingAs($this->admin)->get(route('admin.reports.exams'));

        $response->assertStatus(200);
        $response->assertSee('Examination Performance & Results Analytics', false);
        $response->assertSee($this->student->full_name);
        $response->assertSee('88');
        $response->assertSee('Passed');
    }

    public function test_admin_can_export_exams_report_csv()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.exams.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_view_expenses_report()
    {
        Expense::create([
            'centre_id'    => $this->centre->id,
            'category'     => 'hardware',
            'title'        => 'Report Test Lab Monitors',
            'amount'       => 15000.00,
            'expense_date' => now()->toDateString(),
            'payment_mode' => 'bank_transfer',
            'reference_no' => 'NEFT-REP-15K',
            'recorded_by'  => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.expenses'));

        $response->assertStatus(200);
        $response->assertSee('Operating & Marketing Expense Report', false);
        $response->assertSee('Report Test Lab Monitors');
        $response->assertSee('15,000.00');
    }

    public function test_admin_can_export_expenses_report_csv()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.expenses.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Centre;
use App\Models\CentreWalletTransaction;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\FeePayment;
use App\Models\PaymentReceipt;
use App\Models\Role;
use App\Models\State;
use App\Models\Student;
use App\Models\SystemNotification;
use App\Models\User;
use App\Services\NotificationDispatchService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NotificationDispatchServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected User $studentUser;
    protected Student $student;
    protected Centre $centre;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'student'], ['display_name' => 'Student']);
        $state = State::first() ?? State::create(['name' => 'Bihar', 'status' => true]);

        $this->centre = Centre::firstOrCreate(
            ['centre_code' => 'DCE-NOTIF-01'],
            [
                'centre_name'      => 'Drishti Notification Centre',
                'director_name'    => 'Director Sharma',
                'email'            => 'notif_centre_' . uniqid() . '@drishti.com',
                'mobile'           => '98765' . rand(10000, 99999),
                'address'          => 'Main Road',
                'city'             => 'Patna',
                'pincode'          => '800001',
                'lab_capacity'     => 20,
                'affiliation_date' => now()->toDateString(),
                'state_id'         => $state->id,
                'district_id'      => 1,
                'status'           => 'active',
            ]
        );

        $this->studentUser = User::create([
            'name' => 'Kunal Kashyap',
            'email' => 'kunal_' . uniqid() . '@student.com',
            'mobile' => '9876543299',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'is_active' => true,
        ]);

        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'centre_id' => $this->centre->id,
            'registration_no' => 'DCE/2026/NOTIF/001',
            'roll_no' => 'DCE26' . rand(10000, 99999),
            'full_name' => 'Kunal Kashyap',
            'father_name' => 'Sunil Kashyap',
            'dob' => '2004-05-15',
            'gender' => 'male',
            'mobile' => '9876543299',
            'state_id' => $state->id,
            'district_id' => 1,
            'city' => 'Patna',
            'address' => 'Boring Road',
            'pincode' => '800001',
            'status' => 'enrolled',
        ]);

        $this->course = Course::create([
            'course_name' => 'Certificate in Financial Accounting',
            'course_code' => 'CFA-' . rand(100, 999),
            'slug' => 'cfa-' . uniqid(),
            'duration' => '3 Months',
            'duration_months' => 3,
            'eligibility' => '10th Pass',
            'default_course_fee' => 4500.00,
            'status' => true,
        ]);
    }

    public function test_dispatch_admission_approval_creates_system_notification_and_dispatches_message(): void
    {
        $result = NotificationDispatchService::dispatchAdmissionApproval($this->student);

        $this->assertEquals('admission_approval', $result['type']);
        $this->assertEquals($this->student->mobile, $result['recipient']);
        $this->assertStringContainsString($this->student->roll_no, $result['message']);
        $this->assertStringContainsString($this->student->registration_no, $result['message']);

        $this->assertDatabaseHas('system_notifications', [
            'user_id' => $this->studentUser->id,
            'type' => 'system',
        ]);
    }

    public function test_dispatch_fee_receipt_creates_notification_and_receipt_alert(): void
    {
        $admission = Admission::create([
            'student_id' => $this->student->id,
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'admission_date' => now()->toDateString(),
            'course_fee' => 4500.00,
            'discount_amount' => 0.00,
            'net_fee' => 4500.00,
            'total_paid' => 2000.00,
            'status' => 'active',
        ]);

        $payment = FeePayment::create([
            'admission_id' => $admission->id,
            'student_id' => $this->student->id,
            'centre_id' => $this->centre->id,
            'receipt_no' => 'RCP-2026-TEST-001',
            'payment_date' => now()->toDateString(),
            'amount' => 2000.00,
            'payment_mode' => 'upi',
            'transaction_ref' => 'UPI-9871029381',
        ]);

        $receipt = PaymentReceipt::create([
            'fee_payment_id' => $payment->id,
            'receipt_no' => 'RCP-2026-TEST-001',
            'student_id' => $this->student->id,
            'admission_id' => $admission->id,
            'centre_id' => $this->centre->id,
            'amount' => 2000.00,
            'payment_mode' => 'upi',
            'receipt_date' => now()->toDateString(),
            'qr_token' => 'test_token',
        ]);

        $result = NotificationDispatchService::dispatchFeeReceipt($receipt);

        $this->assertEquals('fee_receipt', $result['type']);
        $this->assertStringContainsString('RCP-2026-TEST-001', $result['message']);
        $this->assertStringContainsString('2,000.00', $result['message']);

        $this->assertDatabaseHas('system_notifications', [
            'user_id' => $this->studentUser->id,
            'type' => 'fee',
        ]);
    }

    public function test_dispatch_admit_card_and_certificate_notifications(): void
    {
        // Admit Card
        $exam = Exam::create([
            'course_id' => $this->course->id,
            'exam_title' => 'Winter Session Examination 2026',
            'exam_code' => 'EXM-W26-' . rand(100, 999),
            'session_name' => 'Winter 2026',
            'exam_date' => now()->addDays(7)->toDateString(),
            'status' => 'scheduled',
        ]);

        $admitResult = NotificationDispatchService::dispatchAdmitCard($exam, $this->student);
        $this->assertEquals('admit_card', $admitResult['type']);
        $this->assertStringContainsString($exam->exam_title, $admitResult['message']);

        $this->assertDatabaseHas('system_notifications', [
            'user_id' => $this->studentUser->id,
            'type' => 'exam',
        ]);

        // Certificate
        $examResult = ExamResult::create([
            'exam_id' => $exam->id,
            'student_id' => $this->student->id,
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'total_max_marks' => 100,
            'total_marks_obtained' => 88,
            'percentage' => 88.0,
            'grade' => 'A+',
            'result_status' => 'passed',
        ]);

        $certificate = Certificate::create([
            'exam_result_id' => $examResult->id,
            'student_id' => $this->student->id,
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'certificate_no' => 'DCE/CERT/2026/' . rand(1000, 9999),
            'issue_date' => now()->toDateString(),
            'security_hash' => 'test_hash',
            'status' => 'issued',
        ]);

        $certResult = NotificationDispatchService::dispatchCertificateIssued($certificate);
        $this->assertEquals('certificate_issued', $certResult['type']);
        $this->assertStringContainsString($certificate->certificate_no, $certResult['message']);

        $this->assertDatabaseHas('system_notifications', [
            'user_id' => $this->studentUser->id,
            'type' => 'certificate',
        ]);
    }

    public function test_dispatch_wallet_alert_notifies_centre_director(): void
    {
        $centreUser = User::create([
            'name' => 'Centre Director X',
            'email' => 'director_x_' . uniqid() . '@drishti.com',
            'mobile' => '9876543200',
            'password' => Hash::make('password123'),
            'role' => 'centre',
            'is_active' => true,
        ]);
        $this->centre->update(['user_id' => $centreUser->id]);

        $txn = CentreWalletTransaction::create([
            'centre_id' => $this->centre->id,
            'type' => 'credit',
            'amount' => 5000.00,
            'balance_after' => 15000.00,
            'reference_no' => 'UTR-TEST-12345',
            'description' => 'Test wallet recharge',
            'status' => 'approved',
        ]);

        $alertResult = NotificationDispatchService::dispatchWalletAlert($txn);
        $this->assertEquals('wallet_alert', $alertResult['type']);
        $this->assertStringContainsString('5,000.00', $alertResult['message']);
        $this->assertStringContainsString('15,000.00', $alertResult['message']);

        $this->assertDatabaseHas('system_notifications', [
            'user_id' => $centreUser->id,
            'type' => 'alert',
        ]);
    }
}

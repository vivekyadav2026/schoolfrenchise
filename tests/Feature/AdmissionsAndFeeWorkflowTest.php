<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\AdmissionApplication;
use App\Models\Centre;
use App\Models\Course;
use App\Models\Enquiry;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\OnlineTransaction;
use App\Models\PaymentReceipt;
use App\Models\Role;
use App\Models\State;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\User;
use App\Services\PaymentGatewayService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdmissionsAndFeeWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected Centre $centre;
    protected Course $course;
    protected State $state;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

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

        $this->state = State::first() ?? State::create(['name' => 'Bihar', 'status' => true]);

        $this->centre = Centre::first() ?? Centre::create([
            'centre_code'   => 'DCE-ADM-01',
            'centre_name'   => 'Admission Workflow Centre',
            'director_name' => 'Dr. Director',
            'email'         => 'admcentre@drishti.com',
            'mobile'        => '9876543210',
            'address'       => 'Test Station Road',
            'state_id'      => $this->state->id,
            'district_id'   => 1,
            'status'        => 'active',
        ]);

        $this->course = Course::firstOrCreate(
            ['course_code' => 'ADCA-TEST-WF'],
            [
                'course_name'        => 'Advanced Diploma in Computer Applications Test',
                'slug'               => 'adca-test-wf',
                'duration'           => '12 Months',
                'eligibility'        => '12th Standard',
                'default_course_fee' => 12000.00,
                'course_fee'         => 12000.00,
                'status'             => true,
            ]
        );
    }

    /**
     * Test 1: Public admission enquiry submission.
     */
    public function test_public_admission_enquiry_submission(): void
    {
        $response = $this->post(route('enquiry.admission.submit'), [
            'name'        => 'Aman Sharma',
            'mobile'      => '9123456789',
            'email'       => 'aman.sharma@example.com',
            'state_id'    => $this->state->id,
            'district_id' => 1,
            'course_id'   => $this->course->id,
            'subject'     => 'Direct Course Admission Enquiry',
            'message'     => 'Interested in the evening batch schedule.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enquiries', [
            'name'   => 'Aman Sharma',
            'mobile' => '9123456789',
            'type'   => 'admission_enquiry',
        ]);
    }

    /**
     * Test 2: Public online application submission with document upload validation and unique application number.
     */
    public function test_public_admission_application_submission_with_documents(): void
    {
        $photo = UploadedFile::fake()->image('passport_photo.jpg', 300, 300);
        $idProof = UploadedFile::fake()->create('aadhaar_card.pdf', 800, 'application/pdf');

        $response = $this->post(route('admission.apply.submit'), [
            'centre_id'             => $this->centre->id,
            'course_id'             => $this->course->id,
            'applicant_name'        => 'Rohan Kumar Gupta',
            'father_name'           => 'Suresh Gupta',
            'mother_name'           => 'Meena Gupta',
            'dob'                   => '2003-05-15',
            'gender'                => 'male',
            'mobile'                => '9811887766',
            'email'                 => 'rohan.gupta@example.com',
            'state_id'              => $this->state->id,
            'district_id'           => 1,
            'city'                  => 'Patna',
            'address'               => 'Flat 302, Ganga Heights',
            'pincode'               => '800001',
            'highest_qualification' => '12th Pass',
            'photo'                 => $photo,
            'id_proof'              => $idProof,
        ]);

        $response->assertRedirect();

        $application = AdmissionApplication::where('mobile', '9811887766')->first();
        $this->assertNotNull($application);
        $this->assertStringStartsWith('APP-', $application->application_no);
        $this->assertEquals('pending', $application->status);
        $this->assertNotNull($application->photo_path);
        $this->assertNotNull($application->id_proof_doc);
    }

    /**
     * Test 3: Public admission status tracking by application number.
     */
    public function test_public_admission_status_tracking(): void
    {
        $application = AdmissionApplication::create([
            'application_no' => 'APP-2026-TEST01',
            'centre_id'      => $this->centre->id,
            'course_id'      => $this->course->id,
            'applicant_name' => 'Pooja Verma',
            'father_name'    => 'Dinesh Verma',
            'dob'            => '2004-08-20',
            'gender'         => 'female',
            'mobile'         => '9877001122',
            'state_id'       => $this->state->id,
            'district_id'    => 1,
            'city'           => 'Patna',
            'address'        => 'Boring Road',
            'pincode'        => '800001',
            'status'         => 'pending',
        ]);

        $response = $this->get(route('admission.status', ['application_no' => $application->application_no]));
        $response->assertStatus(200);
        $response->assertSee('APP-2026-TEST01');
        $response->assertSee('Pooja Verma');
        $response->assertSee('Application Under Scrutiny');
    }

    /**
     * Test 4: Admin application review and rejection with reason.
     */
    public function test_admin_application_review_and_rejection(): void
    {
        $application = AdmissionApplication::create([
            'application_no' => 'APP-2026-REJ01',
            'centre_id'      => $this->centre->id,
            'course_id'      => $this->course->id,
            'applicant_name' => 'Invalid Applicant',
            'father_name'    => 'Father',
            'dob'            => '2005-01-01',
            'gender'         => 'male',
            'mobile'         => '9998887771',
            'state_id'       => $this->state->id,
            'district_id'    => 1,
            'city'           => 'Patna',
            'address'        => 'Test Address',
            'pincode'        => '800001',
            'status'         => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.admissions.application.reject', $application->id), [
            'rejection_reason' => 'Marksheet scan is illegible. Please re-apply with a high-resolution document.',
        ]);

        $response->assertRedirect();
        $application->refresh();
        $this->assertEquals('rejected', $application->status);
        $this->assertStringContainsString('illegible', $application->rejection_reason);
    }

    /**
     * Test 5: Admin application approval and automatic student registration with roll number and fee schedule.
     */
    public function test_admin_application_approval_and_student_registration(): void
    {
        $application = AdmissionApplication::create([
            'application_no' => 'APP-2026-APP01',
            'centre_id'      => $this->centre->id,
            'course_id'      => $this->course->id,
            'applicant_name' => 'Deepak Rawat',
            'father_name'    => 'Kishore Rawat',
            'dob'            => '2001-11-12',
            'gender'         => 'male',
            'mobile'         => '9876501234',
            'state_id'       => $this->state->id,
            'district_id'    => 1,
            'city'           => 'Patna',
            'address'        => 'Kankarbagh Colony',
            'pincode'        => '800020',
            'status'         => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.admissions.application.approve', $application->id), [
            'course_fee'      => 12000.00,
            'discount_amount' => 2000.00,
        ]);

        $response->assertRedirect();

        $application->refresh();
        $this->assertEquals('approved', $application->status);
        $this->assertNotNull($application->converted_student_id);

        $student = Student::find($application->converted_student_id);
        $this->assertNotNull($student);
        $this->assertNotNull($student->roll_no);
        $this->assertNotNull($student->registration_no);

        // Check Admission created
        $admission = Admission::where('student_id', $student->id)->first();
        $this->assertNotNull($admission);
        $this->assertEquals(10000.00, (float) $admission->net_fee);
        $this->assertEquals(2000.00, (float) $admission->discount_amount);

        // Check Student Fees created
        $fees = StudentFee::where('admission_id', $admission->id)->get();
        $this->assertNotEmpty($fees);
    }

    /**
     * Test 6: Official admission form dossier view generation.
     */
    public function test_official_admission_form_dossier_generation(): void
    {
        $student = Student::create([
            'centre_id'       => $this->centre->id,
            'registration_no' => 'REG-WF-DOSSIER-01',
            'roll_no'         => 'ROLL-WF-DOSSIER-01',
            'full_name'       => 'Priya Singh',
            'father_name'     => 'Vikram Singh',
            'dob'             => '2003-04-18',
            'gender'          => 'female',
            'mobile'          => '9800112233',
            'state_id'        => $this->state->id,
            'district_id'     => 1,
            'city'            => 'Patna',
            'address'         => 'Bailey Road',
            'pincode'         => '800001',
            'status'          => 'enrolled',
        ]);

        $admission = Admission::create([
            'student_id'      => $student->id,
            'centre_id'       => $this->centre->id,
            'course_id'       => $this->course->id,
            'admission_date'  => now(),
            'course_fee'      => 12000.00,
            'discount_amount' => 1000.00,
            'net_fee'         => 11000.00,
            'total_paid'      => 0.00,
            'status'          => 'active',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.admissions.print', $admission->id));
        $response->assertStatus(200);
        $response->assertSee('OFFICIAL STUDENT ADMISSION DOSSIER');
        $response->assertSee('Priya Singh');
        $response->assertSee('ROLL-WF-DOSSIER-01');
        $response->assertSee('11,000.00');
    }

    /**
     * Test 7: Fee structure configuration CRUD.
     */
    public function test_fee_structure_configuration_crud(): void
    {
        // 1. Create Structure
        $response = $this->actingAs($this->admin)->post(route('admin.fees.structures.store'), [
            'course_id'          => $this->course->id,
            'name'               => 'Quarterly 4-Term Plan',
            'total_fee'          => 12000.00,
            'admission_fee'      => 3000.00,
            'tuition_fee'        => 8000.00,
            'exam_fee'           => 1000.00,
            'certificate_fee'    => 0.00,
            'installments_count' => 4,
            'terms'              => 'Quarterly payment due at start of each quarter.',
            'is_active'          => 1,
        ]);

        $response->assertRedirect(route('admin.fees.structures'));

        $structure = FeeStructure::where('name', 'Quarterly 4-Term Plan')->first();
        $this->assertNotNull($structure);
        $this->assertEquals(4, $structure->installments_count);
        $this->assertEquals(12000.00, (float) $structure->total_fee);

        // 2. Update Structure
        $updateResp = $this->actingAs($this->admin)->put(route('admin.fees.structures.update', $structure->id), [
            'course_id'          => $this->course->id,
            'name'               => 'Quarterly 4-Term Plan (Updated)',
            'total_fee'          => 13000.00,
            'installments_count' => 4,
            'is_active'          => 1,
        ]);
        $updateResp->assertRedirect();
        $structure->refresh();
        $this->assertEquals('Quarterly 4-Term Plan (Updated)', $structure->name);

        // 3. Delete Structure
        $delResp = $this->actingAs($this->admin)->delete(route('admin.fees.structures.destroy', $structure->id));
        $delResp->assertRedirect();
        $this->assertDatabaseMissing('fee_structures', ['id' => $structure->id]);
    }

    /**
     * Test 8: Custom installment schedule, due dates, concessions and approved adjustments assignment.
     */
    public function test_custom_installment_schedule_and_concessions_assignment(): void
    {
        $student = Student::create([
            'centre_id'       => $this->centre->id,
            'registration_no' => 'REG-WF-SCHED-01',
            'roll_no'         => 'ROLL-WF-SCHED-01',
            'full_name'       => 'Sameer Sen',
            'father_name'     => 'Kunal Sen',
            'dob'             => '2003-09-10',
            'gender'          => 'male',
            'mobile'          => '9833445566',
            'state_id'        => $this->state->id,
            'district_id'     => 1,
            'city'            => 'Patna',
            'address'         => 'Station Road',
            'pincode'         => '800001',
            'status'          => 'enrolled',
        ]);

        $admission = Admission::create([
            'student_id'      => $student->id,
            'centre_id'       => $this->centre->id,
            'course_id'       => $this->course->id,
            'admission_date'  => now(),
            'course_fee'      => 12000.00,
            'discount_amount' => 0.00,
            'net_fee'         => 12000.00,
            'total_paid'      => 0.00,
            'status'          => 'active',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.fees.schedule.store', $admission->id), [
            'course_fee'        => 12000.00,
            'concession_amount' => 2000.00,
            'adjustment_reason' => 'State Level IT Olympiad Merit Concession',
            'installments'      => [
                [
                    'title'    => 'Down Payment & Admission Term',
                    'amount'   => 5000.00,
                    'due_date' => now()->toDateString(),
                ],
                [
                    'title'    => 'Second Instalment Milestone',
                    'amount'   => 3000.00,
                    'due_date' => now()->addMonth()->toDateString(),
                ],
                [
                    'title'    => 'Final Instalment Milestone',
                    'amount'   => 2000.00,
                    'due_date' => now()->addMonths(2)->toDateString(),
                ],
            ],
        ]);

        $response->assertRedirect();

        $admission->refresh();
        $this->assertEquals(10000.00, (float) $admission->net_fee);
        $this->assertEquals(2000.00, (float) $admission->discount_amount);
        $this->assertStringContainsString('Olympiad', $admission->remarks);

        $installments = StudentFee::where('admission_id', $admission->id)->get();
        $this->assertCount(3, $installments);
        $this->assertEquals(10000.00, (float) $installments->sum('amount'));
    }

    /**
     * Test 9: Authorized offline payment entry with receipt number generation.
     */
    public function test_authorized_offline_fee_collection_and_receipt_issuance(): void
    {
        $student = Student::create([
            'centre_id'       => $this->centre->id,
            'registration_no' => 'REG-WF-COLLECT-01',
            'roll_no'         => 'ROLL-WF-COLLECT-01',
            'full_name'       => 'Nisha Patel',
            'father_name'     => 'Ramesh Patel',
            'dob'             => '2004-02-14',
            'gender'          => 'female',
            'mobile'          => '9822334455',
            'state_id'        => $this->state->id,
            'district_id'     => 1,
            'city'            => 'Patna',
            'address'         => 'Test St',
            'pincode'         => '800001',
            'status'          => 'enrolled',
        ]);

        $admission = Admission::create([
            'student_id'      => $student->id,
            'centre_id'       => $this->centre->id,
            'course_id'       => $this->course->id,
            'admission_date'  => now(),
            'course_fee'      => 8000.00,
            'discount_amount' => 0.00,
            'net_fee'         => 8000.00,
            'total_paid'      => 0.00,
            'status'          => 'active',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.fees.store'), [
            'admission_id'    => $admission->id,
            'amount'          => 4000.00,
            'payment_mode'    => 'cash',
            'transaction_ref' => 'OFFLINE-CHQ-102938',
            'notes'           => 'First installment collected at centre reception',
        ]);

        $response->assertRedirect();

        $admission->refresh();
        $this->assertEquals(4000.00, (float) $admission->total_paid);

        $payment = FeePayment::where('admission_id', $admission->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(4000.00, (float) $payment->amount);
        $this->assertStringStartsWith('RCP-', $payment->receipt_no);

        $receipt = PaymentReceipt::where('fee_payment_id', $payment->id)->first();
        $this->assertNotNull($receipt);
        $this->assertNotNull($receipt->qr_token);
    }

    /**
     * Test 10: Server-side order creation for online payments.
     */
    public function test_online_payment_server_side_order_creation(): void
    {
        $orderData = PaymentGatewayService::createOrder(
            1500.00,
            'Rahul Verma',
            '9811002233',
            'rahul@example.com',
            'Exam Registration Fee'
        );

        $this->assertNotNull($orderData['order_id']);
        $this->assertNotNull($orderData['transaction_id']);
        $this->assertEquals(1500.00, $orderData['amount']);
        $this->assertEquals(150000, $orderData['amount_paise']);

        $transaction = OnlineTransaction::where('order_id', $orderData['order_id'])->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('pending', $transaction->status);
    }

    /**
     * Test 11: Authoritative cryptographic signature verification for online payments.
     */
    public function test_online_payment_authoritative_signature_verification_success(): void
    {
        $orderData = PaymentGatewayService::createOrder(
            2500.00,
            'Anjali Das',
            '9876540011',
            'anjali@example.com',
            'Certification Fee'
        );

        $orderId = $orderData['order_id'];
        $paymentId = 'pay_test_' . rand(100000, 999999);
        $validSignature = PaymentGatewayService::generateSignature($orderId, $paymentId);

        $transaction = PaymentGatewayService::reconcilePayment($orderId, $paymentId, $validSignature, ['gateway' => 'razorpay']);

        $this->assertEquals('success', $transaction->status);
        $this->assertEquals($paymentId, $transaction->gateway_payment_id);
        $this->assertEquals($validSignature, $transaction->gateway_signature);
    }

    /**
     * Test 12: Online payment signature mismatch prevents unauthorized marking as success.
     */
    public function test_online_payment_signature_mismatch_failure(): void
    {
        $orderData = PaymentGatewayService::createOrder(
            3000.00,
            'Tamper Tester',
            '9800001111',
            'tamper@example.com',
            'Tampered Test Fee'
        );

        $orderId = $orderData['order_id'];
        $paymentId = 'pay_tamper_123';
        $tamperedSignature = 'FAKE_SIGNATURE_NOT_MATCHING_HMAC';

        $transaction = PaymentGatewayService::reconcilePayment($orderId, $paymentId, $tamperedSignature, ['fake' => true]);

        $this->assertEquals('failed', $transaction->status);
        $this->assertStringContainsString('failed', $transaction->error_message);
    }

    /**
     * Test 13: Idempotency prevents duplicate payment credit and duplicate receipts.
     */
    public function test_payment_idempotency_prevents_duplicate_credit(): void
    {
        $student = Student::create([
            'centre_id'       => $this->centre->id,
            'registration_no' => 'REG-IDEM-01',
            'roll_no'         => 'ROLL-IDEM-01',
            'full_name'       => 'Idempotent Student',
            'father_name'     => 'Kalyan Student',
            'dob'             => '2002-07-19',
            'gender'          => 'male',
            'mobile'          => '9800998877',
            'state_id'        => $this->state->id,
            'district_id'     => 1,
            'city'            => 'Patna',
            'address'         => 'Test',
            'pincode'         => '800001',
            'status'          => 'enrolled',
        ]);

        $admission = Admission::create([
            'student_id'      => $student->id,
            'centre_id'       => $this->centre->id,
            'course_id'       => $this->course->id,
            'admission_date'  => now(),
            'course_fee'      => 5000.00,
            'discount_amount' => 0.00,
            'net_fee'         => 5000.00,
            'total_paid'      => 0.00,
            'status'          => 'active',
        ]);

        $orderData = PaymentGatewayService::createOrder(
            2000.00,
            $student->full_name,
            $student->mobile,
            'idem@example.com',
            'Course Fee',
            $student->roll_no,
            $admission->id,
            $student->id
        );

        $orderId = $orderData['order_id'];
        $paymentId = 'pay_idem_998877';
        $signature = PaymentGatewayService::generateSignature($orderId, $paymentId);

        // First callback
        $txn1 = PaymentGatewayService::reconcilePayment($orderId, $paymentId, $signature);
        $this->assertEquals('success', $txn1->status);

        $admission->refresh();
        $this->assertEquals(2000.00, (float) $admission->total_paid);
        $receiptCount1 = PaymentReceipt::where('admission_id', $admission->id)->count();
        $this->assertEquals(1, $receiptCount1);

        // Duplicate callback (simulating duplicate webhook / multiple clicks)
        $txn2 = PaymentGatewayService::reconcilePayment($orderId, $paymentId, $signature);
        $this->assertEquals('success', $txn2->status);

        $admission->refresh();
        // Total paid MUST NOT become 4000
        $this->assertEquals(2000.00, (float) $admission->total_paid);
        $receiptCount2 = PaymentReceipt::where('admission_id', $admission->id)->count();
        $this->assertEquals(1, $receiptCount2);
    }

    /**
     * Test 14: Payment gateway webhook handling with cryptographic header verification.
     */
    public function test_webhook_verification_and_event_handling(): void
    {
        $orderData = PaymentGatewayService::createOrder(
            1000.00,
            'Webhook Payer',
            '9876509876',
            'webhook@example.com',
            'Admission Fee'
        );

        $orderId = $orderData['order_id'];
        $paymentId = 'pay_wh_' . rand(10000, 99999);

        $rawPayload = json_encode([
            'event'   => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id'       => $paymentId,
                        'order_id' => $orderId,
                        'amount'   => 100000,
                        'status'   => 'captured',
                    ]
                ]
            ]
        ]);

        // Generate webhook signature using webhook secret
        $webhookSecret = PaymentGatewayService::getWebhookSecret();
        $signatureHeader = hash_hmac('sha256', $rawPayload, $webhookSecret);

        $response = $this->postJson(route('payment.webhook'), json_decode($rawPayload, true), [
            'X-Razorpay-Signature' => $signatureHeader,
        ]);

        $response->assertStatus(200);

        $transaction = OnlineTransaction::where('order_id', $orderId)->first();
        $this->assertEquals('success', $transaction->status);
    }

    /**
     * Test 15: Live payment mode cannot activate without merchant credentials.
     */
    public function test_live_mode_cannot_activate_without_merchant_credentials(): void
    {
        Config::set('services.payment.mode', 'live');
        Config::set('services.payment.razorpay.key_id', null);
        Config::set('services.payment.razorpay.key_secret', null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Live payment gateway cannot be activated');

        PaymentGatewayService::getKeySecret();
    }
}

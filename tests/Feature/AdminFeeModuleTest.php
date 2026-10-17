<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Centre;
use App\Models\Course;
use App\Models\FeePayment;
use App\Models\PaymentReceipt;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminFeeModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected Course $course;
    protected Centre $centre;
    protected Student $student;
    protected Admission $admission;

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
            'centre_code'   => 'DCE-TEST-FEE',
            'centre_name'   => 'Fee Test Centre',
            'director_name' => 'Director Fee',
            'email'         => 'feecentre@drishti.com',
            'mobile'        => '9888877776',
            'address'       => 'Test Address',
            'state_id'      => 1,
            'district_id'   => 1,
            'status'        => 'active',
        ]);

        $this->course = Course::firstOrCreate(
            ['course_code' => 'FEE-COURSE-01'],
            [
                'course_name'        => 'Financial Accounting Test Course',
                'slug'               => 'financial-accounting-test-course',
                'duration'           => '6 Months',
                'eligibility'        => '12th Commerce',
                'default_course_fee' => 8000.00,
                'status'             => true,
            ]
        );

        $this->student = Student::firstOrCreate(
            ['roll_no' => 'ROLL-TEST-FEE-01'],
            [
                'centre_id'       => $this->centre->id,
                'registration_no' => 'REG-TEST-FEE-01',
                'full_name'       => 'Aditya Kapoor',
                'father_name'     => 'Ramesh Kapoor',
                'mother_name'     => 'Rekha Kapoor',
                'dob'             => '2002-03-10',
                'gender'          => 'male',
                'mobile'          => '9888877777',
                'state_id'        => 1,
                'district_id'     => 1,
                'city'            => 'Noida',
                'address'         => 'Noida Sector 62',
                'pincode'         => '201301',
                'status'          => 'enrolled',
            ]
        );

        $this->admission = Admission::firstOrCreate(
            ['student_id' => $this->student->id, 'course_id' => $this->course->id],
            [
                'centre_id'      => $this->centre->id,
                'admission_date' => now()->toDateString(),
                'status'         => 'active',
                'course_fee'     => 8000.00,
                'net_fee'        => 8000.00,
            ]
        );
    }

    public function test_admin_can_view_fees_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.fees.index'));
        $response->assertStatus(200);
        $response->assertSee('Fees & Payment Collections', false);
    }

    public function test_admin_can_view_dues_roster(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.fees.dues'));
        $response->assertStatus(200);
        $response->assertSee('Outstanding Fee Dues Roster', false);
        $response->assertSee('Aditya Kapoor');
    }

    public function test_admin_can_view_create_payment_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.fees.create', ['admission_id' => $this->admission->id]));
        $response->assertStatus(200);
        $response->assertSee('Receive Student Fee Payment', false);
        $response->assertSee('Aditya Kapoor');
    }

    public function test_admin_can_collect_fee_payment_and_generate_receipt(): void
    {
        $collectAmount = 3500.00;

        $payload = [
            'admission_id'    => $this->admission->id,
            'amount'          => $collectAmount,
            'payment_mode'    => 'upi',
            'transaction_ref' => 'UPI-TXN-123456789',
            'notes'           => '1st Installment Payment',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.fees.store'), $payload);
        $response->assertRedirect();

        $this->assertDatabaseHas('fee_payments', [
            'admission_id'    => $this->admission->id,
            'student_id'      => $this->student->id,
            'amount'          => $collectAmount,
            'payment_mode'    => 'upi',
            'transaction_ref' => 'UPI-TXN-123456789',
        ]);

        $this->admission->refresh();
        $this->assertEquals($collectAmount, (float) $this->admission->total_paid);

        $receipt = PaymentReceipt::where('admission_id', $this->admission->id)->latest()->first();
        $this->assertNotNull($receipt);
        $this->assertEquals($collectAmount, (float) $receipt->amount);

        // View receipt
        $receiptResponse = $this->actingAs($this->admin)->get(route('admin.fees.receipt', $receipt));
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee($receipt->receipt_no);
        $receiptResponse->assertSee('Aditya Kapoor');
    }

    public function test_collect_fee_fails_if_amount_exceeds_remaining_balance(): void
    {
        $due = (float) $this->admission->net_fee - (float) $this->admission->total_paid;
        $excessiveAmount = $due + 5000;

        $payload = [
            'admission_id' => $this->admission->id,
            'amount'       => $excessiveAmount,
            'payment_mode' => 'cash',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.fees.store'), $payload);
        $response->assertSessionHas('error');
    }

    public function test_admin_can_refund_fee_payment(): void
    {
        // First collect a payment
        $payment = FeePayment::create([
            'admission_id'    => $this->admission->id,
            'student_id'      => $this->student->id,
            'centre_id'       => $this->centre->id,
            'receipt_no'      => 'RCP-REFUND-TEST',
            'payment_date'    => now()->toDateString(),
            'amount'          => 2000.00,
            'payment_mode'    => 'cash',
            'collected_by'    => $this->admin->id,
        ]);

        $this->admission->update(['total_paid' => 2000.00]);

        $refundResponse = $this->actingAs($this->admin)->post(route('admin.fees.refund', $payment), [
            'reason' => 'Candidate duplicate payment refund',
        ]);

        $refundResponse->assertRedirect(route('admin.fees.index'));

        $this->assertSoftDeleted('fee_payments', [
            'id' => $payment->id,
        ]);

        $this->admission->refresh();
        $this->assertEquals(0.00, (float) $this->admission->total_paid);
    }
}

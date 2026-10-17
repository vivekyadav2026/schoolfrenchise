<?php

namespace Tests\Feature;

use App\Models\AdmissionApplication;
use App\Models\Centre;
use App\Models\Course;
use App\Models\Role;
use App\Models\State;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdmissionAcknowledgementTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected Centre $centre;
    protected Course $course;
    protected State $state;

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

        $this->state = State::first() ?? State::create(['name' => 'Delhi', 'status' => true]);

        $this->centre = Centre::first() ?? Centre::create([
            'centre_code'   => 'DCE-ACK-01',
            'centre_name'   => 'Noida Main Training Centre',
            'director_name' => 'Dr. R. K. Sharma',
            'email'         => 'noidacentre@drishti.com',
            'mobile'        => '9988776655',
            'address'       => 'Sector 62, Institutional Area',
            'state_id'      => $this->state->id,
            'district_id'   => 1,
            'pincode'       => '201301',
            'status'        => 'active',
        ]);

        $this->course = Course::firstOrCreate(
            ['course_code' => 'ADCA-ACK-TEST'],
            [
                'course_name'        => 'Advanced Diploma in Computer Applications Test',
                'slug'               => 'adca-ack-test',
                'duration'           => '12 Months',
                'eligibility'        => '12th Standard',
                'default_course_fee' => 12500.00,
                'course_fee'         => 12500.00,
                'status'             => true,
            ]
        );
    }

    /**
     * Test 1: Printable admission acknowledgement slip renders successfully with candidate and course data.
     */
    public function test_admission_acknowledgement_slip_renders_successfully(): void
    {
        $application = AdmissionApplication::create([
            'application_no'        => 'APP-2026-TESTACK1',
            'centre_id'             => $this->centre->id,
            'course_id'             => $this->course->id,
            'applicant_name'        => 'Aarav Singhania',
            'father_name'           => 'Vikram Singhania',
            'mother_name'           => 'Sunita Singhania',
            'dob'                   => '2004-03-21',
            'gender'                => 'male',
            'mobile'                => '9811223344',
            'email'                 => 'aarav.singhania@example.com',
            'state_id'              => $this->state->id,
            'district_id'           => 1,
            'city'                  => 'Noida',
            'address'               => 'Block B, Sector 62',
            'pincode'               => '201301',
            'highest_qualification' => '12th Standard',
            'status'                => 'pending',
        ]);

        $response = $this->get(route('admission.acknowledgement', $application->application_no));

        $response->assertStatus(200);
        $response->assertSee('TECH ERA COMPUTER');
        $response->assertSee('ONLINE ADMISSION APPLICATION ACKNOWLEDGEMENT SLIP');
        $response->assertSee('APP-2026-TESTACK1');
        $response->assertSee('Aarav Singhania');
        $response->assertSee('Vikram Singhania');
        $response->assertSee('Advanced Diploma in Computer Applications Test');
        $response->assertSee('ADCA-ACK-TEST');
        $response->assertSee('UNDER SCRUTINY');
        $response->assertSee('Candidate Declaration');
        $response->assertSee('Print Acknowledgement Slip');
    }

    /**
     * Test 2: Invalid or non-existent application number returns 404.
     */
    public function test_invalid_application_number_returns_404(): void
    {
        $response = $this->get(route('admission.acknowledgement', 'APP-NONEXISTENT-9999'));

        $response->assertStatus(404);
    }

    /**
     * Test 3: Approved application displays converted roll number and registration on the acknowledgement slip.
     */
    public function test_approved_application_shows_converted_student_roll_number(): void
    {
        $studentUser = User::create([
            'name'      => 'Kavita Mishra',
            'email'     => 'kavita.ack@example.com',
            'password'  => bcrypt('password123'),
            'role'      => 'student',
            'is_active' => true,
        ]);

        $student = Student::create([
            'user_id'         => $studentUser->id,
            'centre_id'       => $this->centre->id,
            'registration_no' => 'DCE/2026/ACK/1001',
            'roll_no'         => 'DCE2699881',
            'full_name'       => 'Kavita Mishra',
            'father_name'     => 'Ramesh Mishra',
            'dob'             => '2003-09-10',
            'gender'          => 'female',
            'mobile'          => '9877665544',
            'email'           => 'kavita.ack@example.com',
            'state_id'        => $this->state->id,
            'district_id'     => 1,
            'city'            => 'Noida',
            'address'         => 'Sector 15',
            'pincode'         => '201301',
            'status'          => 'enrolled',
        ]);

        $application = AdmissionApplication::create([
            'application_no'        => 'APP-2026-ACKAPPR1',
            'centre_id'             => $this->centre->id,
            'course_id'             => $this->course->id,
            'applicant_name'        => 'Kavita Mishra',
            'father_name'           => 'Ramesh Mishra',
            'dob'                   => '2003-09-10',
            'gender'                => 'female',
            'mobile'                => '9877665544',
            'email'                 => 'kavita.ack@example.com',
            'state_id'              => $this->state->id,
            'district_id'           => 1,
            'city'                  => 'Noida',
            'address'               => 'Sector 15',
            'pincode'               => '201301',
            'status'                => 'approved',
            'converted_student_id'  => $student->id,
        ]);

        $response = $this->get(route('admission.acknowledgement', $application->application_no));

        $response->assertStatus(200);
        $response->assertSee('APPROVED');
        $response->assertSee('REGISTERED');
        $response->assertSee('DCE2699881');
        $response->assertSee('DCE/2026/ACK/1001');
    }

    /**
     * Test 4: Admission status tracking page includes button to acknowledgement slip.
     */
    public function test_admission_status_page_links_to_acknowledgement_slip(): void
    {
        $application = AdmissionApplication::create([
            'application_no' => 'APP-2026-STATUSLINK1',
            'centre_id'      => $this->centre->id,
            'course_id'      => $this->course->id,
            'applicant_name' => 'Deepak Verma',
            'father_name'    => 'Sunil Verma',
            'dob'            => '2002-11-05',
            'gender'         => 'male',
            'mobile'         => '9844332211',
            'state_id'       => $this->state->id,
            'district_id'    => 1,
            'city'           => 'Noida',
            'address'        => 'Sector 18',
            'pincode'        => '201301',
            'status'         => 'pending',
        ]);

        $response = $this->get(route('admission.status', ['application_no' => $application->application_no]));

        $response->assertStatus(200);
        $response->assertSee(route('admission.acknowledgement', $application->application_no));
        $response->assertSee('Print Acknowledgement Slip');
    }

    /**
     * Test 5: Admin application review page links to acknowledgement slip.
     */
    public function test_admin_application_review_links_to_acknowledgement_slip(): void
    {
        $application = AdmissionApplication::create([
            'application_no' => 'APP-2026-ADMINLINK1',
            'centre_id'      => $this->centre->id,
            'course_id'      => $this->course->id,
            'applicant_name' => 'Siddharth Roy',
            'father_name'    => 'Anand Roy',
            'dob'            => '2001-07-19',
            'gender'         => 'male',
            'mobile'         => '9833221100',
            'state_id'       => $this->state->id,
            'district_id'    => 1,
            'city'           => 'Noida',
            'address'        => 'Sector 62',
            'pincode'        => '201301',
            'status'         => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.admissions.application.show', $application->id));

        $response->assertStatus(200);
        $response->assertSee(route('admission.acknowledgement', $application->application_no));
        $response->assertSee('Print Application Slip');
    }
}

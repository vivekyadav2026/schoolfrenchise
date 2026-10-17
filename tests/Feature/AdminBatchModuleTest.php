<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Attendance;
use App\Models\Batch;
use App\Models\Centre;
use App\Models\Course;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminBatchModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected Centre $centre;
    protected Course $course;
    protected Student $student;
    protected Batch $batch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::firstOrCreate(
            ['email' => 'admin@drishticomputer.com'],
            ['name' => 'Super Administrator', 'password' => bcrypt('Admin@12345'), 'role' => 'super_admin', 'is_active' => true]
        );

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Admin']);
        $this->superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id]);

        $this->centre = Centre::first() ?? Centre::create([
            'centre_code' => 'TEST105',
            'centre_name' => 'Batch Test Centre',
            'contact_person' => 'Director',
            'mobile' => '9876543250',
            'email' => 'batch_centre@drishticomputer.com',
            'status' => 'active',
            'state_id' => 1,
            'district_id' => 1,
            'city' => 'Noida',
            'address' => 'Test Address',
        ]);

        $this->course = Course::first() ?? Course::create([
            'course_code' => 'ADCA',
            'course_name' => 'Advanced Diploma in Computer Application',
            'slug' => 'adca-advanced-diploma',
            'duration' => '12 Months',
            'default_course_fee' => 7500.00,
            'status' => 1,
        ]);

        $uniquePhone = '98' . rand(10000000, 99999999);
        $this->student = Student::create([
            'centre_id' => $this->centre->id,
            'registration_no' => 'DCE/2026/TEST/' . rand(1000, 9999),
            'roll_no' => 'DCE26' . rand(10000, 99999),
            'full_name' => 'Batch Student Verified',
            'father_name' => 'Father Test',
            'dob' => '2002-01-01',
            'gender' => 'female',
            'mobile' => $uniquePhone,
            'state_id' => 1,
            'district_id' => 1,
            'city' => 'Noida',
            'address' => 'Noida Sector 62',
            'pincode' => '201301',
            'status' => 'enrolled',
        ]);

        $this->batch = Batch::firstOrCreate(
            ['batch_name' => 'ADCA Morning Shift Alpha'],
            [
                'centre_id' => $this->centre->id,
                'course_id' => $this->course->id,
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
                'days' => 'Mon-Sat',
                'status' => 'active',
            ]
        );

        // Assign student to this batch
        Admission::create([
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'centre_id' => $this->centre->id,
            'batch_id' => $this->batch->id,
            'admission_date' => date('Y-m-d'),
            'course_fee' => 7500.00,
            'discount_amount' => 0.00,
            'net_fee' => 7500.00,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_view_batches_index(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.batches.index'));
        $response->assertStatus(200);
        $response->assertSee('Batches & Attendance Management', false);
        $response->assertSee('Batches Directory');
    }

    public function test_admin_can_view_batch_create_form(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.batches.create'));
        $response->assertStatus(200);
        $response->assertSee('Schedule New Training Batch');
    }

    public function test_admin_can_create_batch(): void
    {
        $postData = [
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'batch_name' => 'ADCA Evening Shift Beta',
            'start_time' => '17:00',
            'end_time' => '18:30',
            'days' => 'Mon-Sat',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->superAdmin)->post(route('admin.batches.store'), $postData);

        $this->assertDatabaseHas('batches', [
            'batch_name' => 'ADCA Evening Shift Beta',
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'status' => 'active',
        ]);

        $createdBatch = Batch::where('batch_name', 'ADCA Evening Shift Beta')->first();
        $response->assertRedirect(route('admin.batches.show', $createdBatch->id));
    }

    public function test_admin_can_view_batch_dossier(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.batches.show', $this->batch->id));
        $response->assertStatus(200);
        $response->assertSee($this->batch->batch_name);
        $response->assertSee('Batch Dossier & Schedule', false);
    }

    public function test_admin_can_update_batch(): void
    {
        $response = $this->actingAs($this->superAdmin)->put(
            route('admin.batches.update', $this->batch->id),
            [
                'centre_id' => $this->batch->centre_id,
                'course_id' => $this->batch->course_id,
                'batch_name' => 'ADCA Morning Shift Alpha (Updated)',
                'start_time' => '08:30',
                'end_time' => '10:00',
                'days' => 'Mon-Fri',
                'status' => 'active',
            ]
        );

        $this->batch->refresh();
        $this->assertEquals('ADCA Morning Shift Alpha (Updated)', $this->batch->batch_name);
        $this->assertEquals('Mon-Fri', $this->batch->days);
        $response->assertRedirect(route('admin.batches.show', $this->batch->id));
    }

    public function test_admin_can_soft_delete_batch(): void
    {
        $batch = Batch::create([
            'centre_id' => $this->centre->id,
            'course_id' => $this->course->id,
            'batch_name' => 'Temporary Batch to Delete',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'days' => 'MWF',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('admin.batches.destroy', $batch->id));
        $this->assertSoftDeleted('batches', ['id' => $batch->id]);
        $response->assertRedirect(route('admin.batches.index'));
    }

    public function test_admin_can_view_attendance_sheet(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(
            route('admin.batches.attendance.sheet', ['batch' => $this->batch->id, 'date' => date('Y-m-d')])
        );

        $response->assertStatus(200);
        $response->assertSee('Daily Attendance Sheet');
        $response->assertSee('Class Roll Call');
        $response->assertSee($this->student->full_name);
    }

    public function test_admin_can_mark_attendance(): void
    {
        $today = date('Y-m-d');
        $postData = [
            'attendance_date' => $today,
            'attendance' => [
                $this->student->id => 'present',
            ],
            'remarks' => [
                $this->student->id => 'On-time lab practice',
            ],
        ];

        $response = $this->actingAs($this->superAdmin)->post(
            route('admin.batches.attendance.mark', $this->batch->id),
            $postData
        );

        $this->assertDatabaseHas('attendances', [
            'batch_id' => $this->batch->id,
            'student_id' => $this->student->id,
            'attendance_date' => $today,
            'status' => 'present',
            'remarks' => 'On-time lab practice',
        ]);

        $response->assertRedirect(
            route('admin.batches.attendance.sheet', ['batch' => $this->batch->id, 'date' => $today])
        );
    }

    public function test_admin_can_view_attendance_report(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.batches.attendance.report'));
        $response->assertStatus(200);
        $response->assertSee('Institutional Attendance Reports');
        $response->assertSee('Total Logged Sessions');
    }
}

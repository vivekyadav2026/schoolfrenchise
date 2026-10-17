<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\Role;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminNotificationModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $centreUser;
    protected User $studentUser;
    protected Centre $centre;

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
            'centre_code'      => 'DCE-TEST-NOTIF',
            'centre_name'      => 'Notification Test Centre',
            'director_name'    => 'Director Notif',
            'email'            => 'notifcentre@drishti.com',
            'mobile'           => '9888877775',
            'state_id'         => 1,
            'district_id'      => 1,
            'city'             => 'Delhi',
            'address'          => 'Notification Address',
            'pincode'          => '110001',
            'affiliation_date' => now()->toDateString(),
            'status'           => 'active',
        ]);

        $this->centreUser = User::firstOrCreate(
            ['email' => 'director.notif@drishti.com'],
            [
                'name'      => 'Centre Director Test',
                'password'  => bcrypt('Director@2026'),
                'role'      => 'centre',
                'centre_id' => $this->centre->id,
                'is_active' => true,
            ]
        );

        $this->studentUser = User::firstOrCreate(
            ['email' => 'student.notif@drishti.com'],
            [
                'name'      => 'Student Notif Recipient',
                'password'  => bcrypt('Student@2026'),
                'role'      => 'student',
                'centre_id' => $this->centre->id,
                'is_active' => true,
            ]
        );
    }

    public function test_guest_cannot_access_notifications()
    {
        $response = $this->get(route('admin.notifications.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_notifications_index()
    {
        SystemNotification::create([
            'user_id'    => $this->studentUser->id,
            'sender_id'  => $this->admin->id,
            'title'      => 'Admit Card Generated for Term Examination',
            'message'    => 'Your hall ticket for December 2026 examination is ready.',
            'type'       => 'exam',
            'action_url' => '/student/exams',
            'read_at'    => null,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Notification Center');
        $response->assertSee('Admit Card Generated for Term Examination');
        $response->assertSee($this->studentUser->name);
    }

    public function test_admin_can_view_broadcast_composer()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.notifications.create'));

        $response->assertStatus(200);
        $response->assertSee('Compose & Dispatch Broadcast', false);
        $response->assertSee($this->centre->centre_name);
    }

    public function test_admin_can_broadcast_notification_to_all_students()
    {
        $payload = [
            'target_group'  => 'students',
            'title'         => 'Scholarship Examination Circular 2026',
            'message'       => 'National IT scholarship test registrations close on Friday.',
            'type'          => 'info',
            'action_url'    => '/student/dashboard',
            'send_email'    => '1',
            'send_whatsapp' => '1',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.notifications.store'), $payload);

        $response->assertRedirect(route('admin.notifications.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('system_notifications', [
            'user_id' => $this->studentUser->id,
            'title'   => 'Scholarship Examination Circular 2026',
            'type'    => 'info',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action'  => 'notification_broadcast',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_broadcast_notification_to_centres_only()
    {
        $payload = [
            'target_group' => 'centres',
            'title'        => 'Urgent: Monthly Attendance Cut-off',
            'message'      => 'Please finalize October student attendance entries before 5 PM.',
            'type'         => 'alert',
            'action_url'   => '/centre/attendance',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.notifications.store'), $payload);

        $response->assertRedirect(route('admin.notifications.index'));

        $this->assertDatabaseHas('system_notifications', [
            'user_id' => $this->centreUser->id,
            'title'   => 'Urgent: Monthly Attendance Cut-off',
            'type'    => 'alert',
        ]);
    }

    public function test_admin_can_broadcast_to_specific_individual_user()
    {
        $payload = [
            'target_group' => 'specific_user',
            'user_id'      => $this->studentUser->id,
            'title'        => 'Fee Installment 2 Clearance Notice',
            'message'      => 'Your second installment of Rs. 1,500 is due on 25th Oct.',
            'type'         => 'fee',
            'action_url'   => '/student/fees',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.notifications.store'), $payload);

        $response->assertRedirect(route('admin.notifications.index'));

        $this->assertDatabaseHas('system_notifications', [
            'user_id' => $this->studentUser->id,
            'title'   => 'Fee Installment 2 Clearance Notice',
            'type'    => 'fee',
        ]);
    }

    public function test_admin_can_mark_notification_as_read()
    {
        $notification = SystemNotification::create([
            'user_id'    => $this->admin->id,
            'sender_id'  => $this->centreUser->id,
            'title'      => 'Pending Admission Verification Request',
            'message'    => '5 new applications submitted by DCE-1001.',
            'type'       => 'system',
            'read_at'    => null,
        ]);

        $this->assertNull($notification->read_at);

        $response = $this->actingAs($this->admin)->post(route('admin.notifications.read', $notification));

        $response->assertSessionHas('success');
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_admin_can_mark_all_notifications_as_read()
    {
        SystemNotification::create([
            'user_id'   => $this->admin->id,
            'title'     => 'Unread Alert 1',
            'message'   => 'Message 1',
            'type'      => 'info',
            'read_at'   => null,
        ]);

        SystemNotification::create([
            'user_id'   => $this->admin->id,
            'title'     => 'Unread Alert 2',
            'message'   => 'Message 2',
            'type'      => 'info',
            'read_at'   => null,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.notifications.mark-all-read'));

        $response->assertSessionHas('success');
        $this->assertEquals(0, SystemNotification::whereNull('read_at')->count());
    }

    public function test_admin_can_delete_notification()
    {
        $notification = SystemNotification::create([
            'user_id'   => $this->admin->id,
            'title'     => 'Notification to Delete',
            'message'   => 'Temporary broadcast log.',
            'type'      => 'info',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.notifications.destroy', $notification));

        $response->assertRedirect(route('admin.notifications.index'));
        $this->assertDatabaseMissing('system_notifications', ['id' => $notification->id]);
    }
}

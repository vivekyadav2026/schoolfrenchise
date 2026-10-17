<?php

namespace Tests\Feature;

use App\Models\Centre;
use App\Models\Role;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminSupportModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $staff;
    protected Centre $centre;
    protected SupportTicket $ticket;

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

        $this->staff = User::firstOrCreate(
            ['email' => 'support.officer@drishticomputer.com'],
            [
                'name'      => 'Officer Priya Verma',
                'password'  => bcrypt('Officer@2026'),
                'role'      => 'staff',
                'is_active' => true,
            ]
        );

        $this->centre = Centre::first() ?? Centre::create([
            'centre_code'      => 'DCE-TEST-SUP01',
            'centre_name'      => 'Support Test Centre',
            'director_name'    => 'Director Support',
            'email'            => 'supportcentre@drishti.com',
            'mobile'           => '9888877773',
            'state_id'         => 1,
            'district_id'      => 1,
            'city'             => 'Delhi',
            'address'          => 'Support Address',
            'pincode'          => '110001',
            'affiliation_date' => now()->toDateString(),
            'status'           => 'active',
        ]);

        $this->ticket = SupportTicket::firstOrCreate(
            ['ticket_no' => 'TCK-TEST-MOD13'],
            [
                'user_id'       => $this->admin->id,
                'centre_id'     => $this->centre->id,
                'category'      => 'exam',
                'priority'      => 'high',
                'subject'       => 'Urgent: Exam Schedule Clarification for DCA',
                'description'   => 'Centre requires clarification on online exam slot timings for DCA batch.',
                'status'        => 'open',
                'last_reply_at' => now(),
            ]
        );
    }

    public function test_admin_can_view_support_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.support.index'));
        $response->assertStatus(200);
        $response->assertSee('Help & Support Central Helpdesk', false);
        $response->assertSee('TCK-TEST-MOD13');
        $response->assertSee('Urgent: Exam Schedule Clarification for DCA');
    }

    public function test_admin_can_view_create_ticket_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.support.create'));
        $response->assertStatus(200);
        $response->assertSee('Create New Support Ticket', false);
        $response->assertSee('Technical / Portal Issue', false);
    }

    public function test_admin_can_store_support_ticket(): void
    {
        $payload = [
            'centre_id'   => $this->centre->id,
            'category'    => 'billing',
            'priority'    => 'urgent',
            'subject'     => 'Dispute in Kit Order Invoice #987',
            'description' => 'Centre reports 5 damaged bag kits in parcel received on 17/10/2026.',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.support.store'), $payload);
        $response->assertRedirect();

        $this->assertDatabaseHas('support_tickets', [
            'centre_id' => $this->centre->id,
            'category'  => 'billing',
            'priority'  => 'urgent',
            'subject'   => 'Dispute in Kit Order Invoice #987',
            'status'    => 'open',
        ]);
    }

    public function test_admin_can_view_ticket_discussion_dossier(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.support.show', $this->ticket));
        $response->assertStatus(200);
        $response->assertSee('TCK-TEST-MOD13');
        $response->assertSee('Urgent: Exam Schedule Clarification for DCA');
        $response->assertSee('Post Official Reply', false);
    }

    public function test_admin_can_post_reply_to_ticket(): void
    {
        $payload = [
            'message' => 'Online exam slots for DCA will be scheduled daily from 10:00 AM to 12:00 PM.',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.support.reply', $this->ticket), $payload);
        $response->assertRedirect();

        $this->assertDatabaseHas('support_ticket_replies', [
            'ticket_id'      => $this->ticket->id,
            'user_id'        => $this->admin->id,
            'message'        => 'Online exam slots for DCA will be scheduled daily from 10:00 AM to 12:00 PM.',
            'is_staff_reply' => 1,
        ]);

        $this->ticket->refresh();
        $this->assertEquals('answered', $this->ticket->status);
    }

    public function test_admin_can_post_reply_and_mark_ticket_resolved(): void
    {
        $payload = [
            'message'       => 'Issue investigated and rectified on the live portal.',
            'mark_resolved' => 1,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.support.reply', $this->ticket), $payload);
        $response->assertRedirect();

        $this->ticket->refresh();
        $this->assertEquals('resolved', $this->ticket->status);
    }

    public function test_admin_can_assign_staff_to_ticket(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.support.assign', $this->ticket), [
            'assigned_to' => $this->staff->id,
        ]);

        $response->assertRedirect();

        $this->ticket->refresh();
        $this->assertEquals($this->staff->id, $this->ticket->assigned_to);
        $this->assertEquals('in_progress', $this->ticket->status);
    }

    public function test_admin_can_update_ticket_status(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.support.status', $this->ticket), [
            'status' => 'closed',
        ]);

        $response->assertRedirect();

        $this->ticket->refresh();
        $this->assertEquals('closed', $this->ticket->status);
    }

    public function test_admin_can_archive_support_ticket(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.support.destroy', $this->ticket));
        $response->assertRedirect(route('admin.support.index'));

        $this->assertSoftDeleted('support_tickets', [
            'id' => $this->ticket->id,
        ]);
    }
}

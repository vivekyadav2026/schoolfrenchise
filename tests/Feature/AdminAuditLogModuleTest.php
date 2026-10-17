<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminAuditLogModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected AuditLog $sampleLog;

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

        $this->sampleLog = AuditLog::create([
            'user_id'        => $this->admin->id,
            'action'         => 'fee_payment_refunded',
            'auditable_type' => User::class,
            'auditable_id'   => $this->admin->id,
            'old_values'     => ['status' => 'cleared', 'amount' => 5000],
            'new_values'     => ['status' => 'refunded', 'amount' => 5000, 'refund_reason' => 'Administrative test reversal'],
            'ip_address'     => '192.168.1.100',
            'user_agent'     => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ]);
    }

    public function test_guest_cannot_access_audit_logs()
    {
        $response = $this->get(route('admin.audit-logs.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_audit_logs_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.index'));

        $response->assertStatus(200);
        $response->assertSee('Audit Logs & Security Trail', false);
        $response->assertSee('Total System Logs', false);
        $response->assertSee('fee_payment_refunded');
        $response->assertSee($this->admin->name);
    }

    public function test_admin_can_filter_audit_logs_by_action_and_search()
    {
        $uniqueAction = 'custom_security_test_action_' . uniqid();
        AuditLog::create([
            'user_id'    => $this->admin->id,
            'action'     => $uniqueAction,
            'ip_address' => '10.0.0.1',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.index', [
            'action' => $uniqueAction,
            'search' => '10.0.0.1',
        ]));

        $response->assertStatus(200);
        $response->assertSee($uniqueAction);
        $response->assertSee('10.0.0.1');
    }

    public function test_admin_can_view_audit_log_inspection_dossier()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.show', $this->sampleLog));

        $response->assertStatus(200);
        $response->assertSee('Audit Record #LOG-');
        $response->assertSee('fee_payment_refunded');
        $response->assertSee($this->admin->name);
        $response->assertSee('192.168.1.100');
        $response->assertSee('Administrative test reversal');
    }

    public function test_admin_can_export_audit_logs_csv()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}

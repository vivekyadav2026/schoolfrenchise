<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\Expense;
use App\Models\MarketingExpense;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminExpenseModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
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
            'centre_code'      => 'DCE-TEST-EXP01',
            'centre_name'      => 'Expense Test Centre',
            'director_name'    => 'Director Expense',
            'email'            => 'expensecentre@drishti.com',
            'mobile'           => '9888877774',
            'state_id'         => 1,
            'district_id'      => 1,
            'city'             => 'Delhi',
            'address'          => 'Expense Address',
            'pincode'          => '110001',
            'affiliation_date' => now()->toDateString(),
            'status'           => 'active',
        ]);
    }

    public function test_guest_cannot_access_expenses()
    {
        $response = $this->get(route('admin.expenses.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_expenses_index_with_statistics()
    {
        Expense::create([
            'centre_id'    => $this->centre->id,
            'category'     => 'electricity',
            'title'        => 'Central Office Power Bill May',
            'amount'       => 4500.00,
            'expense_date' => now()->toDateString(),
            'payment_mode' => 'upi',
            'reference_no' => 'UPI-POW-4500',
            'recorded_by'  => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.expenses.index'));

        $response->assertStatus(200);
        $response->assertSee('Operating Expenses & Cost Ledger');
        $response->assertSee('Central Office Power Bill May');
        $response->assertSee('4,500.00');
    }

    public function test_admin_can_view_create_expense_form()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.expenses.create'));

        $response->assertStatus(200);
        $response->assertSee('Record New Operating Expense', false);
        $response->assertSee($this->centre->centre_name);
    }

    public function test_admin_can_store_operating_expense_with_audit_trail()
    {
        $payload = [
            'centre_id'    => $this->centre->id,
            'category'     => 'hardware',
            'title'        => 'Purchase of 5 Logitech USB Keyboards',
            'amount'       => 2750.00,
            'expense_date' => now()->toDateString(),
            'payment_mode' => 'bank_transfer',
            'reference_no' => 'NEFT-HARD-2750',
            'notes'        => 'Computer Lab 2 hardware upgrade',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.expenses.store'), $payload);

        $response->assertRedirect(route('admin.expenses.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('expenses', [
            'centre_id'    => $this->centre->id,
            'category'     => 'hardware',
            'title'        => 'Purchase of 5 Logitech USB Keyboards',
            'amount'       => 2750.00,
            'payment_mode' => 'bank_transfer',
            'reference_no' => 'NEFT-HARD-2750',
            'recorded_by'  => $this->admin->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action'    => 'expense_recorded',
            'user_id'   => $this->admin->id,
        ]);
    }

    public function test_store_expense_fails_on_validation_errors()
    {
        $payload = [
            'title'        => '',
            'amount'       => -100,
            'expense_date' => '',
            'payment_mode' => 'invalid_mode',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.expenses.store'), $payload);

        $response->assertSessionHasErrors(['title', 'amount', 'expense_date', 'payment_mode', 'category']);
    }

    public function test_admin_can_view_edit_form_and_update_expense()
    {
        $expense = Expense::create([
            'centre_id'    => $this->centre->id,
            'category'     => 'internet',
            'title'        => 'Fiber Broadband Bill',
            'amount'       => 1200.00,
            'expense_date' => now()->toDateString(),
            'payment_mode' => 'upi',
            'recorded_by'  => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.expenses.edit', $expense));
        $response->assertStatus(200);
        $response->assertSee('Edit Expense Entry');
        $response->assertSee('Fiber Broadband Bill');

        $updatePayload = [
            'centre_id'    => $this->centre->id,
            'category'     => 'internet',
            'title'        => 'Fiber Broadband High-Speed Bill',
            'amount'       => 1500.00,
            'expense_date' => now()->toDateString(),
            'payment_mode' => 'bank_transfer',
            'reference_no' => 'UPI-FIBER-REV',
            'notes'        => 'Speed upgraded to 200Mbps',
        ];

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.expenses.update', $expense), $updatePayload);
        $updateResponse->assertRedirect(route('admin.expenses.index'));

        $this->assertDatabaseHas('expenses', [
            'id'     => $expense->id,
            'title'  => 'Fiber Broadband High-Speed Bill',
            'amount' => 1500.00,
        ]);
    }

    public function test_admin_can_delete_expense_soft_deletes()
    {
        $expense = Expense::create([
            'centre_id'    => $this->centre->id,
            'category'     => 'stationery',
            'title'        => 'Exam Print Paper 10 Reams',
            'amount'       => 3500.00,
            'expense_date' => now()->toDateString(),
            'payment_mode' => 'cash',
            'recorded_by'  => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.expenses.destroy', $expense));

        $response->assertRedirect(route('admin.expenses.index'));
        $this->assertSoftDeleted('expenses', ['id' => $expense->id]);
    }

    public function test_admin_can_view_marketing_campaigns_list()
    {
        MarketingExpense::create([
            'centre_id'         => $this->centre->id,
            'campaign_name'     => 'DCE Diwali Admission Blast',
            'platform'          => 'Facebook & Instagram Ads',
            'budget'            => 10000.00,
            'amount_spent'      => 8500.00,
            'start_date'        => now()->toDateString(),
            'leads_generated'   => 120,
            'conversions_count' => 35,
            'recorded_by'       => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.expenses.marketing'));

        $response->assertStatus(200);
        $response->assertSee('DCE Diwali Admission Blast');
        $response->assertSee('8,500.00');
        $response->assertSee('Facebook &amp; Instagram Ads', false);
    }

    public function test_admin_can_create_and_store_marketing_campaign()
    {
        $formResponse = $this->actingAs($this->admin)->get(route('admin.expenses.marketing.create'));
        $formResponse->assertStatus(200);
        $formResponse->assertSee('Log Marketing Campaign');

        $payload = [
            'centre_id'         => $this->centre->id,
            'campaign_name'     => 'New Year 2026 Coding Bootcamps Campaign',
            'platform'          => 'Google Ads (Search & Display)',
            'budget'            => 15000.00,
            'amount_spent'      => 12000.00,
            'start_date'        => now()->toDateString(),
            'end_date'          => now()->addDays(30)->toDateString(),
            'leads_generated'   => 85,
            'conversions_count' => 20,
            'notes'             => 'Search ads targeting Python & Full Stack keyword phrases in NCR',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.expenses.marketing.store'), $payload);

        $response->assertRedirect(route('admin.expenses.marketing'));
        $this->assertDatabaseHas('marketing_expenses', [
            'campaign_name'   => 'New Year 2026 Coding Bootcamps Campaign',
            'platform'        => 'Google Ads (Search & Display)',
            'budget'          => 15000.00,
            'amount_spent'    => 12000.00,
            'leads_generated' => 85,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'marketing_campaign_created',
        ]);
    }

    public function test_admin_can_edit_and_update_marketing_campaign()
    {
        $campaign = MarketingExpense::create([
            'centre_id'         => null,
            'campaign_name'     => 'Summer Camp Campaign',
            'platform'          => 'YouTube Video Ads',
            'budget'            => 5000.00,
            'amount_spent'      => 4000.00,
            'start_date'        => now()->toDateString(),
            'leads_generated'   => 30,
            'conversions_count' => 8,
            'recorded_by'       => $this->admin->id,
        ]);

        $editResponse = $this->actingAs($this->admin)->get(route('admin.expenses.marketing.edit', $campaign));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Summer Camp Campaign');

        $updatePayload = [
            'centre_id'         => null,
            'campaign_name'     => 'Summer Camp Campaign - Extended',
            'platform'          => 'YouTube Video Ads',
            'budget'            => 8000.00,
            'amount_spent'      => 7500.00,
            'start_date'        => now()->toDateString(),
            'leads_generated'   => 55,
            'conversions_count' => 15,
            'notes'             => 'Extended campaign due to high CTR',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.expenses.marketing.update', $campaign), $updatePayload);
        $response->assertRedirect(route('admin.expenses.marketing'));

        $this->assertDatabaseHas('marketing_expenses', [
            'id'              => $campaign->id,
            'campaign_name'   => 'Summer Camp Campaign - Extended',
            'amount_spent'    => 7500.00,
            'leads_generated' => 55,
        ]);
    }

    public function test_admin_can_delete_marketing_campaign()
    {
        $campaign = MarketingExpense::create([
            'centre_id'         => null,
            'campaign_name'     => 'Temporary Poster Run',
            'platform'          => 'Outdoor Hoardings / Banners',
            'budget'            => 2000.00,
            'amount_spent'      => 2000.00,
            'start_date'        => now()->toDateString(),
            'recorded_by'       => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.expenses.marketing.destroy', $campaign));

        $response->assertRedirect(route('admin.expenses.marketing'));
        $this->assertDatabaseMissing('marketing_expenses', ['id' => $campaign->id]);
    }
}

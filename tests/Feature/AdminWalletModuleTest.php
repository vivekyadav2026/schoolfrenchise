<?php

namespace Tests\Feature;

use App\Models\Centre;
use App\Models\CentreWallet;
use App\Models\CentreWalletTransaction;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminWalletModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected Centre $centre;
    protected CentreWallet $wallet;

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

        $this->centre = Centre::firstOrCreate(
            ['centre_code' => 'DCE-TEST-WALLET01'],
            [
                'centre_name'      => 'Wallet Test Centre',
                'director_name'    => 'Director Wallet',
                'email'            => 'walletcentre@drishti.com',
                'mobile'           => '9888877774',
                'state_id'         => 1,
                'district_id'      => 1,
                'city'             => 'Delhi',
                'address'          => 'Wallet Centre Address',
                'pincode'          => '110001',
                'affiliation_date' => now()->toDateString(),
                'status'           => 'active',
            ]
        );

        $this->wallet = CentreWallet::firstOrCreate(
            ['centre_id' => $this->centre->id],
            [
                'balance'        => 1000.00,
                'total_credited' => 1000.00,
                'total_debited'  => 0.00,
                'status'         => 'active',
            ]
        );
    }

    public function test_admin_can_view_wallet_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.wallet.index'));
        $response->assertStatus(200);
        $response->assertSee('Franchise Wallet & Ledger History', false);
        $response->assertSee('Circulating Balance', false);
    }

    public function test_admin_can_view_adjust_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.wallet.adjust'));
        $response->assertStatus(200);
        $response->assertSee('Execute Ledger Adjustment', false);
        $response->assertSee('Wallet Test Centre');
    }

    public function test_admin_can_store_credit_wallet_adjustment(): void
    {
        $initialBalance = (float) $this->wallet->balance;
        $creditAmount = 5000.00;

        $payload = [
            'centre_id'    => $this->centre->id,
            'type'         => 'credit',
            'amount'       => $creditAmount,
            'reference_no' => 'BANK-IMPS-987654',
            'description'  => 'Direct NEFT deposit by franchisee confirmed.',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.wallet.adjust.store'), $payload);
        $response->assertRedirect(route('admin.wallet.index'));

        $this->wallet->refresh();
        $this->assertEquals($initialBalance + $creditAmount, (float) $this->wallet->balance);

        $this->assertDatabaseHas('centre_wallet_transactions', [
            'centre_id'     => $this->centre->id,
            'type'          => 'credit',
            'amount'        => $creditAmount,
            'reference_no'  => 'BANK-IMPS-987654',
            'balance_after' => $initialBalance + $creditAmount,
            'status'        => 'approved',
        ]);
    }

    public function test_admin_can_store_debit_wallet_adjustment(): void
    {
        // First set balance to 6000
        $this->wallet->update(['balance' => 6000.00]);
        $debitAmount = 2500.00;

        $payload = [
            'centre_id'    => $this->centre->id,
            'type'         => 'debit',
            'amount'       => $debitAmount,
            'reference_no' => 'DEBIT-KIT-ORDER-99',
            'description'  => 'Deduction for 10 Student Welcome Kits dispatched.',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.wallet.adjust.store'), $payload);
        $response->assertRedirect(route('admin.wallet.index'));

        $this->wallet->refresh();
        $this->assertEquals(3500.00, (float) $this->wallet->balance);

        $this->assertDatabaseHas('centre_wallet_transactions', [
            'centre_id'     => $this->centre->id,
            'type'          => 'debit',
            'amount'        => $debitAmount,
            'reference_no'  => 'DEBIT-KIT-ORDER-99',
            'balance_after' => 3500.00,
            'status'        => 'approved',
        ]);
    }

    public function test_debit_fails_if_insufficient_wallet_balance(): void
    {
        $this->wallet->update(['balance' => 500.00]);

        $payload = [
            'centre_id'    => $this->centre->id,
            'type'         => 'debit',
            'amount'       => 2000.00,
            'reference_no' => 'OVERDRAFT-FAIL-TEST',
            'description'  => 'Attempting overdraft deduction.',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.wallet.adjust.store'), $payload);
        $response->assertSessionHasErrors('amount');

        $this->wallet->refresh();
        $this->assertEquals(500.00, (float) $this->wallet->balance);
    }

    public function test_admin_can_approve_pending_topup_request(): void
    {
        $this->wallet->update(['balance' => 1000.00]);

        $pendingTxn = CentreWalletTransaction::create([
            'centre_id'     => $this->centre->id,
            'type'          => 'credit',
            'amount'        => 4000.00,
            'balance_after' => 1000.00,
            'reference_no'  => 'REQ-TOPUP-12345',
            'description'   => 'Centre submitted recharge request via UPI.',
            'status'        => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.wallet.transactions.approve', $pendingTxn));
        $response->assertRedirect();

        $pendingTxn->refresh();
        $this->assertEquals('approved', $pendingTxn->status);
        $this->assertEquals(5000.00, (float) $pendingTxn->balance_after);

        $this->wallet->refresh();
        $this->assertEquals(5000.00, (float) $this->wallet->balance);
    }

    public function test_admin_can_reject_pending_topup_request(): void
    {
        $this->wallet->update(['balance' => 1000.00]);

        $pendingTxn = CentreWalletTransaction::create([
            'centre_id'     => $this->centre->id,
            'type'          => 'credit',
            'amount'        => 3000.00,
            'balance_after' => 1000.00,
            'reference_no'  => 'REQ-TOPUP-FAIL',
            'description'   => 'Duplicate recharge claim.',
            'status'        => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.wallet.transactions.reject', $pendingTxn), [
            'reason' => 'Invalid bank transfer screenshot attached.',
        ]);

        $response->assertRedirect();

        $pendingTxn->refresh();
        $this->assertEquals('rejected', $pendingTxn->status);
        $this->assertStringContainsString('Invalid bank transfer', $pendingTxn->description);

        $this->wallet->refresh();
        $this->assertEquals(1000.00, (float) $this->wallet->balance);
    }

    public function test_admin_can_toggle_freeze_centre_wallet(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.wallet.centres.freeze', $this->centre));
        $response->assertRedirect();

        $this->wallet->refresh();
        $this->assertEquals('frozen', $this->wallet->status);

        // Attempting adjustment on frozen wallet should fail
        $payload = [
            'centre_id'    => $this->centre->id,
            'type'         => 'credit',
            'amount'       => 100.00,
            'reference_no' => 'FROZEN-TEST',
            'description'  => 'Testing frozen block.',
        ];

        $failResponse = $this->actingAs($this->admin)->post(route('admin.wallet.adjust.store'), $payload);
        $failResponse->assertSessionHasErrors('centre_id');

        // Unfreeze
        $this->actingAs($this->admin)->post(route('admin.wallet.centres.freeze', $this->centre));
        $this->wallet->refresh();
        $this->assertEquals('active', $this->wallet->status);
    }

    public function test_admin_can_view_centre_statement(): void
    {
        CentreWalletTransaction::create([
            'centre_id'     => $this->centre->id,
            'type'          => 'credit',
            'amount'        => 1000.00,
            'balance_after' => 1000.00,
            'reference_no'  => 'TXN-STMT-01',
            'description'   => 'Opening Balance Grant',
            'status'        => 'approved',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.wallet.statement', $this->centre));
        $response->assertStatus(200);
        $response->assertSee('Account Statement', false);
        $response->assertSee($this->centre->centre_name);
        $response->assertSee('TXN-STMT-01');
    }
}

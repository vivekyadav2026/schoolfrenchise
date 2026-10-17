<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected Centre $centre;
    protected Role $adminRole;
    protected Role $centreRole;
    protected Role $staffRole;

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

        $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator']);
        $this->centreRole = Role::firstOrCreate(['name' => 'centre'], ['display_name' => 'Centre Director']);
        $this->staffRole = Role::firstOrCreate(['name' => 'staff'], ['display_name' => 'Staff / Faculty']);

        $this->centre = Centre::first() ?? Centre::create([
            'centre_code'      => 'DCE-TEST-USR01',
            'centre_name'      => 'User Test Centre',
            'director_name'    => 'Director User Test',
            'email'            => 'usercentre@drishti.com',
            'mobile'           => '9888877775',
            'state_id'         => 1,
            'district_id'      => 1,
            'city'             => 'Delhi',
            'address'          => 'Test Address User',
            'pincode'          => '110001',
            'affiliation_date' => now()->toDateString(),
            'status'           => 'active',
        ]);
    }

    public function test_guest_cannot_access_user_management()
    {
        $response = $this->get(route('admin.users.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_users_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('User & Access Management', false);
        $response->assertSee('Add New User');
        $response->assertSee($this->admin->name);
    }

    public function test_admin_can_filter_users_by_role_and_search()
    {
        $uniqueEmail = 'staff.filter.' . uniqid() . '@drishti.com';
        $testUser = User::create([
            'name'      => 'Vikramaditya Faculty',
            'email'     => $uniqueEmail,
            'mobile'    => '9812345678',
            'password'  => Hash::make('Password@123'),
            'role'      => 'staff',
            'centre_id' => $this->centre->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.users.index', [
            'search' => 'Vikramaditya',
            'role'   => 'staff',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Vikramaditya Faculty');
        $response->assertSee($uniqueEmail);
    }

    public function test_admin_can_view_create_user_form()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertSee('Provision New User Account');
        $response->assertSee('Personal & Contact Details', false);
    }

    public function test_admin_can_create_new_user()
    {
        $email = 'newmanager.' . uniqid() . '@drishticomputer.com';
        $payload = [
            'name'                  => 'Kavita Sharma',
            'email'                 => $email,
            'mobile'                => '9876500001',
            'password'              => 'SecurePass@2026',
            'password_confirmation' => 'SecurePass@2026',
            'role'                  => 'centre',
            'centre_id'             => $this->centre->id,
            'role_ids'              => [$this->centreRole->id],
            'is_active'             => 1,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), $payload);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email'     => $email,
            'name'      => 'Kavita Sharma',
            'role'      => 'centre',
            'centre_id' => $this->centre->id,
            'is_active' => 1,
        ]);

        $createdUser = User::where('email', $email)->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue(Hash::check('SecurePass@2026', $createdUser->password));
        $this->assertTrue($createdUser->roles->contains('id', $this->centreRole->id));
    }

    public function test_validation_blocks_duplicate_email_and_short_password()
    {
        $payload = [
            'name'                  => 'Duplicate Tester',
            'email'                 => $this->admin->email, // Existing email
            'mobile'                => '9876500002',
            'password'              => 'short', // under 8 chars
            'password_confirmation' => 'short',
            'role'                  => 'staff',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), $payload);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_admin_can_view_user_dossier()
    {
        $testUser = User::create([
            'name'      => 'Pooja Reddy',
            'email'     => 'pooja.' . uniqid() . '@drishti.com',
            'mobile'    => '9876543209',
            'password'  => Hash::make('Password@123'),
            'role'      => 'staff',
            'centre_id' => $this->centre->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.users.show', $testUser));

        $response->assertStatus(200);
        $response->assertSee('User Dossier');
        $response->assertSee('Pooja Reddy');
        $response->assertSee($testUser->email);
        $response->assertSee('Account Particulars');
    }

    public function test_admin_can_view_edit_user_form()
    {
        $testUser = User::create([
            'name'      => 'Sunil Nambiar',
            'email'     => 'sunil.' . uniqid() . '@drishti.com',
            'mobile'    => '9876543208',
            'password'  => Hash::make('Password@123'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.users.edit', $testUser));

        $response->assertStatus(200);
        $response->assertSee('Edit User Account: Sunil Nambiar');
        $response->assertSee($testUser->email);
    }

    public function test_admin_can_update_user_details()
    {
        $testUser = User::create([
            'name'      => 'Original Name',
            'email'     => 'original.' . uniqid() . '@drishti.com',
            'mobile'    => '9876543207',
            'password'  => Hash::make('Password@123'),
            'role'      => 'staff',
            'centre_id' => $this->centre->id,
            'is_active' => true,
        ]);

        $updatedEmail = 'modified.' . uniqid() . '@drishti.com';
        $payload = [
            'name'                  => 'Updated Officer Name',
            'email'                 => $updatedEmail,
            'mobile'                => '9999988888',
            'role'                  => 'admin',
            'centre_id'             => null,
            'is_active'             => 1,
            'password'              => 'NewSecurePass@2026',
            'password_confirmation' => 'NewSecurePass@2026',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $testUser), $payload);

        $response->assertRedirect(route('admin.users.show', $testUser));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id'     => $testUser->id,
            'name'   => 'Updated Officer Name',
            'email'  => $updatedEmail,
            'mobile' => '9999988888',
            'role'   => 'admin',
        ]);

        $testUser->refresh();
        $this->assertTrue(Hash::check('NewSecurePass@2026', $testUser->password));
    }

    public function test_admin_cannot_deactivate_or_demote_self()
    {
        // Try deactivating self
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->admin), [
            'name'      => $this->admin->name,
            'email'     => $this->admin->email,
            'role'      => 'super_admin',
            'is_active' => 0, // attempt deactivation
        ]);

        $response->assertSessionHas('error');
        $this->admin->refresh();
        $this->assertTrue((bool)$this->admin->is_active);

        // Try toggling status of self
        $toggleResponse = $this->actingAs($this->admin)->post(route('admin.users.toggle-status', $this->admin));
        $toggleResponse->assertSessionHas('error');
        $this->admin->refresh();
        $this->assertTrue((bool)$this->admin->is_active);
    }

    public function test_admin_can_toggle_another_user_status()
    {
        $targetUser = User::create([
            'name'      => 'Target Active User',
            'email'     => 'target.active.' . uniqid() . '@drishti.com',
            'password'  => Hash::make('Password@123'),
            'role'      => 'staff',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.users.toggle-status', $targetUser));

        $response->assertSessionHas('success');
        $targetUser->refresh();
        $this->assertFalse((bool)$targetUser->is_active);

        // Toggle back to active
        $response2 = $this->actingAs($this->admin)->post(route('admin.users.toggle-status', $targetUser));
        $response2->assertSessionHas('success');
        $targetUser->refresh();
        $this->assertTrue((bool)$targetUser->is_active);
    }

    public function test_admin_can_reset_user_password()
    {
        $targetUser = User::create([
            'name'      => 'Target Pass User',
            'email'     => 'target.pass.' . uniqid() . '@drishti.com',
            'password'  => Hash::make('OldPassword@123'),
            'role'      => 'staff',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.users.reset-password', $targetUser), [
            'password'              => 'BrandNewSecret@2026',
            'password_confirmation' => 'BrandNewSecret@2026',
        ]);

        $response->assertSessionHas('success');
        $targetUser->refresh();
        $this->assertTrue(Hash::check('BrandNewSecret@2026', $targetUser->password));
    }

    public function test_admin_cannot_delete_self()
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_admin_can_delete_another_user()
    {
        $targetUser = User::create([
            'name'      => 'Disposable User',
            'email'     => 'disposable.' . uniqid() . '@drishti.com',
            'password'  => Hash::make('Password@123'),
            'role'      => 'staff',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $targetUser));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }
}

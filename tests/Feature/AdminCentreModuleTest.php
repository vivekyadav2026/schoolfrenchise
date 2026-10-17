<?php

namespace Tests\Feature;

use App\Models\Centre;
use App\Models\CentreWallet;
use App\Models\District;
use App\Models\FranchiseApplication;
use App\Models\Role;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminCentreModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected State $state;
    protected District $district;
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

        $this->state = State::first() ?? State::create(['name' => 'Delhi', 'code' => 'DL', 'status' => true]);
        $this->district = District::firstOrCreate(
            ['state_id' => $this->state->id, 'name' => 'North Delhi'],
            ['status' => true]
        );

        $this->centre = Centre::firstOrCreate(
            ['centre_code' => 'DCE-TEST-MOD10'],
            [
                'centre_name'      => 'Model Test Franchise Centre',
                'director_name'    => 'Sanjay Verma',
                'email'            => 'sanjay.verma@testcentre.com',
                'mobile'           => '9876543210',
                'state_id'         => $this->state->id,
                'district_id'      => $this->district->id,
                'city'             => 'Rohini',
                'address'          => 'Sector 15, Rohini, New Delhi',
                'pincode'          => '110085',
                'lab_capacity'     => 20,
                'affiliation_date' => now()->toDateString(),
                'status'           => 'active',
            ]
        );

        CentreWallet::firstOrCreate(
            ['centre_id' => $this->centre->id],
            ['balance' => 0.00, 'total_credited' => 0.00, 'total_debited' => 0.00, 'status' => 'active']
        );
    }

    public function test_admin_can_view_centres_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.centres.index'));
        $response->assertStatus(200);
        $response->assertSee('Franchise & Branch Management', false);
        $response->assertSee('Model Test Franchise Centre');
        $response->assertSee('DCE-TEST-MOD10');
    }

    public function test_admin_can_view_franchise_applications(): void
    {
        $app = FranchiseApplication::create([
            'application_no'   => 'DCE-APP-TEST-99',
            'applicant_name'   => 'Harish Chandra',
            'centre_name'      => 'Chandra Computer Point',
            'mobile'           => '9811223344',
            'email'            => 'harish@chandra.com',
            'state_id'         => $this->state->id,
            'district_id'      => $this->district->id,
            'city'             => 'Noida',
            'address'          => 'Sector 18, Commercial Belt',
            'qualification'    => 'MCA, B.Tech',
            'computers_count'  => 12,
            'carpet_area_sqft' => 450,
            'status'           => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.centres.applications'));
        $response->assertStatus(200);
        $response->assertSee('Franchise Inward Applications', false);
        $response->assertSee('DCE-APP-TEST-99');
        $response->assertSee('Chandra Computer Point');
    }

    public function test_admin_can_view_single_franchise_application(): void
    {
        $app = FranchiseApplication::create([
            'application_no'   => 'DCE-APP-TEST-98',
            'applicant_name'   => 'Pooja Sharma',
            'centre_name'      => 'Sharma Infotech Academy',
            'mobile'           => '9811223355',
            'email'            => 'pooja@sharma.com',
            'state_id'         => $this->state->id,
            'district_id'      => $this->district->id,
            'city'             => 'Gurugram',
            'address'          => 'Cyber City Hub',
            'qualification'    => 'M.Sc Computer Science',
            'computers_count'  => 15,
            'carpet_area_sqft' => 600,
            'status'           => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.centres.applications.show', $app));
        $response->assertStatus(200);
        $response->assertSee('DCE-APP-TEST-98');
        $response->assertSee('Sharma Infotech Academy');
        $response->assertSee('Pooja Sharma');
    }

    public function test_admin_can_approve_franchise_application_creating_centre_and_wallet(): void
    {
        $app = FranchiseApplication::create([
            'application_no'   => 'DCE-APP-APPROVE-01',
            'applicant_name'   => 'Manish Malhotra',
            'centre_name'      => 'Malhotra Tech Institute',
            'mobile'           => '9877001122',
            'email'            => 'manish.approv@test.com',
            'state_id'         => $this->state->id,
            'district_id'      => $this->district->id,
            'city'             => 'Dwarka',
            'address'          => 'Sector 10, Dwarka, Delhi',
            'qualification'    => 'B.Tech IT',
            'computers_count'  => 10,
            'carpet_area_sqft' => 400,
            'status'           => 'pending',
        ]);

        $payload = [
            'centre_code'  => 'DCE-DWK-01',
            'lab_capacity' => 12,
            'valid_upto'   => now()->addYear()->toDateString(),
            'remarks'      => 'Approved after inspection of laboratory premises.',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.centres.applications.approve', $app), $payload);
        $response->assertRedirect();

        $app->refresh();
        $this->assertEquals('approved', $app->status);

        $this->assertDatabaseHas('centres', [
            'centre_code'   => 'DCE-DWK-01',
            'centre_name'   => 'Malhotra Tech Institute',
            'director_name' => 'Manish Malhotra',
            'email'         => 'manish.approv@test.com',
            'status'        => 'active',
        ]);

        $createdCentre = Centre::where('centre_code', 'DCE-DWK-01')->first();
        $this->assertNotNull($createdCentre);

        $this->assertDatabaseHas('centre_wallets', [
            'centre_id' => $createdCentre->id,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'manish.approv@test.com',
            'role'  => 'centre',
        ]);
    }

    public function test_admin_can_reject_franchise_application_with_reason(): void
    {
        $app = FranchiseApplication::create([
            'application_no'   => 'DCE-APP-REJECT-01',
            'applicant_name'   => 'Rohan Joshi',
            'centre_name'      => 'Joshi Classes',
            'mobile'           => '9877001199',
            'email'            => 'rohan.reject@test.com',
            'state_id'         => $this->state->id,
            'district_id'      => $this->district->id,
            'city'             => 'Faridabad',
            'address'          => 'NIT 1, Faridabad',
            'qualification'    => 'Graduate',
            'computers_count'  => 3,
            'carpet_area_sqft' => 150,
            'status'           => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.centres.applications.reject', $app), [
            'reason' => 'Computer count is insufficient for autonomous affiliation criteria.',
        ]);

        $response->assertRedirect(route('admin.centres.applications'));

        $app->refresh();
        $this->assertEquals('rejected', $app->status);
        $this->assertStringContainsString('insufficient', $app->remarks);
    }

    public function test_admin_can_view_create_centre_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.centres.create'));
        $response->assertStatus(200);
        $response->assertSee('Register New Affiliated Centre', false);
    }

    public function test_admin_can_register_new_centre_directly(): void
    {
        $payload = [
            'centre_code'      => 'DCE-DIRECT-99',
            'centre_name'      => 'Direct Branch Test Institute',
            'director_name'    => 'Anil Aggarwal',
            'email'            => 'anil.aggarwal@directtest.com',
            'mobile'           => '9899112233',
            'state_id'         => $this->state->id,
            'district_id'      => $this->district->id,
            'city'             => 'Janakpuri',
            'address'          => 'District Centre Janakpuri',
            'pincode'          => '110058',
            'lab_capacity'     => 18,
            'affiliation_date' => now()->toDateString(),
            'valid_upto'       => now()->addYear()->toDateString(),
            'status'           => 'active',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.centres.store'), $payload);
        $response->assertRedirect();

        $this->assertDatabaseHas('centres', [
            'centre_code'   => 'DCE-DIRECT-99',
            'centre_name'   => 'Direct Branch Test Institute',
            'director_name' => 'Anil Aggarwal',
        ]);

        $newCentre = Centre::where('centre_code', 'DCE-DIRECT-99')->first();
        $this->assertNotNull($newCentre);

        $this->assertDatabaseHas('centre_wallets', [
            'centre_id' => $newCentre->id,
        ]);
    }

    public function test_admin_can_view_centre_dossier(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.centres.show', $this->centre));
        $response->assertStatus(200);
        $response->assertSee('Model Test Franchise Centre');
        $response->assertSee('Sanjay Verma');
        $response->assertSee('Centre Particulars');
    }

    public function test_admin_can_update_centre_particulars(): void
    {
        $payload = [
            'centre_code'      => $this->centre->centre_code,
            'centre_name'      => 'Updated Franchise Centre Name',
            'director_name'    => 'Sanjay Verma Updated',
            'email'            => $this->centre->email,
            'mobile'           => $this->centre->mobile,
            'state_id'         => $this->state->id,
            'district_id'      => $this->district->id,
            'city'             => 'Rohini West',
            'address'          => 'Updated Address Sector 16',
            'pincode'          => '110089',
            'lab_capacity'     => 25,
            'affiliation_date' => $this->centre->affiliation_date->toDateString(),
            'status'           => 'active',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.centres.update', $this->centre), $payload);
        $response->assertRedirect();

        $this->centre->refresh();
        $this->assertEquals('Updated Franchise Centre Name', $this->centre->centre_name);
        $this->assertEquals(25, $this->centre->lab_capacity);
    }

    public function test_admin_can_toggle_centre_status(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.centres.toggle-status', $this->centre), [
            'status' => 'suspended',
        ]);

        $response->assertRedirect();

        $this->centre->refresh();
        $this->assertEquals('suspended', $this->centre->status);
    }

    public function test_admin_can_view_franchise_affiliation_certificate(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.centres.certificate', $this->centre));
        $response->assertStatus(200);
        $response->assertSee('Certificate of Affiliation', false);
        $response->assertSee($this->centre->centre_name);
        $response->assertSee($this->centre->centre_code);
    }

    public function test_admin_can_archive_centre(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.centres.destroy', $this->centre));
        $response->assertRedirect(route('admin.centres.index'));

        $this->assertSoftDeleted('centres', [
            'id' => $this->centre->id,
        ]);
    }
}

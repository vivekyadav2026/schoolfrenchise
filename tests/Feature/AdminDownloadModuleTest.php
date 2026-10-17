<?php

namespace Tests\Feature;

use App\Models\Centre;
use App\Models\Download;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminDownloadModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected Centre $centre;
    protected Download $download;

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
            'centre_code'      => 'DCE-TEST-DL01',
            'centre_name'      => 'Download Test Centre',
            'director_name'    => 'Director DL',
            'email'            => 'dlcentre@drishti.com',
            'mobile'           => '9888877775',
            'state_id'         => 1,
            'district_id'      => 1,
            'city'             => 'Delhi',
            'address'          => 'Test Address',
            'pincode'          => '110001',
            'affiliation_date' => now()->toDateString(),
            'status'           => 'active',
        ]);

        $this->download = Download::firstOrCreate(
            ['slug' => 'test-official-prospectus'],
            [
                'title'           => 'Test Official Prospectus 2026',
                'category'        => 'prospectus',
                'file_path'       => 'uploads/downloads/test_prospectus.pdf',
                'file_size'       => '1.5 MB',
                'version'         => 'v1.0',
                'description'     => 'Complete course curriculum and prospectus handbook.',
                'downloads_count' => 10,
                'is_free'         => true,
                'price'           => 0.00,
                'status'          => true,
            ]
        );
    }

    public function test_admin_can_view_downloads_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.downloads.index'));
        $response->assertStatus(200);
        $response->assertSee('Downloads & Orders Management', false);
        $response->assertSee('Test Official Prospectus 2026');
    }

    public function test_admin_can_view_create_download_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.downloads.create'));
        $response->assertStatus(200);
        $response->assertSee('Upload New Downloadable Resource', false);
    }

    public function test_admin_can_store_download_resource_with_file(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('course_handbook.pdf', 800, 'application/pdf');

        $payload = [
            'title'       => 'New Student Course Handbook',
            'category'    => 'study_note',
            'file'        => $file,
            'version'     => 'v2.1',
            'is_free'     => 1,
            'price'       => 0,
            'description' => 'Comprehensive study handbook for IT students.',
            'status'      => 1,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.downloads.store'), $payload);
        $response->assertRedirect(route('admin.downloads.index'));

        $this->assertDatabaseHas('downloads', [
            'title'    => 'New Student Course Handbook',
            'category' => 'study_note',
            'version'  => 'v2.1',
            'is_free'  => 1,
            'status'   => 1,
        ]);

        $created = Download::where('title', 'New Student Course Handbook')->first();
        $this->assertNotNull($created);
        Storage::disk('public')->assertExists($created->file_path);
    }

    public function test_admin_can_view_edit_download_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.downloads.edit', $this->download));
        $response->assertStatus(200);
        $response->assertSee('Test Official Prospectus 2026');
        $response->assertSee('Edit Resource', false);
    }

    public function test_admin_can_update_download_resource(): void
    {
        $payload = [
            'title'       => 'Updated Official Prospectus 2026-27',
            'category'    => 'prospectus',
            'version'     => 'v3.0',
            'is_free'     => 1,
            'price'       => 0,
            'description' => 'Updated prospectus description text.',
            'status'      => 1,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.downloads.update', $this->download), $payload);
        $response->assertRedirect(route('admin.downloads.index'));

        $this->download->refresh();
        $this->assertEquals('Updated Official Prospectus 2026-27', $this->download->title);
        $this->assertEquals('v3.0', $this->download->version);
    }

    public function test_admin_can_toggle_download_publish_status(): void
    {
        $currentStatus = $this->download->status;

        $response = $this->actingAs($this->admin)->post(route('admin.downloads.toggle-status', $this->download));
        $response->assertRedirect();

        $this->download->refresh();
        $this->assertEquals(!$currentStatus, (bool) $this->download->status);
    }

    public function test_admin_can_download_file_and_increment_counter(): void
    {
        Storage::fake('public');
        $filePath = 'uploads/downloads/test_sample_file.pdf';
        Storage::disk('public')->put($filePath, 'Fake PDF Content for Testing');

        $testDownload = Download::create([
            'title'           => 'Test Download Counter File',
            'slug'            => 'test-download-counter-file',
            'category'        => 'form',
            'file_path'       => $filePath,
            'file_size'       => '25 KB',
            'version'         => 'v1.0',
            'downloads_count' => 5,
            'is_free'         => true,
            'price'           => 0.00,
            'status'          => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.downloads.download-file', $testDownload));
        $response->assertStatus(200);

        $testDownload->refresh();
        $this->assertEquals(6, $testDownload->downloads_count);
    }

    public function test_admin_can_archive_download_resource(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.downloads.destroy', $this->download));
        $response->assertRedirect(route('admin.downloads.index'));

        $this->assertSoftDeleted('downloads', [
            'id' => $this->download->id,
        ]);
    }

    public function test_admin_can_view_orders_index_with_strict_accounting(): void
    {
        // Completed Order
        Order::create([
            'order_no'        => 'ORD-TEST-COMP-01',
            'centre_id'       => $this->centre->id,
            'item_type'       => 'kit',
            'item_name'       => '50 Sets Student Welcome Kit',
            'amount'          => 7500.00,
            'payment_status'  => 'completed',
            'payment_method'  => 'online',
            'transaction_ref' => 'TXN-ORD-001',
        ]);

        // Pending Order
        Order::create([
            'order_no'        => 'ORD-TEST-PEND-01',
            'centre_id'       => $this->centre->id,
            'item_type'       => 'certificates',
            'item_name'       => '100 Certificate Blank Sheets',
            'amount'          => 3000.00,
            'payment_status'  => 'pending',
            'payment_method'  => 'upi',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.downloads.orders'));
        $response->assertStatus(200);
        $response->assertSee('Resource & Franchise Orders', false);
        $response->assertSee('ORD-TEST-COMP-01');
        $response->assertSee('ORD-TEST-PEND-01');
    }

    public function test_admin_can_view_create_order_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.downloads.orders.create'));
        $response->assertStatus(200);
        $response->assertSee('Log Resource / Kit Order', false);
    }

    public function test_admin_can_log_new_order(): void
    {
        $payload = [
            'centre_id'       => $this->centre->id,
            'item_type'       => 'stationery',
            'item_name'       => 'Promotional Flex Banners & Flyers Pack',
            'amount'          => 4500.00,
            'payment_status'  => 'completed',
            'payment_method'  => 'bank_transfer',
            'transaction_ref' => 'NEFT-20261017001',
            'notes'           => 'Dispatch via DTDC courier.',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.downloads.orders.store'), $payload);
        $response->assertRedirect(route('admin.downloads.orders'));

        $this->assertDatabaseHas('orders', [
            'centre_id'       => $this->centre->id,
            'item_type'       => 'stationery',
            'item_name'       => 'Promotional Flex Banners & Flyers Pack',
            'amount'          => 4500.00,
            'payment_status'  => 'completed',
            'transaction_ref' => 'NEFT-20261017001',
        ]);
    }

    public function test_admin_can_view_order_dossier(): void
    {
        $order = Order::create([
            'order_no'        => 'ORD-TEST-DOSSIER',
            'centre_id'       => $this->centre->id,
            'item_type'       => 'kit',
            'item_name'       => 'Sample Kit Test',
            'amount'          => 2500.00,
            'payment_status'  => 'pending',
            'payment_method'  => 'wallet',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.downloads.orders.show', $order));
        $response->assertStatus(200);
        $response->assertSee('ORD-TEST-DOSSIER');
        $response->assertSee('Sample Kit Test');
        $response->assertSee('Order Particulars');
    }

    public function test_admin_can_update_order_payment_status(): void
    {
        $order = Order::create([
            'order_no'        => 'ORD-TEST-UPDATE-STATUS',
            'centre_id'       => $this->centre->id,
            'item_type'       => 'certificates',
            'item_name'       => 'Certificate Blank Order',
            'amount'          => 1800.00,
            'payment_status'  => 'pending',
            'payment_method'  => 'upi',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.downloads.orders.status', $order), [
            'payment_status' => 'completed',
            'notes'          => 'UPI payment received and confirmed.',
        ]);

        $response->assertRedirect();

        $order->refresh();
        $this->assertEquals('completed', $order->payment_status);
        $this->assertStringContainsString('UPI payment received', $order->notes);
    }
}

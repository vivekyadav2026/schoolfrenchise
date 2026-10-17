<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\Gallery;
use App\Models\Notice;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCmsModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

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
    }

    public function test_guest_cannot_access_cms()
    {
        $response = $this->get(route('admin.cms.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_cms_overview_dashboard()
    {
        Notice::create([
            'title'           => 'Special National Scholarship Notice',
            'content'         => 'Scholarship examination announced for all enrolled students.',
            'target_audience' => 'students',
            'is_marquee'      => true,
            'status'          => true,
            'published_at'    => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.cms.index'));

        $response->assertStatus(200);
        $response->assertSee('Website Content Management');
        $response->assertSee('Special National Scholarship Notice');
    }

    public function test_admin_can_view_notices_list()
    {
        $notice = Notice::create([
            'title'           => 'Holiday Notice for Diwali 2026',
            'content'         => 'All regional centres will remain closed.',
            'target_audience' => 'centres',
            'is_marquee'      => false,
            'status'          => true,
            'published_at'    => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.cms.notices'));

        $response->assertStatus(200);
        $response->assertSee('Announcements &amp; Marquee Notices', false);
        $response->assertSee('Holiday Notice for Diwali 2026');
    }

    public function test_admin_can_view_create_notice_form()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.cms.notices.create'));

        $response->assertStatus(200);
        $response->assertSee('Post New Announcement');
    }

    public function test_admin_can_store_notice_with_audit_log()
    {
        $payload = [
            'title'           => 'Admission Open for Winter Batch 2026',
            'content'         => 'Admissions are now open across all affiliated study centres.',
            'target_audience' => 'public',
            'is_marquee'      => '1',
            'status'          => '1',
            'published_at'    => now()->toDateString(),
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.cms.notices.store'), $payload);

        $response->assertRedirect(route('admin.cms.notices'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('notices', [
            'title'           => 'Admission Open for Winter Batch 2026',
            'target_audience' => 'public',
            'is_marquee'      => 1,
            'status'          => 1,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action'  => 'notice_created',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_edit_and_update_notice()
    {
        $notice = Notice::create([
            'title'           => 'Old Examination Schedule',
            'content'         => 'Tentative schedule.',
            'target_audience' => 'students',
            'is_marquee'      => false,
            'status'          => true,
            'published_at'    => now()->toDateString(),
        ]);

        $editResponse = $this->actingAs($this->admin)->get(route('admin.cms.notices.edit', $notice));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Old Examination Schedule');

        $updatePayload = [
            'title'           => 'Revised Final Examination Schedule',
            'content'         => 'Finalized datesheet for December 2026 semester.',
            'target_audience' => 'all',
            'is_marquee'      => '1',
            'status'          => '1',
            'published_at'    => now()->toDateString(),
        ];

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.cms.notices.update', $notice), $updatePayload);
        $updateResponse->assertRedirect(route('admin.cms.notices'));

        $this->assertDatabaseHas('notices', [
            'id'    => $notice->id,
            'title' => 'Revised Final Examination Schedule',
        ]);
    }

    public function test_admin_can_toggle_notice_status()
    {
        $notice = Notice::create([
            'title'           => 'Active Toggle Notice',
            'target_audience' => 'all',
            'status'          => true,
            'published_at'    => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.cms.notices.toggle-status', $notice));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('notices', [
            'id'     => $notice->id,
            'status' => 0,
        ]);
    }

    public function test_admin_can_delete_notice()
    {
        $notice = Notice::create([
            'title'           => 'Temporary Notice for Removal',
            'target_audience' => 'public',
            'published_at'    => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.cms.notices.destroy', $notice));

        $response->assertRedirect(route('admin.cms.notices'));
        $this->assertDatabaseMissing('notices', ['id' => $notice->id]);
    }

    public function test_admin_can_view_gallery_and_create_photo()
    {
        Storage::fake('public');

        $listResponse = $this->actingAs($this->admin)->get(route('admin.cms.gallery'));
        $listResponse->assertStatus(200);
        $listResponse->assertSee('Photo Gallery');

        $createResponse = $this->actingAs($this->admin)->get(route('admin.cms.gallery.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Upload Photo to Gallery');

        $fakeImage = UploadedFile::fake()->image('lab_photos.jpg', 800, 600);

        $payload = [
            'title'      => 'State of the Art Advanced Coding Lab',
            'category'   => 'Computer Lab',
            'sort_order' => 2,
            'status'     => '1',
            'image'      => $fakeImage,
        ];

        $postResponse = $this->actingAs($this->admin)->post(route('admin.cms.gallery.store'), $payload);

        $postResponse->assertRedirect(route('admin.cms.gallery'));
        $this->assertDatabaseHas('galleries', [
            'title'    => 'State of the Art Advanced Coding Lab',
            'category' => 'Computer Lab',
        ]);
    }

    public function test_admin_can_edit_gallery_item()
    {
        $gallery = Gallery::create([
            'title'      => 'Annual Day 2026',
            'category'   => 'Events',
            'image_path' => 'uploads/gallery/test_photo.jpg',
            'sort_order' => 1,
            'status'     => true,
        ]);

        $editResponse = $this->actingAs($this->admin)->get(route('admin.cms.gallery.edit', $gallery));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Annual Day 2026');

        $updatePayload = [
            'title'      => 'Grand Annual Day & Convocation 2026',
            'category'   => 'Convocation',
            'sort_order' => 5,
            'status'     => '1',
        ];

        $putResponse = $this->actingAs($this->admin)->put(route('admin.cms.gallery.update', $gallery), $updatePayload);
        $putResponse->assertRedirect(route('admin.cms.gallery'));

        $this->assertDatabaseHas('galleries', [
            'id'       => $gallery->id,
            'title'    => 'Grand Annual Day & Convocation 2026',
            'category' => 'Convocation',
        ]);
    }

    public function test_admin_can_toggle_and_delete_gallery_item()
    {
        $gallery = Gallery::create([
            'title'      => 'Photo to Delete',
            'category'   => 'Campus',
            'image_path' => 'uploads/gallery/temp.jpg',
            'sort_order' => 1,
            'status'     => true,
        ]);

        $this->actingAs($this->admin)->post(route('admin.cms.gallery.toggle-status', $gallery));
        $this->assertDatabaseHas('galleries', ['id' => $gallery->id, 'status' => 0]);

        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.cms.gallery.destroy', $gallery));
        $deleteResponse->assertRedirect(route('admin.cms.gallery'));
        $this->assertDatabaseMissing('galleries', ['id' => $gallery->id]);
    }

    public function test_admin_can_view_enquiries_and_update_lifecycle_status()
    {
        $enquiry = Enquiry::create([
            'name'    => 'Rajesh Sharma',
            'mobile'  => '9876500112',
            'email'   => 'rajesh@example.com',
            'subject' => 'Inquiry for Python & AI Master Diploma',
            'message' => 'Please share course syllabus and fee details.',
            'type'    => 'admission_enquiry',
            'status'  => 'new',
        ]);

        $listResponse = $this->actingAs($this->admin)->get(route('admin.cms.enquiries'));
        $listResponse->assertStatus(200);
        $listResponse->assertSee('Rajesh Sharma');
        $listResponse->assertSee('9876500112');

        $showResponse = $this->actingAs($this->admin)->get(route('admin.cms.enquiries.show', $enquiry));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Inquiry for Python &amp; AI Master Diploma', false);

        $statusPayload = ['status' => 'contacted'];
        $putResponse = $this->actingAs($this->admin)->put(route('admin.cms.enquiries.update', $enquiry), $statusPayload);
        $putResponse->assertSessionHas('success');

        $this->assertDatabaseHas('enquiries', [
            'id'     => $enquiry->id,
            'status' => 'contacted',
        ]);
    }

    public function test_admin_can_delete_enquiry()
    {
        $enquiry = Enquiry::create([
            'name'    => 'Spam Lead',
            'mobile'  => '9000000000',
            'subject' => 'Spam Subject',
            'type'    => 'general_contact',
            'status'  => 'new',
        ]);

        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.cms.enquiries.destroy', $enquiry));
        $deleteResponse->assertRedirect(route('admin.cms.enquiries'));
        $this->assertDatabaseMissing('enquiries', ['id' => $enquiry->id]);
    }

    public function test_admin_can_view_and_update_cms_content_settings()
    {
        $contentResponse = $this->actingAs($this->admin)->get(route('admin.cms.content'));
        $contentResponse->assertStatus(200);
        $contentResponse->assertSee('Page Settings');

        $settingsPayload = [
            'site_helpline'          => '+91 99999 88888',
            'site_whatsapp'          => '+91 99999 88888',
            'site_email'             => 'contact@drishticomputer.com',
            'site_office_hours'      => 'Mon - Sat: 9:00 AM - 7:00 PM',
            'total_franchises_count' => '12,500+',
            'total_states_count'     => '29+',
            'total_cities_count'     => '800+',
            'total_courses_count'    => '400+',
            'hero_title'             => 'National Leader in Computer & IT Education',
            'hero_subtitle'          => 'Join over 5,00,000 students certified nationwide.',
            'social_facebook'        => 'https://facebook.com/drishticomputer',
        ];

        $postResponse = $this->actingAs($this->admin)->post(route('admin.cms.content.update'), $settingsPayload);
        $postResponse->assertSessionHas('success');

        $this->assertEquals('+91 99999 88888', Setting::get('site_helpline'));
        $this->assertEquals('12,500+', Setting::get('total_franchises_count'));
        $this->assertEquals('National Leader in Computer & IT Education', Setting::get('hero_title'));
    }
}

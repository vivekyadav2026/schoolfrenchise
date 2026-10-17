<?php

namespace Tests\Feature;

use App\Models\Centre;
use App\Models\Course;
use App\Models\CourseSubject;
use App\Models\Role;
use App\Models\StudyMaterial;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMaterialModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected Course $course;
    protected CourseSubject $subject;
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
            'centre_code'   => 'DCE-TEST-MAT',
            'centre_name'   => 'Material Test Centre',
            'director_name' => 'Test Director',
            'email'         => 'matcentre@drishti.com',
            'mobile'        => '9888877773',
            'address'       => 'Test Address',
            'state_id'      => 1,
            'district_id'   => 1,
            'status'        => 'active',
        ]);

        $this->course = Course::firstOrCreate(
            ['course_code' => 'MAT-COURSE-01'],
            [
                'course_name'        => 'Study Material Test Course',
                'slug'               => 'study-material-test-course',
                'duration'           => '6 Months',
                'eligibility'        => '10th Standard',
                'default_course_fee' => 4000.00,
                'status'             => true,
            ]
        );

        $this->subject = CourseSubject::firstOrCreate(
            ['course_id' => $this->course->id, 'subject_code' => 'MAT-SUB-01'],
            [
                'subject_name'   => 'Computing Notes Module',
                'theory_max'     => 70,
                'practical_max'  => 20,
                'viva_max'       => 10,
                'total_max'      => 100,
                'min_pass_marks' => 40,
                'sort_order'     => 1,
            ]
        );
    }

    public function test_admin_can_view_materials_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.materials.index'));
        $response->assertStatus(200);
        $response->assertSee('Study Materials & E-Library', false);
    }

    public function test_admin_can_view_create_material_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.materials.create'));
        $response->assertStatus(200);
        $response->assertSee('Upload New Study Material');
    }

    public function test_admin_can_upload_pdf_study_material(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('operating_systems_notes.pdf', 500, 'application/pdf');

        $payload = [
            'title'             => 'Operating Systems Chapter 1 Notes',
            'course_id'         => $this->course->id,
            'course_subject_id' => $this->subject->id,
            'material_type'     => 'pdf',
            'file'              => $file,
            'description'       => 'Complete chapter 1 overview with review questions.',
            'status'            => 1,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.materials.store'), $payload);
        $response->assertRedirect(route('admin.materials.index'));

        $this->assertDatabaseHas('study_materials', [
            'title'         => 'Operating Systems Chapter 1 Notes',
            'material_type' => 'pdf',
            'course_id'     => $this->course->id,
            'status'        => 1,
        ]);
    }

    public function test_admin_can_create_video_lecture_material(): void
    {
        $payload = [
            'title'         => 'Advanced Python Video Lecture - OOP Concepts',
            'course_id'     => $this->course->id,
            'material_type' => 'video',
            'external_url'  => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'description'   => 'Video lecture explaining classes and objects.',
            'status'        => 1,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.materials.store'), $payload);
        $response->assertRedirect(route('admin.materials.index'));

        $this->assertDatabaseHas('study_materials', [
            'title'         => 'Advanced Python Video Lecture - OOP Concepts',
            'material_type' => 'video',
            'external_url'  => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);
    }

    public function test_admin_can_view_edit_and_update_material(): void
    {
        $material = StudyMaterial::create([
            'title'         => 'Initial Lecture Slides',
            'slug'          => 'initial-lecture-slides-' . uniqid(),
            'course_id'     => $this->course->id,
            'material_type' => 'notes',
            'description'   => 'Initial description',
            'uploaded_by'   => $this->admin->id,
            'status'        => true,
        ]);

        $showResponse = $this->actingAs($this->admin)->get(route('admin.materials.show', $material));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($material->title);

        $editResponse = $this->actingAs($this->admin)->get(route('admin.materials.edit', $material));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Modify Resource Details');

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.materials.update', $material), [
            'title'         => 'Updated Lecture Slides 2026',
            'course_id'     => $this->course->id,
            'material_type' => 'notes',
            'description'   => 'Updated comprehensive slides.',
            'status'        => 1,
        ]);

        $updateResponse->assertRedirect(route('admin.materials.index'));
        $this->assertDatabaseHas('study_materials', [
            'id'    => $material->id,
            'title' => 'Updated Lecture Slides 2026',
        ]);
    }

    public function test_admin_can_toggle_material_status(): void
    {
        $material = StudyMaterial::create([
            'title'         => 'Sample Mock Paper 2026',
            'slug'          => 'sample-mock-paper-2026-' . uniqid(),
            'course_id'     => $this->course->id,
            'material_type' => 'sample_paper',
            'uploaded_by'   => $this->admin->id,
            'status'        => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.materials.toggle-status', $material));
        $response->assertRedirect();

        $material->refresh();
        $this->assertFalse((bool) $material->status);
    }

    public function test_admin_can_delete_material(): void
    {
        $material = StudyMaterial::create([
            'title'         => 'Outdated Syllabus Document',
            'slug'          => 'outdated-syllabus-document-' . uniqid(),
            'course_id'     => $this->course->id,
            'material_type' => 'syllabus',
            'uploaded_by'   => $this->admin->id,
            'status'        => false,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.materials.destroy', $material));
        $response->assertRedirect(route('admin.materials.index'));

        $this->assertDatabaseMissing('study_materials', [
            'id' => $material->id,
        ]);
    }
}

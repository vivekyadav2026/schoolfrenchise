<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\Course;
use App\Models\CourseSubject;
use App\Models\StudyMaterial;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminMaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = StudyMaterial::with(['course', 'subject', 'centre', 'uploader']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('material_type')) {
            $query->where('material_type', $request->material_type);
        }

        if ($request->filled('status')) {
            $query->where('status', (bool) $request->status);
        }

        $materials = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'     => StudyMaterial::count(),
            'pdf'       => StudyMaterial::where('material_type', 'pdf')->count(),
            'video'     => StudyMaterial::where('material_type', 'video')->count(),
            'notes'     => StudyMaterial::whereIn('material_type', ['notes', 'sample_paper', 'syllabus'])->count(),
            'published' => StudyMaterial::where('status', true)->count(),
        ];

        $courses = Course::where('status', true)->orderBy('course_name')->get();

        return view('admin.materials.index', compact('materials', 'stats', 'courses'));
    }

    public function create()
    {
        $courses = Course::with('subjects')->where('status', true)->orderBy('course_name')->get();
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.materials.create', compact('courses', 'centres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:191',
            'course_id'         => 'required|exists:courses,id',
            'course_subject_id' => 'nullable|exists:course_subjects,id',
            'centre_id'         => 'nullable|exists:centres,id',
            'material_type'     => 'required|in:pdf,video,notes,syllabus,sample_paper,link',
            'file'              => 'nullable|file|max:20480',
            'external_url'      => 'nullable|url|max:255',
            'description'       => 'nullable|string',
            'status'            => 'nullable|boolean',
        ]);

        $filePath = null;
        $fileSize = null;

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $filePath = FileUploadService::uploadMaterial($uploadedFile);
            $bytes = $uploadedFile->getSize();
            $fileSize = $bytes >= 1048576 
                ? round($bytes / 1048576, 2) . ' MB' 
                : round($bytes / 1024, 2) . ' KB';
        }

        $slug = Str::slug($validated['title']) . '-' . Str::lower(Str::random(6));

        $material = DB::transaction(function () use ($validated, $slug, $filePath, $fileSize, $request) {
            $material = StudyMaterial::create([
                'title'             => $validated['title'],
                'slug'              => $slug,
                'course_id'         => $validated['course_id'],
                'course_subject_id' => $validated['course_subject_id'] ?? null,
                'centre_id'         => $validated['centre_id'] ?? null,
                'material_type'     => $validated['material_type'],
                'file_path'         => $filePath,
                'external_url'      => $validated['external_url'] ?? null,
                'file_size'         => $fileSize,
                'description'       => $validated['description'] ?? null,
                'uploaded_by'       => Auth::id(),
                'status'            => $request->has('status'),
            ]);

            AuditLog::record(
                'study_material_created',
                $material,
                [],
                $material->toArray()
            );

            return $material;
        });

        return redirect()->route('admin.materials.index')
            ->with('success', "Study material '{$material->title}' created successfully.");
    }

    public function show(StudyMaterial $material)
    {
        $material->load(['course.subjects', 'subject', 'centre', 'uploader']);
        return view('admin.materials.show', compact('material'));
    }

    public function edit(StudyMaterial $material)
    {
        $courses = Course::with('subjects')->where('status', true)->orderBy('course_name')->get();
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.materials.edit', compact('material', 'courses', 'centres'));
    }

    public function update(Request $request, StudyMaterial $material)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:191',
            'course_id'         => 'required|exists:courses,id',
            'course_subject_id' => 'nullable|exists:course_subjects,id',
            'centre_id'         => 'nullable|exists:centres,id',
            'material_type'     => 'required|in:pdf,video,notes,syllabus,sample_paper,link',
            'file'              => 'nullable|file|max:20480',
            'external_url'      => 'nullable|url|max:255',
            'description'       => 'nullable|string',
            'status'            => 'nullable|boolean',
        ]);

        $filePath = $material->file_path;
        $fileSize = $material->file_size;

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $filePath = FileUploadService::uploadMaterial($uploadedFile);
            $bytes = $uploadedFile->getSize();
            $fileSize = $bytes >= 1048576 
                ? round($bytes / 1048576, 2) . ' MB' 
                : round($bytes / 1024, 2) . ' KB';
        }

        $oldData = $material->toArray();

        DB::transaction(function () use ($material, $validated, $filePath, $fileSize, $request, $oldData) {
            $material->update([
                'title'             => $validated['title'],
                'course_id'         => $validated['course_id'],
                'course_subject_id' => $validated['course_subject_id'] ?? null,
                'centre_id'         => $validated['centre_id'] ?? null,
                'material_type'     => $validated['material_type'],
                'file_path'         => $filePath,
                'external_url'      => $validated['external_url'] ?? null,
                'file_size'         => $fileSize,
                'description'       => $validated['description'] ?? null,
                'status'            => $request->has('status'),
            ]);

            AuditLog::record(
                'study_material_updated',
                $material,
                $oldData,
                $material->toArray()
            );
        });

        return redirect()->route('admin.materials.index')
            ->with('success', "Study material '{$material->title}' updated successfully.");
    }

    public function destroy(StudyMaterial $material)
    {
        DB::transaction(function () use ($material) {
            AuditLog::record(
                'study_material_deleted',
                $material,
                $material->toArray(),
                []
            );

            if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }

            $material->delete();
        });

        return redirect()->route('admin.materials.index')
            ->with('success', 'Study material removed successfully.');
    }

    public function toggleStatus(StudyMaterial $material)
    {
        $old = $material->status;
        $material->status = !$old;
        $material->save();

        AuditLog::record(
            'study_material_status_toggled',
            $material,
            ['status' => $old],
            ['status' => $material->status]
        );

        $state = $material->status ? 'published' : 'unpublished';
        return back()->with('success', "Material '{$material->title}' is now {$state}.");
    }
}

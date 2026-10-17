@extends('layouts.admin')

@section('title', $material->title . ' - Study Material')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0" style="color: #0f172a;">{{ $material->title }}</h4>
                @if($material->status)
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-check me-1"></i>Published</span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1"><i class="fa-solid fa-eye-slash me-1"></i>Draft</span>
                @endif
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.materials.index') }}">Study Materials</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($material->title, 25) }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.materials.edit', $material) }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-pen me-1"></i> Edit Resource
            </a>
            <a href="{{ route('admin.materials.index') }}" class="btn btn-outline-dark">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to All
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Resource Details & Description -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-circle-info me-2 text-primary"></i>Resource Synopsis & Contents
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <h6 class="fw-semibold text-muted text-uppercase small">Overview & Description</h6>
                        <div class="p-3 bg-light rounded-3 text-secondary">
                            {{ $material->description ?: 'No detailed description provided for this resource.' }}
                        </div>
                    </div>

                    <!-- Curriculum Alignment -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <div class="text-muted small fw-semibold text-uppercase">Target Course</div>
                                <div class="fw-bold text-dark fs-6 mt-1">{{ $material->course->course_name ?? 'N/A' }}</div>
                                <span class="badge bg-light text-secondary border font-monospace mt-1">{{ $material->course->course_code ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3">
                                <div class="text-muted small fw-semibold text-uppercase">Subject Module</div>
                                <div class="fw-bold text-dark fs-6 mt-1">{{ $material->subject->subject_name ?? 'All Modules / General' }}</div>
                                <span class="badge bg-light text-secondary border font-monospace mt-1">{{ $material->subject->subject_code ?? 'GENERAL' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Resource Actions & Meta -->
        <div class="col-lg-4">
            <!-- Access & Download Box -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-download me-2 text-primary"></i>Access Digital File
                    </h6>
                </div>
                <div class="card-body p-4 text-center">
                    @if($material->material_type === 'pdf')
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger mb-3" style="width: 64px; height: 64px;">
                            <i class="fa-solid fa-file-pdf fs-2"></i>
                        </div>
                    @elseif($material->material_type === 'video')
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary mb-3" style="width: 64px; height: 64px;">
                            <i class="fa-solid fa-video fs-2"></i>
                        </div>
                    @else
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-info-subtle text-info mb-3" style="width: 64px; height: 64px;">
                            <i class="fa-solid fa-file-lines fs-2"></i>
                        </div>
                    @endif

                    <h6 class="fw-bold text-dark mb-1 text-uppercase">{{ $material->material_type }}</h6>
                    <div class="text-muted small mb-3">File Size: {{ $material->file_size ?: 'Online Stream' }}</div>

                    @if($material->file_path)
                        <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="btn btn-primary w-100 py-2" style="background-color: #0891b2; border-color: #0891b2;">
                            <i class="fa-solid fa-download me-1"></i> Download / View File
                        </a>
                    @elseif($material->external_url)
                        <a href="{{ $material->external_url }}" target="_blank" class="btn btn-outline-primary w-100 py-2">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Online Resource
                        </a>
                    @else
                        <div class="alert alert-secondary py-2 small mb-0">No file or URL linked</div>
                    @endif
                </div>
            </div>

            <!-- Meta Particulars Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-shield-halved me-2 text-primary"></i>Permissions & Audit
                    </h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Access Scope:</span>
                            <span class="fw-semibold text-dark">{{ $material->centre->centre_name ?? 'All Authorized Centres' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Uploaded By:</span>
                            <span class="fw-semibold text-dark">{{ $material->uploader->name ?? 'Administrator' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Uploaded On:</span>
                            <span class="fw-semibold text-dark">{{ $material->created_at->format('d M, Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Status:</span>
                            <span class="badge {{ $material->status ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                {{ $material->status ? 'Published' : 'Draft' }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

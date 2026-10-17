@extends('layouts.admin')

@section('title', 'Study Materials Management')

@section('content')
<div class="container-fluid p-0">
    <!-- Header with Action Buttons -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a;">
                <i class="fa-solid fa-file-pdf me-2" style="color: #0891b2;"></i>Study Materials & E-Library
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Study Materials</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.materials.create') }}" class="btn btn-primary" style="background-color: #0891b2; border-color: #0891b2;">
                <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Material
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Resources</div>
                        <h3 class="fw-bold my-1" style="color: #0f172a;">{{ number_format($stats['total']) }}</h3>
                        <div class="small text-muted">All learning assets</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #e0f2fe; color: #0284c7;">
                        <i class="fa-solid fa-folder-open fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">PDF Documents</div>
                        <h3 class="fw-bold my-1 text-danger">{{ number_format($stats['pdf']) }}</h3>
                        <div class="small text-muted">Books & module notes</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fee2e2; color: #dc2626;">
                        <i class="fa-solid fa-file-pdf fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Video Lectures</div>
                        <h3 class="fw-bold my-1 text-primary">{{ number_format($stats['video']) }}</h3>
                        <div class="small text-muted">Tutorials & classes</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #eff6ff; color: #2563eb;">
                        <i class="fa-solid fa-video fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Published Active</div>
                        <h3 class="fw-bold my-1 text-success">{{ number_format($stats['published']) }}</h3>
                        <div class="small text-muted">Accessible to students</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #dcfce7; color: #16a34a;">
                        <i class="fa-solid fa-circle-check fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.materials.index') }}" class="row g-2">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Search title, description..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="course_id" class="form-select bg-light">
                        <option value="">All Courses</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->course_name }} ({{ $c->course_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="material_type" class="form-select bg-light">
                        <option value="">All Types</option>
                        <option value="pdf" {{ request('material_type') === 'pdf' ? 'selected' : '' }}>PDF Document</option>
                        <option value="video" {{ request('material_type') === 'video' ? 'selected' : '' }}>Video Lecture</option>
                        <option value="notes" {{ request('material_type') === 'notes' ? 'selected' : '' }}>Lecture Notes</option>
                        <option value="sample_paper" {{ request('material_type') === 'sample_paper' ? 'selected' : '' }}>Sample Paper</option>
                        <option value="syllabus" {{ request('material_type') === 'syllabus' ? 'selected' : '' }}>Syllabus Copy</option>
                        <option value="link" {{ request('material_type') === 'link' ? 'selected' : '' }}>External Web Link</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100" style="background-color: #0891b2; border-color: #0891b2;">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.materials.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Materials Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-book-bookmark me-2 text-primary"></i>Digital Study Materials Directory
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3" style="width: 35%;">Title & Resource</th>
                        <th>Target Course</th>
                        <th>Subject Paper</th>
                        <th>File Size / Link</th>
                        <th>Access Scope</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $mat)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    @if($mat->material_type === 'pdf')
                                        <div class="rounded-2 p-2 bg-danger-subtle text-danger"><i class="fa-solid fa-file-pdf fs-5"></i></div>
                                    @elseif($mat->material_type === 'video')
                                        <div class="rounded-2 p-2 bg-primary-subtle text-primary"><i class="fa-solid fa-file-video fs-5"></i></div>
                                    @elseif($mat->material_type === 'sample_paper')
                                        <div class="rounded-2 p-2 bg-warning-subtle text-warning"><i class="fa-solid fa-file-lines fs-5"></i></div>
                                    @else
                                        <div class="rounded-2 p-2 bg-info-subtle text-info"><i class="fa-solid fa-file fs-5"></i></div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark">{{ $mat->title }}</div>
                                        <small class="text-muted text-uppercase font-monospace">{{ $mat->material_type }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary fw-semibold">
                                    {{ $mat->course->course_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-secondary">{{ $mat->subject->subject_name ?? 'All Modules / General' }}</small>
                            </td>
                            <td>
                                @if($mat->file_path)
                                    <span class="badge bg-light text-dark border font-monospace"><i class="fa-solid fa-hard-drive me-1"></i>{{ $mat->file_size ?: 'Uploaded' }}</span>
                                @elseif($mat->external_url)
                                    <a href="{{ $mat->external_url }}" target="_blank" class="small text-decoration-none">
                                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>External Link
                                    </a>
                                @else
                                    <span class="text-muted small">None</span>
                                @endif
                            </td>
                            <td>
                                @if($mat->centre_id)
                                    <span class="badge bg-light text-secondary border">{{ $mat->centre->centre_name ?? 'Specific Centre' }}</span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fa-solid fa-globe me-1"></i>All Enrolled Students</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.materials.toggle-status', $mat) }}" class="d-inline">
                                    @csrf
                                    @if($mat->status)
                                        <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none" title="Click to Unpublish">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-check me-1"></i>Published</span>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none" title="Click to Publish">
                                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1"><i class="fa-solid fa-eye-slash me-1"></i>Draft</span>
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.materials.show', $mat) }}" class="btn btn-outline-info" title="Preview Dossier">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.materials.edit', $mat) }}" class="btn btn-outline-secondary" title="Edit Material">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.materials.destroy', $mat) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this study material?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete Material">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fa-solid fa-file-pdf fs-1 text-secondary opacity-50"></i></div>
                                <h6>No Study Materials Found</h6>
                                <p class="small mb-3">Upload digital study guides, PDF textbooks, and video lectures for enrolled candidates.</p>
                                <a href="{{ route('admin.materials.create') }}" class="btn btn-sm btn-primary" style="background-color: #0891b2; border-color: #0891b2;">
                                    <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload First Resource
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($materials->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $materials->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Upload Study Material')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a;">
                <i class="fa-solid fa-cloud-arrow-up me-2" style="color: #0891b2;"></i>Upload New Study Material
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.materials.index') }}">Study Materials</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Upload Material</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.materials.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Materials
        </a>
    </div>

    <div class="row">
        <div class="col-lg-9 mx-auto">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-file-arrow-up me-2 text-primary"></i>Resource Particulars & Upload
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.materials.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="title" class="form-label fw-semibold">Resource Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. ADCA Complete Operating System & Fundamentals Notes" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="course_id" class="form-label fw-semibold">Target Course Curriculum <span class="text-danger">*</span></label>
                                <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
                                    <option value="">Select Course</option>
                                    @foreach($courses as $c)
                                        <option value="{{ $c->id }}" {{ old('course_id') == $c->id ? 'selected' : '' }}>
                                            {{ $c->course_name }} ({{ $c->course_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="course_subject_id" class="form-label fw-semibold">Subject Module (Optional)</label>
                                <select class="form-select @error('course_subject_id') is-invalid @enderror" id="course_subject_id" name="course_subject_id">
                                    <option value="">All Modules / Comprehensive</option>
                                    @foreach($courses as $c)
                                        @foreach($c->subjects as $s)
                                            <option value="{{ $s->id }}" {{ old('course_subject_id') == $s->id ? 'selected' : '' }}>
                                                {{ $c->course_code }} - {{ $s->subject_name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                                @error('course_subject_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="material_type" class="form-label fw-semibold">Resource Format <span class="text-danger">*</span></label>
                                <select class="form-select @error('material_type') is-invalid @enderror" id="material_type" name="material_type" required>
                                    <option value="pdf" {{ old('material_type') === 'pdf' ? 'selected' : '' }}>PDF Document / E-Book</option>
                                    <option value="video" {{ old('material_type') === 'video' ? 'selected' : '' }}>Video Lecture Link</option>
                                    <option value="notes" {{ old('material_type') === 'notes' ? 'selected' : '' }}>Study Notes & Slides</option>
                                    <option value="sample_paper" {{ old('material_type') === 'sample_paper' ? 'selected' : '' }}>Sample Question Paper</option>
                                    <option value="syllabus" {{ old('material_type') === 'syllabus' ? 'selected' : '' }}>Official Syllabus Copy</option>
                                    <option value="link" {{ old('material_type') === 'link' ? 'selected' : '' }}>External Web Reference</option>
                                </select>
                                @error('material_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="centre_id" class="form-label fw-semibold">Access Restriction</label>
                                <select class="form-select @error('centre_id') is-invalid @enderror" id="centre_id" name="centre_id">
                                    <option value="">All Centres (Nationwide Public Access)</option>
                                    @foreach($centres as $ctr)
                                        <option value="{{ $ctr->id }}" {{ old('centre_id') == $ctr->id ? 'selected' : '' }}>
                                            Restricted to: {{ $ctr->centre_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('centre_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="fileUploadContainer">
                                <label for="file" class="form-label fw-semibold">Upload Digital Document</label>
                                <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip">
                                <div class="form-text small">Allowed: PDF, DOC, XLS, ZIP (Max 20 MB).</div>
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="externalUrlContainer">
                                <label for="external_url" class="form-label fw-semibold">External Resource / Video URL</label>
                                <input type="url" class="form-control @error('external_url') is-invalid @enderror" id="external_url" name="external_url" value="{{ old('external_url') }}" placeholder="https://youtube.com/watch?v=... or https://drive.google.com/...">
                                <div class="form-text small">Used for online video lessons or cloud-hosted assets.</div>
                                @error('external_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label fw-semibold">Resource Description & Instructions</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Brief outline of topics covered in this material...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" {{ old('status', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="status">Publish Immediately (Visible to enrolled students)</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.materials.index') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4" style="background-color: #0891b2; border-color: #0891b2;">
                                <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload & Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

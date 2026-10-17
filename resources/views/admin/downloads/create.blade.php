@extends('layouts.admin')

@section('title', 'Upload Downloadable Resource')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.downloads.index') }}">Downloads</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Upload</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-cloud-arrow-up text-primary me-2"></i>Upload New Downloadable Resource
            </h2>
            <p class="text-muted small mb-0">Publish official files, brochures, software utilities, or forms for centres and students.</p>
        </div>
        <div>
            <a href="{{ route('admin.downloads.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Catalog
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Please correct the following errors:</h6>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.downloads.store') }}" enctype="multipart/form-data" class="card border-0 shadow-sm rounded-3 bg-white">
                @csrf
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Resource Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="e.g. Tech Era Official Prospectus 2026-27">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="prospectus" {{ old('category') === 'prospectus' ? 'selected' : '' }}>Prospectus & Brochure</option>
                                <option value="form" {{ old('category') === 'form' ? 'selected' : '' }}>Forms & Affidavits</option>
                                <option value="software" {{ old('category') === 'software' ? 'selected' : '' }}>Software & Utilities</option>
                                <option value="template" {{ old('category') === 'template' ? 'selected' : '' }}>Templates & Branding</option>
                                <option value="driver" {{ old('category') === 'driver' ? 'selected' : '' }}>Device Drivers</option>
                                <option value="study_note" {{ old('category') === 'study_note' ? 'selected' : '' }}>Study Notes</option>
                            </select>
                            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Version</label>
                            <input type="text" name="version" class="form-control font-monospace @error('version') is-invalid @enderror" value="{{ old('version', 'v1.0') }}" placeholder="e.g. v2.4">
                            @error('version')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Upload File Resource <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" required>
                        <div class="form-text small">Accepted: PDF, DOC, DOCX, XLS, XLSX, ZIP up to 20MB.</div>
                        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Access Type <span class="text-danger">*</span></label>
                            <select name="is_free" id="isFreeSelect" class="form-select @error('is_free') is-invalid @enderror" required>
                                <option value="1" {{ old('is_free', '1') === '1' ? 'selected' : '' }}>Free Download</option>
                                <option value="0" {{ old('is_free') === '0' ? 'selected' : '' }}>Paid / Kit Resource</option>
                            </select>
                            @error('is_free')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6" id="priceContainer" style="display: {{ old('is_free', '1') === '0' ? 'block' : 'none' }};">
                            <label class="form-label fw-semibold small">Price (INR)</label>
                            <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', '0.00') }}" min="0">
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Resource Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Brief outline of contents, instructions, or compatibility...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Publication Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="1" {{ old('status', '1') === '1' ? 'selected' : '' }}>Published (Active)</option>
                            <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Draft / Hidden</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.downloads.index') }}" class="btn btn-light border px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fa-solid fa-cloud-arrow-up me-1"></i> Publish File
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isFreeSelect = document.getElementById('isFreeSelect');
    const priceContainer = document.getElementById('priceContainer');

    isFreeSelect.addEventListener('change', function() {
        priceContainer.style.display = this.value === '0' ? 'block' : 'none';
    });
});
</script>
@endpush
@endsection

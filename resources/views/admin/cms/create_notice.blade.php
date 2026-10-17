@extends('layouts.admin')

@section('title', 'Post New Notice / Announcement')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.cms.index') }}">CMS</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.cms.notices') }}">Notices</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Post Notice</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-plus-circle text-primary me-2"></i>Post New Announcement
            </h2>
            <p class="text-muted small mb-0">Publish official notices, admission updates, holiday circulars or marquee alerts.</p>
        </div>
        <div>
            <a href="{{ route('admin.cms.notices') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Notices
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-2"></i>Please correct the following errors:</h6>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-pen-nib me-2 text-primary"></i>Notice Particulars</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.cms.notices.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label small fw-semibold">Notice Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. Schedule for All India ADCA Term-End Examination December 2026" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="target_audience" class="form-label small fw-semibold">Target Audience <span class="text-danger">*</span></label>
                                <select class="form-select @error('target_audience') is-invalid @enderror" id="target_audience" name="target_audience" required>
                                    <option value="all" {{ old('target_audience') == 'all' ? 'selected' : '' }}>General (All Audiences)</option>
                                    <option value="centres" {{ old('target_audience') == 'centres' ? 'selected' : '' }}>Centres & Franchises</option>
                                    <option value="students" {{ old('target_audience') == 'students' ? 'selected' : '' }}>Students</option>
                                    <option value="public" {{ old('target_audience') == 'public' ? 'selected' : '' }}>Public Visitors</option>
                                </select>
                                @error('target_audience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="published_at" class="form-label small fw-semibold">Publication Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('published_at') is-invalid @enderror" id="published_at" name="published_at" value="{{ old('published_at', date('Y-m-d')) }}" required>
                                @error('published_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold d-block">Marquee Ticker</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_marquee" name="is_marquee" value="1" {{ old('is_marquee') ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="is_marquee">Scroll on Homepage Ticker</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label small fw-semibold">Notice Content / Full Details</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="5" placeholder="Enter complete notice text, guidelines, instructions...">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label for="attachment" class="form-label small fw-semibold">Attach Document / PDF Circular (Optional)</label>
                                <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment">
                                <div class="form-text small">Accepted formats: PDF, JPG, PNG, WEBP up to 5MB.</div>
                                @error('attachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold d-block">Publication Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="status">Active & Visible</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('admin.cms.notices') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa-solid fa-check me-1"></i> Publish Announcement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

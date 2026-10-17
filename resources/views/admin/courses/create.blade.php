@extends('layouts.admin')

@section('title', 'Add New Course - TECH ERA COMPUTER ERP')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}" class="text-decoration-none">Courses</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Course</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Add New Curriculum Course</h3>
        <p class="text-muted small mb-0">Define course parameters, duration, fee breakdown, eligibility and syllabus modules.</p>
    </div>
    <div>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Courses
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- Main Course Details -->
        <div class="col-lg-8">
            <div class="card dce-table-card mb-4">
                <div class="card-header bg-light">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-book me-2"></i>Course Particulars</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Course Code <span class="text-danger">*</span></label>
                            <input type="text" name="course_code" class="form-control font-monospace text-uppercase @error('course_code') is-invalid @enderror" 
                                   placeholder="e.g. ADCA, DCA, CCC" value="{{ old('course_code') }}" required>
                            @error('course_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Course Full Name / Title <span class="text-danger">*</span></label>
                            <input type="text" name="course_name" class="form-control @error('course_name') is-invalid @enderror" 
                                   placeholder="e.g. Advanced Diploma in Computer Application" value="{{ old('course_name') }}" required>
                            @error('course_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Category</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">-- General Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Duration <span class="text-danger">*</span></label>
                            <input type="text" name="duration" class="form-control @error('duration') is-invalid @enderror" 
                                   placeholder="e.g. 12 Months / 1 Year" value="{{ old('duration') }}" required>
                            @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Minimum Eligibility <span class="text-danger">*</span></label>
                            <input type="text" name="eligibility" class="form-control @error('eligibility') is-invalid @enderror" 
                                   placeholder="e.g. 10th Pass / 12th Pass" value="{{ old('eligibility', '10th Pass') }}" required>
                            @error('eligibility')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Course Syllabus & Curriculum Modules</label>
                            <textarea name="syllabus" rows="8" class="form-control @error('syllabus') is-invalid @enderror" 
                                      placeholder="Module 1: Computer Fundamentals&#10;Module 2: Operating Systems & GUI&#10;Module 3: MS Office Suite (Word, Excel, PowerPoint)&#10;Module 4: Internet & Cyber Ethics&#10;Module 5: Project & Practical Work">{{ old('syllabus') }}</textarea>
                            <div class="form-text small">Enter key topics and units taught during this course.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing, Thumbnail & Settings -->
        <div class="col-lg-4">
            <!-- Fee Breakdown -->
            <div class="card dce-table-card mb-4">
                <div class="card-header bg-light">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-indian-rupee-sign me-2"></i>Fee Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tuition / Base Course Fee (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="default_course_fee" id="baseFee" 
                               class="form-control @error('default_course_fee') is-invalid @enderror" 
                               value="{{ old('default_course_fee', 0) }}" required>
                        @error('default_course_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Registration Fee (₹)</label>
                        <input type="number" step="0.01" name="registration_fee" id="regFee" 
                               class="form-control" value="{{ old('registration_fee', 0) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Examination Fee (₹)</label>
                        <input type="number" step="0.01" name="exam_fee" id="examFee" 
                               class="form-control" value="{{ old('exam_fee', 0) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Certificate Fee (₹)</label>
                        <input type="number" step="0.01" name="certificate_fee" id="certFee" 
                               class="form-control" value="{{ old('certificate_fee', 0) }}">
                    </div>

                    <div class="p-3 bg-light rounded border text-center">
                        <div class="text-muted small fw-semibold">Estimated Total Package Fee</div>
                        <div class="h4 fw-bold text-success mb-0" id="totalFeeDisplay">₹0.00</div>
                    </div>
                </div>
            </div>

            <!-- Media & Publishing Settings -->
            <div class="card dce-table-card">
                <div class="card-header bg-light">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-sliders me-2"></i>Publishing & Media</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Thumbnail / Banner Image</label>
                        <input type="file" name="thumbnail" accept="image/*" class="form-control @error('thumbnail') is-invalid @enderror">
                        <div class="form-text small">Recommended size: 600x400 PNG/JPG.</div>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_popular" id="isPopularCheck" value="1" {{ old('is_popular') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="isPopularCheck">
                            Feature as Popular Course (Homepage)
                        </label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" id="statusCheck" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="statusCheck">
                            Published & Active for Enrollments
                        </label>
                    </div>
                </div>
                <div class="card-footer bg-white text-end py-3">
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary btn-sm px-3 me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                        <i class="fa-solid fa-check me-1"></i> Save Course
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseFee = document.getElementById('baseFee');
    const regFee = document.getElementById('regFee');
    const examFee = document.getElementById('examFee');
    const certFee = document.getElementById('certFee');
    const totalDisplay = document.getElementById('totalFeeDisplay');

    function calculateTotal() {
        const b = parseFloat(baseFee.value) || 0;
        const r = parseFloat(regFee.value) || 0;
        const e = parseFloat(examFee.value) || 0;
        const c = parseFloat(certFee.value) || 0;
        const total = b + r + e + c;
        totalDisplay.textContent = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    }

    [baseFee, regFee, examFee, certFee].forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    calculateTotal();
});
</script>
@endpush
@endsection

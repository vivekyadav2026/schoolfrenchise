@extends('layouts.admin')

@section('title', $course->course_name . ' (' . $course->course_code . ') - TECH ERA COMPUTER')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}" class="text-decoration-none">Courses</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $course->course_code }}</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Course Curriculum Dossier</h3>
        <p class="text-muted small mb-0">Standardized course outline, pricing schedule, batches, and active student admissions.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-outline-primary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-pen me-1"></i> Edit Course
        </a>
        <a href="{{ route('courses.show', $course->slug) }}" target="_blank" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Public Page
        </a>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-light btn-sm px-3 shadow-sm border">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Courses
        </a>
    </div>
</div>

<!-- Course Banner Card -->
<div class="card dce-table-card mb-4 border-top border-4 border-primary">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                @if($course->thumbnail)
                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->course_name }}" 
                         class="rounded border shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                @else
                    <div class="rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center border" 
                         style="width: 80px; height: 80px; font-size: 32px;">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                @endif
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-dark font-monospace fs-6">{{ $course->course_code }}</span>
                        @if($course->is_popular)
                            <span class="badge bg-warning text-dark">★ POPULAR</span>
                        @endif
                        @if($course->status)
                            <span class="badge bg-success">Active & Published</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                    <h4 class="fw-bold mb-1 text-dark">{{ $course->course_name }}</h4>
                    <div class="text-muted small">
                        <span><i class="fa-solid fa-layer-group text-primary me-1"></i>Category: {{ $course->category?->name ?? 'Information Technology' }}</span>
                        <span class="mx-2">•</span>
                        <span><i class="fa-solid fa-clock text-info me-1"></i>Duration: <strong>{{ $course->duration }}</strong></span>
                        <span class="mx-2">•</span>
                        <span><i class="fa-solid fa-graduation-cap text-success me-1"></i>Eligibility: {{ $course->eligibility }}</span>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <div class="text-muted small fw-semibold text-uppercase">Total Package Fee</div>
                <div class="h3 fw-bold text-success mb-0">₹{{ number_format($course->total_fee, 2) }}</div>
                <div class="text-muted small">{{ $course->admissions->count() }} Total Admitted Students</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Syllabus and Curriculum -->
    <div class="col-lg-8">
        <div class="card dce-table-card mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list-check text-primary me-2"></i>Curriculum & Syllabus Breakdown</h6>
            </div>
            <div class="card-body">
                @if($course->syllabus)
                    <div class="bg-light p-3 rounded border font-monospace small" style="white-space: pre-wrap; line-height: 1.6;">
                        {{ $course->syllabus }}
                    </div>
                @else
                    <div class="text-muted text-center py-4">No detailed syllabus added yet. <a href="{{ route('admin.courses.edit', $course->id) }}">Edit course</a> to add modules.</div>
                @endif
            </div>
        </div>

        <!-- Enrolled Admissions Table -->
        <div class="card dce-table-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-users-viewfinder text-primary me-2"></i>Recent Course Admissions ({{ $course->admissions->count() }})
                </h6>
                <a href="{{ route('admin.admissions.create') }}" class="btn btn-sm btn-outline-primary shadow-sm">
                    <i class="fa-solid fa-plus me-1"></i> New Admission
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Centre</th>
                                <th>Admission Date</th>
                                <th>Net Fee</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($course->admissions->take(10) as $adm)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.students.show', $adm->student_id) }}" class="fw-bold text-primary text-decoration-none">
                                        {{ $adm->student?->full_name }}
                                    </a>
                                    <div class="font-monospace text-muted small">Roll: {{ $adm->student?->roll_no }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark border font-monospace">{{ $adm->centre?->centre_code }}</span></td>
                                <td>{{ $adm->admission_date ? \Carbon\Carbon::parse($adm->admission_date)->format('d M Y') : '—' }}</td>
                                <td class="fw-bold text-dark">₹{{ number_format($adm->net_fee, 2) }}</td>
                                <td><span class="badge bg-success">{{ ucfirst($adm->status) }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No students currently enrolled in this course.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Structure & Batches -->
    <div class="col-lg-4">
        <!-- Fee Components Card -->
        <div class="card dce-table-card mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-receipt text-success me-2"></i>Fee Breakdown</h6>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0 table-striped">
                    <tbody>
                        <tr>
                            <td class="ps-3 text-muted">Tuition / Base Course Fee</td>
                            <td class="text-end pe-3 fw-bold">₹{{ number_format($course->default_course_fee, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted">Registration Fee</td>
                            <td class="text-end pe-3">₹{{ number_format($course->registration_fee, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted">Examination Fee</td>
                            <td class="text-end pe-3">₹{{ number_format($course->exam_fee, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted">Certification Fee</td>
                            <td class="text-end pe-3">₹{{ number_format($course->certificate_fee, 2) }}</td>
                        </tr>
                        <tr class="table-success">
                            <td class="ps-3 fw-bold text-dark">Total Standard Fee</td>
                            <td class="text-end pe-3 fw-bold text-success fs-6">₹{{ number_format($course->total_fee, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Associated Batches Card -->
        <div class="card dce-table-card">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-calendar-days text-info me-2"></i>Active Batches ({{ $course->batches->count() }})</h6>
            </div>
            <div class="card-body p-3">
                @forelse($course->batches as $batch)
                    <div class="p-2 border rounded mb-2 bg-light">
                        <div class="fw-semibold text-dark">{{ $batch->batch_name }}</div>
                        <div class="small text-muted font-monospace"><i class="fa-solid fa-clock me-1 text-primary"></i>{{ $batch->start_time }} - {{ $batch->end_time }}</div>
                    </div>
                @empty
                    <div class="text-muted small text-center py-3">No active training batches linked to this course.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

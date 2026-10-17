@extends('layouts.admin')

@section('title', $exam->exam_title . ' - Examination Dossier')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0" style="color: #0f172a;">{{ $exam->exam_title }}</h4>
                @if($exam->status === 'scheduled')
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"><i class="fa-solid fa-clock me-1"></i> Scheduled</span>
                @elseif($exam->status === 'ongoing')
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="fa-solid fa-spinner me-1"></i> Ongoing</span>
                @elseif($exam->status === 'evaluated')
                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1"><i class="fa-solid fa-check-to-slot me-1"></i> Evaluated</span>
                @elseif($exam->status === 'published')
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i> Published</span>
                @endif
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.exams.index') }}">Examinations</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $exam->exam_code }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.exams.results.create', ['exam_id' => $exam->id]) }}" class="btn btn-success">
                <i class="fa-solid fa-marker me-1"></i> Enter Student Marks
            </a>
            <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-pen me-1"></i> Edit Exam
            </a>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-dark">
                <i class="fa-solid fa-arrow-left me-1"></i> All Exams
            </a>
        </div>
    </div>

    <!-- Exam Overview Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase">Course & Code</div>
                    <div class="fw-bold fs-6 text-dark mt-1">{{ $exam->course->course_name ?? 'N/A' }}</div>
                    <span class="badge bg-light text-secondary border font-monospace mt-1">{{ $exam->exam_code }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase">Academic Session</div>
                    <div class="fw-bold fs-6 text-primary mt-1">{{ $exam->session_name }}</div>
                    <div class="small text-muted mt-1"><i class="fa-regular fa-calendar me-1"></i>Exam Date: {{ $exam->exam_date ? $exam->exam_date->format('d M, Y') : 'N/A' }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase">Evaluated Candidates</div>
                    <div class="fw-bold fs-5 text-dark mt-1">{{ $totalCandidates }} Students</div>
                    <div class="small text-muted mt-1">Average Score: <strong class="text-primary">{{ $avgScore }}%</strong></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase">Pass / Fail Ratio</div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-success-subtle text-success fs-6"><i class="fa-solid fa-check me-1"></i>{{ $passedCount }} Pass</span>
                        <span class="badge bg-danger-subtle text-danger fs-6"><i class="fa-solid fa-xmark me-1"></i>{{ $failedCount }} Fail</span>
                        @if($withheldCount > 0)
                            <span class="badge bg-warning-subtle text-warning fs-6">{{ $withheldCount }} Withheld</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Subjects in this Course Curriculum -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-book-open me-2 text-primary"></i>Curriculum Subject Papers
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($exam->course->subjects ?? [] as $sub)
                            <div class="list-group-item px-3 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fw-semibold text-dark">{{ $sub->subject_name }}</div>
                                    <span class="badge bg-light text-secondary border font-monospace">{{ $sub->subject_code }}</span>
                                </div>
                                <div class="small text-muted mt-1 d-flex gap-2">
                                    <span>Theory: <strong>{{ $sub->theory_max }}</strong></span>
                                    <span>Practical: <strong>{{ $sub->practical_max }}</strong></span>
                                    <span>Viva: <strong>{{ $sub->viva_max }}</strong></span>
                                    <span>Max: <strong>{{ $sub->total_max }}</strong></span>
                                    <span>Pass: <strong class="text-success">{{ $sub->min_pass_marks }}</strong></span>
                                </div>
                            </div>
                        @empty
                            <div class="p-3 text-muted text-center small">No subjects configured for this course yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Candidate Results List -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-users-rectangle me-2 text-primary"></i>Evaluated Candidate Roster ({{ $exam->results->count() }})
                    </h6>
                    <a href="{{ route('admin.exams.results.create', ['exam_id' => $exam->id]) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-plus me-1"></i> Add Candidate Marks
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-3">Student</th>
                                <th>Centre</th>
                                <th>Marks Obtained</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exam->results as $result)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold text-dark">{{ $result->student->full_name ?? 'N/A' }}</div>
                                        <small class="text-muted font-monospace">{{ $result->student->roll_no ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-secondary">{{ $result->centre->centre_name ?? 'Main Centre' }}</small>
                                    </td>
                                    <td class="fw-semibold">
                                        {{ number_format($result->total_marks_obtained, 2) }} / {{ number_format($result->total_max_marks, 2) }}
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ number_format($result->percentage, 2) }}%</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary font-monospace px-2 py-1">{{ $result->grade }}</span>
                                    </td>
                                    <td>
                                        @if($result->result_status === 'passed')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-check me-1"></i>Passed</span>
                                        @elseif($result->result_status === 'failed')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="fa-solid fa-xmark me-1"></i>Failed</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Withheld</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('admin.exams.results.show', $result) }}" class="btn btn-sm btn-outline-info" title="View Result Dossier">
                                            <i class="fa-solid fa-eye me-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <p class="small mb-2">No student marks have been entered for this examination cycle yet.</p>
                                        <a href="{{ route('admin.exams.results.create', ['exam_id' => $exam->id]) }}" class="btn btn-sm btn-primary" style="background-color: #0891b2; border-color: #0891b2;">
                                            <i class="fa-solid fa-marker me-1"></i> Enter Marks Now
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

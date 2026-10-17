@extends('layouts.centre')

@section('title', 'Examination Results - TECH ERA COMPUTER')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <a href="{{ route('centre.exams.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill mb-2">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Exams
        </a>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-square-poll-vertical text-success me-2"></i>Published Examination Results
        </h3>
        <p class="text-muted small mb-0">Candidate grades, percentage scores, and performance summaries for centre candidates.</p>
    </div>
</div>

<!-- Stats Strip -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase">Evaluated Candidates</div>
            <div class="fs-4 fw-bold text-dark my-1">{{ number_format($stats['total']) }}</div>
            <span class="small text-muted">All sessions</span>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card p-3 bg-white border-start border-4 border-success">
            <div class="text-muted small fw-semibold text-uppercase">Passed</div>
            <div class="fs-4 fw-bold text-success my-1">{{ number_format($stats['passed']) }}</div>
            <span class="small text-muted">Eligible for certification</span>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card p-3 bg-white border-start border-4 border-danger">
            <div class="text-muted small fw-semibold text-uppercase">Failed / Reappear</div>
            <div class="fs-4 fw-bold text-danger my-1">{{ number_format($stats['failed']) }}</div>
            <span class="small text-muted">Requires re-attempt</span>
        </div>
    </div>
</div>

<!-- Results Table -->
<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Roll No & Reg No</th>
                        <th>Student Name</th>
                        <th>Exam Session</th>
                        <th>Course</th>
                        <th class="text-center">Score</th>
                        <th class="text-center">Percentage</th>
                        <th class="text-center">Grade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $res)
                    <tr>
                        <td>
                            <span class="badge bg-primary-subtle text-primary font-monospace fw-bold">{{ $res->student?->roll_no }}</span>
                            <div class="small text-muted font-monospace" style="font-size: 0.72rem;">{{ $res->student?->registration_no }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $res->student?->full_name }}</div>
                            <div class="small text-muted">Father: {{ $res->student?->father_name }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $res->exam?->exam_title }}</div>
                            <div class="small text-muted">{{ $res->exam?->session_name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary">{{ $res->course?->course_code }}</span>
                            <div class="small text-muted text-truncate" style="max-width: 150px;">{{ $res->course?->course_name }}</div>
                        </td>
                        <td class="text-center fw-bold">{{ $res->obtained_marks }} / {{ $res->total_marks }}</td>
                        <td class="text-center fw-bold">{{ $res->percentage }}%</td>
                        <td class="text-center"><span class="badge bg-primary">{{ $res->grade }}</span></td>
                        <td>
                            <span class="badge {{ $res->result_status === 'passed' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($res->result_status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-square-poll-horizontal fs-1 text-black-50 mb-3 d-block"></i>
                            <h5>No Examination Results Recorded</h5>
                            <p class="small text-muted mb-0">Select an exam session and submit marks evaluation to record results.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($results->hasPages())
        <div class="p-3 border-top">
            {{ $results->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

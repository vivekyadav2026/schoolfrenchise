@extends('layouts.admin')

@section('title', 'Examination Results & Marksheets')

@section('content')
<div class="container-fluid p-0">
    <!-- Header with Action Buttons -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a;">
                <i class="fa-solid fa-square-poll-vertical me-2" style="color: #0891b2;"></i>Student Results & Marksheets
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.exams.index') }}">Examinations</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Results</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-calendar-days me-1"></i> Exam Cycles
            </a>
            <a href="{{ route('admin.exams.results.create') }}" class="btn btn-primary" style="background-color: #0891b2; border-color: #0891b2;">
                <i class="fa-solid fa-marker me-1"></i> Enter Student Marks
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Evaluated</div>
                        <h3 class="fw-bold my-1" style="color: #0f172a;">{{ number_format($stats['total']) }}</h3>
                        <div class="small text-muted">All students evaluated</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #e0f2fe; color: #0284c7;">
                        <i class="fa-solid fa-graduation-cap fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Passed</div>
                        <h3 class="fw-bold my-1 text-success">{{ number_format($stats['passed']) }}</h3>
                        <div class="small text-muted">Cleared course requirements</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #dcfce7; color: #16a34a;">
                        <i class="fa-solid fa-circle-check fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Failed / Reappear</div>
                        <h3 class="fw-bold my-1 text-danger">{{ number_format($stats['failed']) }}</h3>
                        <div class="small text-muted">Below passing criteria</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fee2e2; color: #dc2626;">
                        <i class="fa-solid fa-circle-xmark fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Withheld / Pending</div>
                        <h3 class="fw-bold my-1 text-warning">{{ number_format($stats['withheld']) }}</h3>
                        <div class="small text-muted">Under board verification</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fef3c7; color: #d97706;">
                        <i class="fa-solid fa-pause fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.exams.results') }}" class="row g-2">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Student name, Roll or Reg No..." value="{{ request('search') }}">
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
                <div class="col-md-2">
                    <select name="centre_id" class="form-select bg-light">
                        <option value="">All Centres</option>
                        @foreach($centres as $ctr)
                            <option value="{{ $ctr->id }}" {{ request('centre_id') == $ctr->id ? 'selected' : '' }}>
                                {{ $ctr->centre_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="result_status" class="form-select bg-light">
                        <option value="">All Statuses</option>
                        <option value="passed" {{ request('result_status') === 'passed' ? 'selected' : '' }}>Passed</option>
                        <option value="failed" {{ request('result_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="withheld" {{ request('result_status') === 'withheld' ? 'selected' : '' }}>Withheld</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100" style="background-color: #0891b2; border-color: #0891b2;">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.exams.results') }}" class="btn btn-outline-secondary" title="Reset Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-award me-2 text-primary"></i>Evaluated Results Roster
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3">Student Particulars</th>
                        <th>Course & Session</th>
                        <th>Study Centre</th>
                        <th>Marks Obtained</th>
                        <th>Percentage</th>
                        <th>Grade</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $res)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">{{ $res->student->full_name ?? 'N/A' }}</div>
                                <div class="small text-muted font-monospace">
                                    Roll: {{ $res->student->roll_no ?? 'N/A' }} | Reg: {{ $res->student->registration_no ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $res->course->course_name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $res->exam->session_name ?? 'General Session' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">
                                    {{ $res->centre->centre_name ?? 'Head Office' }}
                                </span>
                            </td>
                            <td class="fw-semibold">
                                {{ number_format($res->total_marks_obtained, 2) }} / {{ number_format($res->total_max_marks, 2) }}
                            </td>
                            <td>
                                <span class="fw-bold fs-6 text-dark">{{ number_format($res->percentage, 2) }}%</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary font-monospace px-2 py-1 fs-6">{{ $res->grade }}</span>
                            </td>
                            <td>
                                @if($res->result_status === 'passed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-check me-1"></i> Passed</span>
                                @elseif($res->result_status === 'failed')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="fa-solid fa-xmark me-1"></i> Failed</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Withheld</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('admin.exams.results.show', $res) }}" class="btn btn-sm btn-outline-info" title="View Detailed Marksheet">
                                    <i class="fa-solid fa-eye me-1"></i> Dossier
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fa-solid fa-square-poll-vertical fs-1 text-secondary opacity-50"></i></div>
                                <h6>No Exam Results Found</h6>
                                <p class="small mb-3">Enter marks for candidates to generate computerized marksheets and certificates.</p>
                                <a href="{{ route('admin.exams.results.create') }}" class="btn btn-sm btn-primary" style="background-color: #0891b2; border-color: #0891b2;">
                                    <i class="fa-solid fa-marker me-1"></i> Enter Marks Now
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($results->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $results->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Certificates & Marksheets Management')

@section('content')
<div class="container-fluid p-0">
    <!-- Header with Action Buttons -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a;">
                <i class="fa-solid fa-certificate me-2" style="color: #0891b2;"></i>Certificates & Marksheets
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Certificates</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.exams.results') }}" class="btn btn-outline-info">
                <i class="fa-solid fa-square-poll-vertical me-1"></i> Exam Results
            </a>
            <a href="{{ route('admin.certificates.create') }}" class="btn btn-primary" style="background-color: #0891b2; border-color: #0891b2;">
                <i class="fa-solid fa-stamp me-1"></i> Issue Certificate
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Certificates</div>
                        <h3 class="fw-bold my-1" style="color: #0f172a;">{{ number_format($stats['total_certificates']) }}</h3>
                        <div class="small text-muted">All registered credentials</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fef3c7; color: #d97706;">
                        <i class="fa-solid fa-award fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Active Issued</div>
                        <h3 class="fw-bold my-1 text-success">{{ number_format($stats['issued_count']) }}</h3>
                        <div class="small text-muted">Verifiable online</div>
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
                        <div class="text-muted small fw-semibold text-uppercase">Revoked / Suspended</div>
                        <h3 class="fw-bold my-1 text-danger">{{ number_format($stats['revoked_count']) }}</h3>
                        <div class="small text-muted">Disqualified records</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fee2e2; color: #dc2626;">
                        <i class="fa-solid fa-ban fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Pending Issuance</div>
                        <h3 class="fw-bold my-1 text-primary">{{ number_format($stats['pending_results']) }}</h3>
                        <div class="small text-muted">Passed students awaiting cert</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #eff6ff; color: #2563eb;">
                        <i class="fa-solid fa-user-clock fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.certificates.index') }}" class="row g-2">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Cert No, Student Name, Roll No..." value="{{ request('search') }}">
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
                    <select name="centre_id" class="form-select bg-light">
                        <option value="">All Study Centres</option>
                        @foreach($centres as $ctr)
                            <option value="{{ $ctr->id }}" {{ request('centre_id') == $ctr->id ? 'selected' : '' }}>
                                {{ $ctr->centre_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select bg-light">
                        <option value="">All Statuses</option>
                        <option value="issued" {{ request('status') === 'issued' ? 'selected' : '' }}>Active / Issued</option>
                        <option value="revoked" {{ request('status') === 'revoked' ? 'selected' : '' }}>Revoked</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100 px-0" style="background-color: #0891b2; border-color: #0891b2;" title="Filter">
                        <i class="fa-solid fa-filter"></i>
                    </button>
                    <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary px-2" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Certificates Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-ribbon me-2 text-primary"></i>Issued Certificates & Credentials
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3">Certificate No</th>
                        <th>Student Particulars</th>
                        <th>Course Curriculum</th>
                        <th>Study Centre</th>
                        <th>Issue Date</th>
                        <th>Grade</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $cert)
                        <tr>
                            <td class="ps-3">
                                <span class="fw-bold font-monospace text-primary fs-6">{{ $cert->certificate_no }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $cert->student->full_name ?? 'N/A' }}</div>
                                <div class="small text-muted font-monospace">
                                    Roll: {{ $cert->student->roll_no ?? 'N/A' }} | Reg: {{ $cert->student->registration_no ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $cert->course->course_name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $cert->course->course_code ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">
                                    {{ $cert->centre->centre_name ?? 'Head Office' }}
                                </span>
                            </td>
                            <td class="fw-semibold">
                                {{ $cert->issue_date ? $cert->issue_date->format('d M, Y') : 'N/A' }}
                            </td>
                            <td>
                                <span class="badge bg-secondary font-monospace px-2 py-1 fs-6">{{ $cert->result->grade ?? 'A' }}</span>
                            </td>
                            <td>
                                @if($cert->status === 'issued')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i>Valid</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="fa-solid fa-ban me-1"></i>Revoked</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.certificates.show', $cert) }}" class="btn btn-outline-info" title="View Certificate Dossier">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.certificates.print', $cert) }}" target="_blank" class="btn btn-outline-primary" title="Print Certificate">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                    <a href="{{ route('admin.certificates.marksheet', $cert) }}" target="_blank" class="btn btn-outline-secondary" title="View / Print Marksheet">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fa-solid fa-certificate fs-1 text-secondary opacity-50"></i></div>
                                <h6>No Certificates Issued Yet</h6>
                                <p class="small mb-3">Issue certificates and marksheets to students who have passed their examination evaluations.</p>
                                <a href="{{ route('admin.certificates.create') }}" class="btn btn-sm btn-primary" style="background-color: #0891b2; border-color: #0891b2;">
                                    <i class="fa-solid fa-stamp me-1"></i> Issue Certificate Now
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($certificates->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

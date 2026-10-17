@extends('layouts.admin')

@section('title', 'Admissions Management - TECH ERA COMPUTER ERP')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Admissions</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Admissions & Applications Management</h3>
        <p class="text-muted small mb-0">Review online applications, verify student KYC documents, approve admissions, and manage enrolled courses.</p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('admin.admissions.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Direct Admission
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card dce-stat-card border-start border-4 border-warning">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase">Pending Applications</div>
                <div class="h4 fw-bold mb-0 text-warning">{{ $counts['pending_apps'] }}</div>
                <div class="text-muted small mt-1">Awaiting document review</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card dce-stat-card border-start border-4 border-info">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase">Under Review</div>
                <div class="h4 fw-bold mb-0 text-info">{{ $counts['under_review_apps'] }}</div>
                <div class="text-muted small mt-1">Verification in progress</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card dce-stat-card border-start border-4 border-success">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase">Approved Applications</div>
                <div class="h4 fw-bold mb-0 text-success">{{ $counts['approved_apps'] }}</div>
                <div class="text-muted small mt-1">Converted to students</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card dce-stat-card border-start border-4 border-primary">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase">Active Course Enrollments</div>
                <div class="h4 fw-bold mb-0 text-primary">{{ $counts['active_admissions'] }}</div>
                <div class="text-muted small mt-1">Enrolled admissions ledger</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs: Applications vs Active Admissions -->
<ul class="nav nav-pills dce-nav-pills mb-4">
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'applications' ? 'active' : '' }}" href="{{ route('admin.admissions.index', ['tab' => 'applications']) }}">
            <i class="fa-solid fa-file-signature me-1"></i> Admission Applications
            @if($counts['pending_apps'] > 0)
                <span class="badge bg-danger ms-1">{{ $counts['pending_apps'] }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'active' ? 'active' : '' }}" href="{{ route('admin.admissions.index', ['tab' => 'active']) }}">
            <i class="fa-solid fa-graduation-cap me-1"></i> Active Course Admissions ({{ $counts['active_admissions'] }})
        </a>
    </li>
</ul>

<!-- Filter Bar -->
<div class="card dce-table-card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.admissions.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="{{ $tab === 'active' ? 'Search student name, roll no, mobile...' : 'Search applicant name, application no, mobile...' }}" 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="centre_id" class="form-select">
                    <option value="">-- All Centres --</option>
                    @foreach($centres as $c)
                        <option value="{{ $c->id }}" {{ request('centre_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->centre_code }} - {{ $c->centre_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- All Statuses --</option>
                    @if($tab === 'active')
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Admission</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    @else
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Review</option>
                        <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    @endif
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i>Filter</button>
                @if(request()->anyFilled(['search', 'centre_id', 'status']))
                    <a href="{{ route('admin.admissions.index', ['tab' => $tab]) }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="fa-solid fa-xmark"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tab Content -->
@if($tab === 'applications')
    <!-- Applications Table -->
    <div class="card dce-table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-file-lines text-primary me-2"></i>Online Admission Applications
                <span class="badge bg-secondary-subtle text-secondary ms-2">{{ $applications->total() }} Records</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Application No</th>
                            <th>Applicant Details</th>
                            <th>Course Applied</th>
                            <th>Allocated Centre</th>
                            <th>Submitted On</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                        <tr>
                            <td>
                                <div class="fw-bold font-monospace text-primary">{{ $app->application_no }}</div>
                                <span class="badge bg-light text-muted border" style="font-size: 10px;">ONLINE APP</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $app->applicant_name }}</div>
                                <div class="text-muted small">
                                    <i class="fa-solid fa-phone me-1 text-success"></i>{{ $app->mobile }}
                                    @if($app->email) • {{ $app->email }} @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary">{{ $app->course?->course_code }}</span>
                                <div class="small text-truncate" style="max-width: 160px;">{{ $app->course?->title }}</div>
                            </td>
                            <td>
                                <span class="badge bg-dark-subtle text-dark font-monospace">{{ $app->centre?->centre_code }}</span>
                                <div class="small text-muted text-truncate" style="max-width: 140px;">{{ $app->centre?->centre_name }}</div>
                            </td>
                            <td>
                                <div class="small text-dark">{{ $app->created_at->format('d M Y') }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $app->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                @php
                                    $appBadge = match($app->status) {
                                        'pending' => 'bg-warning text-dark',
                                        'under_review' => 'bg-info',
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $appBadge }}">{{ ucfirst(str_replace('_', ' ', $app->status)) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.admissions.application.show', $app->id) }}" class="btn btn-sm btn-outline-primary shadow-sm">
                                    <i class="fa-solid fa-magnifying-glass me-1"></i> Review & Action
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-clipboard-question fa-2x mb-3 text-secondary"></i>
                                <p class="mb-0">No admission applications found matching your criteria.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($applications->hasPages())
        <div class="card-footer bg-white py-3 border-top">
            {{ $applications->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
@else
    <!-- Active Admissions Table -->
    <div class="card dce-table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-graduation-cap text-primary me-2"></i>Enrolled Student Course Admissions
                <span class="badge bg-secondary-subtle text-secondary ms-2">{{ $admissions->total() }} Records</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Centre</th>
                            <th>Admission Date</th>
                            <th>Fee Ledger</th>
                            <th>Batch</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admissions as $adm)
                        <tr>
                            <td>
                                <a href="{{ route('admin.students.show', $adm->student_id) }}" class="fw-bold text-primary text-decoration-none">
                                    {{ $adm->student?->full_name }}
                                </a>
                                <div class="font-monospace text-muted small">Roll: {{ $adm->student?->roll_no }}</div>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info">{{ $adm->course?->course_code }}</span>
                                <div class="small fw-semibold text-dark text-truncate" style="max-width: 150px;">{{ $adm->course?->title }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark font-monospace border">{{ $adm->centre?->centre_code }}</span>
                            </td>
                            <td>{{ $adm->admission_date ? \Carbon\Carbon::parse($adm->admission_date)->format('d M Y') : '—' }}</td>
                            <td>
                                <div class="small fw-bold text-dark">Net: ₹{{ number_format($adm->net_fee, 2) }}</div>
                                <div class="small text-success">Paid: ₹{{ number_format($adm->total_paid, 2) }}</div>
                            </td>
                            <td>
                                @if($adm->batch)
                                    <span class="badge bg-light text-dark border">{{ $adm->batch->batch_name }}</span>
                                @else
                                    <span class="text-muted small">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusBadge = match($adm->status) {
                                        'active' => 'bg-success',
                                        'completed' => 'bg-primary',
                                        'cancelled' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $statusBadge }}">{{ ucfirst($adm->status) }}</span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.admissions.print', $adm->id) }}" target="_blank" class="btn btn-outline-primary" title="Print Official Admission Dossier">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                    <a href="{{ route('admin.admissions.edit', $adm->id) }}" class="btn btn-outline-secondary" title="Edit Admission">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.admissions.destroy', $adm->id) }}" class="d-inline" data-confirm="Are you sure you want to soft-delete this admission record?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Soft Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-inbox fa-2x mb-3 text-secondary"></i>
                                <p class="mb-0">No active admissions recorded yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($admissions->hasPages())
        <div class="card-footer bg-white py-3 border-top">
            {{ $admissions->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
@endif
@endsection

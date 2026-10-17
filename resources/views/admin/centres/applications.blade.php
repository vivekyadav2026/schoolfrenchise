@extends('layouts.admin')

@section('title', 'Franchise Applications')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.centres.index') }}">Centres & Franchises</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Applications</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-file-signature text-warning me-2"></i>Franchise Inward Applications
            </h2>
            <p class="text-muted small mb-0">Review new franchise proposals, verify infrastructure specifications, and approve institute accreditation.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.centres.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Centres
            </a>
            <a href="{{ route('admin.centres.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-plus-circle me-1"></i> Direct Centre Registration
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Status KPI Badges -->
    <div class="row g-3 mb-4">
        <div class="col">
            <a href="{{ route('admin.centres.applications') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3 text-center {{ !request('status') ? 'border-bottom border-3 border-dark' : '' }}">
                    <div class="small text-muted fw-bold text-uppercase">Total Inward</div>
                    <div class="h4 fw-bold text-dark mb-0">{{ number_format($stats['total']) }}</div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('admin.centres.applications', ['status' => 'pending']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3 text-center {{ request('status') === 'pending' ? 'border-bottom border-3 border-warning' : '' }}">
                    <div class="small text-warning fw-bold text-uppercase">Pending Review</div>
                    <div class="h4 fw-bold text-warning mb-0">{{ number_format($stats['pending']) }}</div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('admin.centres.applications', ['status' => 'approved']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3 text-center {{ request('status') === 'approved' ? 'border-bottom border-3 border-success' : '' }}">
                    <div class="small text-success fw-bold text-uppercase">Approved</div>
                    <div class="h4 fw-bold text-success mb-0">{{ number_format($stats['approved']) }}</div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('admin.centres.applications', ['status' => 'rejected']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3 text-center {{ request('status') === 'rejected' ? 'border-bottom border-3 border-danger' : '' }}">
                    <div class="small text-danger fw-bold text-uppercase">Rejected</div>
                    <div class="h4 fw-bold text-danger mb-0">{{ number_format($stats['rejected']) }}</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.centres.applications') }}" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search application no, applicant, proposed centre, phone..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-4">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('admin.centres.applications') }}" class="btn btn-sm btn-light border" title="Clear Filters">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0 text-dark">
                Application Submissions <span class="badge bg-light text-dark fw-normal border ms-1">{{ $applications->total() }}</span>
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3">Application No</th>
                        <th>Applicant Name</th>
                        <th>Proposed Centre</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th class="text-center">Lab PCs</th>
                        <th class="text-center">Area (Sqft)</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr>
                            <td class="ps-3">
                                <span class="font-monospace fw-bold text-primary">{{ $app->application_no }}</span>
                                <div class="small text-muted">{{ $app->created_at?->format('d M, Y') }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $app->applicant_name }}</div>
                                <div class="small text-muted">{{ $app->qualification }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $app->centre_name }}</div>
                            </td>
                            <td>
                                <div><i class="fa-solid fa-phone me-1 small text-muted"></i>{{ $app->mobile }}</div>
                                <div class="small text-muted">{{ $app->email }}</div>
                            </td>
                            <td>
                                <div>{{ $app->city }}</div>
                                <div class="small text-muted">{{ $app->state?->name ?? 'N/A' }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border font-monospace">
                                    {{ $app->computers_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border font-monospace">
                                    {{ $app->carpet_area_sqft }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($app->status === 'approved')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="fa-solid fa-circle-check me-1 small"></i>Approved
                                    </span>
                                @elseif($app->status === 'rejected')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="fa-solid fa-circle-xmark me-1 small"></i>Rejected
                                    </span>
                                @elseif($app->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                        <i class="fa-solid fa-clock me-1 small"></i>Pending
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                        {{ ucfirst($app->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('admin.centres.applications.show', $app) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-folder-open me-1"></i> Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-inbox fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h6>No Franchise Applications Found</h6>
                                    <p class="small text-muted mb-0">No application submissions match the selected filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applications->hasPages())
            <div class="card-footer bg-white py-3 border-0">
                {{ $applications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

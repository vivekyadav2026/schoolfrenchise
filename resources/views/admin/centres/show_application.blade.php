@extends('layouts.admin')

@section('title', 'Review Franchise Application #' . $application->application_no)

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.centres.index') }}">Centres</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.centres.applications') }}">Applications</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $application->application_no }}</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                Application Review: <span class="text-primary font-monospace">{{ $application->application_no }}</span>
            </h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.centres.applications') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Roster
            </a>
            @if($application->status !== 'approved')
                <button type="button" class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="fa-solid fa-circle-check me-1"></i> Approve & Issue Affiliation
                </button>
            @endif
            @if($application->status !== 'rejected')
                <button type="button" class="btn btn-outline-danger shadow-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fa-solid fa-ban me-1"></i> Reject Application
                </button>
            @endif
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Application Dossier -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-building-columns text-primary me-2"></i>Proposed Centre Particulars
                    </h5>
                    @if($application->status === 'approved')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                            <i class="fa-solid fa-circle-check me-1"></i> Approved
                        </span>
                    @elseif($application->status === 'rejected')
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                            <i class="fa-solid fa-ban me-1"></i> Rejected
                        </span>
                    @else
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">
                            <i class="fa-solid fa-clock me-1"></i> Pending Verification
                        </span>
                    @endif
                </div>

                <div class="card-body p-4 pt-0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold">Proposed Centre Name</label>
                            <div class="fw-bold fs-5 text-dark">{{ $application->centre_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold">Applicant / Director Name</label>
                            <div class="fw-semibold text-dark">{{ $application->applicant_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold">Primary Mobile Number</label>
                            <div class="text-dark"><i class="fa-solid fa-phone text-muted me-1"></i>{{ $application->mobile }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold">Email Address</label>
                            <div class="text-dark"><i class="fa-solid fa-envelope text-muted me-1"></i>{{ $application->email }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold">Educational Qualification</label>
                            <div class="text-dark">{{ $application->qualification }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold">Submission Timestamp</label>
                            <div class="text-dark">{{ $application->created_at?->format('d M, Y h:i A') }}</div>
                        </div>
                        <div class="col-12"><hr class="my-2"></div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold">State</label>
                            <div class="text-dark fw-semibold">{{ $application->state?->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold">District</label>
                            <div class="text-dark">{{ $application->district?->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold">City / Town</label>
                            <div class="text-dark">{{ $application->city }}</div>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small fw-bold">Complete Facility Address</label>
                            <div class="p-3 bg-light rounded text-dark">{{ $application->address }}</div>
                        </div>
                        @if($application->remarks)
                            <div class="col-12">
                                <label class="text-muted small fw-bold">Reviewer Remarks / Decision Notes</label>
                                <div class="p-3 bg-warning bg-opacity-10 border border-warning rounded text-dark">
                                    <i class="fa-solid fa-comment-dots text-warning me-1"></i>{{ $application->remarks }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Infrastructure Verification Summary Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-microchip text-primary me-2"></i>Infrastructure Setup
                    </h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="p-3 rounded bg-light mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-bold text-uppercase">Proposed Computer PCs</span>
                            <span class="badge bg-primary fs-6">{{ $application->computers_count }}</span>
                        </div>
                        <div class="small text-muted">Dedicated workstations committed for the student laboratory.</div>
                    </div>

                    <div class="p-3 rounded bg-light mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-bold text-uppercase">Carpet Area</span>
                            <span class="badge bg-secondary fs-6">{{ $application->carpet_area_sqft }} Sqft</span>
                        </div>
                        <div class="small text-muted">Classroom & computer laboratory floor plan surface.</div>
                    </div>

                    <div class="alert alert-info border-0 py-2 small mb-0">
                        <i class="fa-solid fa-info-circle me-1"></i> Approving this application will automatically provision a Centre Director user account and initialize a Franchise Wallet.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Approve Application -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.centres.applications.approve', $application) }}" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="approveModalLabel">
                    <i class="fa-solid fa-circle-check me-2"></i>Approve Franchise Affiliation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small">You are approving affiliation for <strong>{{ $application->centre_name }}</strong> under Director <strong>{{ $application->applicant_name }}</strong>.</p>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Centre Code (Optional - leave blank for auto-generation)</label>
                    <input type="text" name="centre_code" class="form-control font-monospace" placeholder="e.g. DCE-DL01">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Verified Lab Capacity (Workstations)</label>
                    <input type="number" name="lab_capacity" class="form-control" value="{{ $application->computers_count }}" min="1">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Affiliation Valid Upto</label>
                    <input type="date" name="valid_upto" class="form-control" value="{{ now()->addYear()->toDateString() }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Board Approval Remarks</label>
                    <textarea name="remarks" class="form-control" rows="2" placeholder="Accreditation approved after premises inspection."></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="fa-solid fa-check me-1"></i> Confirm & Issue Affiliation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Reject Application -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.centres.applications.reject', $application) }}" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="rejectModalLabel">
                    <i class="fa-solid fa-ban me-2"></i>Reject Franchise Application
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small">Please supply the formal reason for rejecting application <strong>#{{ $application->application_no }}</strong>.</p>

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-danger">Rejection Reason *</label>
                    <textarea name="reason" class="form-control" rows="3" required placeholder="e.g. Lab premises do not meet the minimum carpet area / hardware criteria."></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fa-solid fa-xmark me-1"></i> Reject Application
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

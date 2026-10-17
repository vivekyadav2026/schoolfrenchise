@extends('layouts.admin')

@section('title', 'Executive Dashboard - TECH ERA COMPUTER ERP')

@section('content')
<!-- Page Header & Breadcrumb -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">ERP</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">{{ auth()->user()->isSuperAdmin() ? 'Super Admin Executive Dashboard' : 'Executive Institute Dashboard' }}</h3>
        <p class="text-muted small mb-0">Nationwide academic performance, franchise overview, and verified financial ledger.</p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-user-plus me-1"></i> New Admission
        </a>
        <a href="{{ Route::has('admin.fees.create') ? route('admin.fees.create') : (Route::has('admin.fees.index') ? route('admin.fees.index') : '#') }}" class="btn btn-success btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-indian-rupee-sign me-1"></i> Collect Fee
        </a>
    </div>
</div>

<!-- Primary Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="dce-metric-card border-start border-primary border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Students</div>
                    <h3 class="fw-bold text-dark my-1">{{ number_format($totalStudents) }}</h3>
                    <span class="badge bg-success-subtle text-success small">{{ $activeStudents }} Active Enrolled</span>
                </div>
                <div class="metric-icon-box bg-primary-subtle text-primary">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="dce-metric-card border-start border-success border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Collected Fees</div>
                    <h3 class="fw-bold text-success my-1">₹{{ number_format($collectedFees, 2) }}</h3>
                    <span class="badge bg-success-subtle text-success small">Verified Ledger</span>
                </div>
                <div class="metric-icon-box bg-success-subtle text-success">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="dce-metric-card border-start border-danger border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Outstanding Dues</div>
                    <h3 class="fw-bold text-danger my-1">₹{{ number_format($outstandingDues, 2) }}</h3>
                    <span class="badge bg-danger-subtle text-danger small">Pending Installments</span>
                </div>
                <div class="metric-icon-box bg-danger-subtle text-danger">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="dce-metric-card border-start border-warning border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Study Centres</div>
                    <h3 class="fw-bold text-dark my-1">{{ number_format($totalCentres) }}</h3>
                    <span class="badge bg-warning-subtle text-warning small">{{ $activeCentres }} Active Franchises</span>
                </div>
                <div class="metric-icon-box bg-warning-subtle text-warning">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Financial Revenue Reconciliation -->
<div class="card dce-table-card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-scale-balanced text-primary me-2"></i>Financial Reconciliation & Gateways</h6>
        <span class="text-muted small">Updated Real-Time</span>
    </div>
    <div class="card-body p-3">
        <div class="row g-3 text-center">
            <div class="col-md-3">
                <div class="p-3 bg-light rounded-3">
                    <div class="small text-muted text-uppercase fw-semibold">Total Revenue Collected</div>
                    <div class="fw-bold fs-5 text-success">₹{{ number_format($collectedFees, 2) }}</div>
                    <div class="text-muted" style="font-size: 11px;">Cash, UPI & Bank Settlements</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 bg-light rounded-3">
                    <div class="small text-muted text-uppercase fw-semibold">Online Gateway Success</div>
                    <div class="fw-bold fs-5 text-primary">₹{{ number_format($onlineCollected, 2) }}</div>
                    <div class="text-muted" style="font-size: 11px;">Verified Gateway Checkout</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 bg-light rounded-3">
                    <div class="small text-muted text-uppercase fw-semibold">Gateway Pending/Failed</div>
                    <div class="fw-bold fs-5 text-secondary">
                        <span class="text-warning">₹{{ number_format($onlinePending, 2) }}</span> / 
                        <span class="text-danger">₹{{ number_format($onlineFailed, 2) }}</span>
                    </div>
                    <div class="text-muted" style="font-size: 11px;">Unsettled Checkout Attempts</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 bg-light rounded-3">
                    <div class="small text-muted text-uppercase fw-semibold">Total Operating Expenses</div>
                    <div class="fw-bold fs-5 text-danger">₹{{ number_format($totalExpenses, 2) }}</div>
                    <div class="text-muted" style="font-size: 11px;">Salaries, Rent & Utilities</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Admissions & Recent Payments -->
<div class="row g-4 mb-4">
    <!-- Recent Admissions -->
    <div class="col-lg-6">
        <div class="card dce-table-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-user-plus text-primary me-2"></i>Recent Course Admissions</h6>
                <a href="{{ Route::has('admin.admissions.index') ? route('admin.admissions.index') : '#' }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Centre</th>
                                <th>Fee Paid</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAdmissions as $adm)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $adm->student?->full_name }}</div>
                                    <div class="text-muted" style="font-size: 11px;">{{ $adm->student?->roll_no }}</div>
                                </td>
                                <td><span class="badge bg-primary-subtle text-primary">{{ $adm->course?->course_code }}</span></td>
                                <td class="small">{{ $adm->centre?->centre_code }}</td>
                                <td class="fw-bold text-success">₹{{ number_format($adm->total_paid, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No recent admissions found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="col-lg-6">
        <div class="card dce-table-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-receipt text-success me-2"></i>Recent Fee Collections</h6>
                <a href="{{ Route::has('admin.fees.index') ? route('admin.fees.index') : '#' }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Receipt No</th>
                                <th>Student</th>
                                <th>Amount</th>
                                <th>Mode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                            <tr>
                                <td class="font-monospace small fw-bold">{{ $payment->receipt_no }}</td>
                                <td>{{ $payment->student?->full_name }}</td>
                                <td class="fw-bold text-success">₹{{ number_format($payment->amount, 2) }}</td>
                                <td><span class="badge bg-secondary-subtle text-secondary small">{{ strtoupper($payment->payment_mode) }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No payments recorded yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Audit Trail & Operations Summary -->
<div class="card dce-table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Live System Audit Trail</h6>
        <a href="{{ Route::has('admin.audit-logs.index') ? route('admin.audit-logs.index') : '#' }}" class="btn btn-sm btn-link text-decoration-none">Full Audit Log</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>User</th>
                        <th>Entity</th>
                        <th>IP Address</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAuditLogs as $log)
                    <tr>
                        <td><span class="badge bg-dark-subtle text-dark font-monospace">{{ $log->action }}</span></td>
                        <td class="fw-semibold">{{ $log->user?->name ?? 'System Process' }}</td>
                        <td class="small text-muted">{{ class_basename($log->auditable_type ?? '-') }} #{{ $log->auditable_id ?? '-' }}</td>
                        <td class="font-monospace small">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                        <td class="small text-muted">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No audit logs recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

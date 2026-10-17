@extends('layouts.centre')

@section('title', 'Outstanding Dues Report - TECH ERA COMPUTER')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <a href="{{ route('centre.fees.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill mb-2">
            <i class="fa-solid fa-arrow-left me-1"></i>Back to Collections
        </a>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-file-invoice-dollar text-danger me-2"></i>Outstanding Dues Report
        </h3>
        <p class="text-muted small mb-0">List of admitted students with remaining fee installments at study centre {{ $centre->centre_name }} ({{ $centre->centre_code }}).</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="fa-solid fa-print me-1"></i>Print Report
        </button>
    </div>
</div>

<!-- Total Dues Highlight Card -->
<div class="card border-0 rounded-4 shadow-sm mb-4 border-start border-4 border-danger">
    <div class="card-body p-4">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <div class="text-muted small fw-semibold text-uppercase">Total Centre Outstanding Balance</div>
                <h2 class="fw-bold text-danger my-1">₹{{ number_format($totalOutstanding, 2) }}</h2>
                <span class="small text-muted">Across {{ $admissionsWithDues->total() }} candidate accounts with pending installments</span>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('centre.fees.create') }}" class="btn btn-success rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-hand-holding-dollar me-1"></i>Record Fee Collection
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Dues Table -->
<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Roll No & Reg No</th>
                        <th>Student Name & Contact</th>
                        <th>Course</th>
                        <th>Net Course Fee</th>
                        <th>Amount Paid</th>
                        <th>Outstanding Due</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admissionsWithDues as $adm)
                    @php $due = max(0, $adm->net_fee - $adm->total_paid); @endphp
                    <tr>
                        <td>
                            <span class="badge bg-primary-subtle text-primary font-monospace fw-bold">{{ $adm->student?->roll_no }}</span>
                            <div class="small text-muted font-monospace" style="font-size: 0.72rem;">{{ $adm->student?->registration_no }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $adm->student?->full_name }}</div>
                            <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $adm->student?->mobile }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary">{{ $adm->course?->course_code }}</span>
                            <div class="small text-muted text-truncate" style="max-width: 160px;">{{ $adm->course?->course_name }}</div>
                        </td>
                        <td>₹{{ number_format($adm->net_fee, 2) }}</td>
                        <td class="text-success fw-bold">₹{{ number_format($adm->total_paid, 2) }}</td>
                        <td class="text-danger fw-bold fs-6">₹{{ number_format($due, 2) }}</td>
                        <td class="text-end">
                            <a href="{{ route('centre.fees.create', ['admission_id' => $adm->id]) }}" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold">
                                <i class="fa-solid fa-indian-rupee-sign me-1"></i>Collect Fee
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-success">
                            <i class="fa-solid fa-circle-check fs-1 text-success mb-3 d-block"></i>
                            <h5>No Outstanding Dues!</h5>
                            <p class="small text-muted mb-0">All admitted students at this centre have completely settled their course fees.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($admissionsWithDues->hasPages())
        <div class="p-3 border-top">
            {{ $admissionsWithDues->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

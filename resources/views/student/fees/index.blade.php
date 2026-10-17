@extends('layouts.student')

@section('title', 'My Fees & Instalments - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-receipt text-primary me-2"></i> Fees & Instalment Schedule
            </h4>
            <div class="text-muted small">Course fee ledger, installment dues and payment history</div>
        </div>
        <div class="d-flex gap-2">
            @if($totalDues > 0)
                <a href="{{ route('student.fees.pay') }}" class="btn btn-primary rounded-pill px-4 btn-sm shadow-sm">
                    <i class="fa-solid fa-credit-card me-1"></i> Pay Outstanding Dues
                </a>
            @endif
            <a href="{{ route('student.fees.history') }}" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
                <i class="fa-solid fa-clock-rotate-left me-1"></i> Payment Ledger
            </a>
        </div>
    </div>

    <!-- Top 3 Financial Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Net Course Fee</span>
                    <div class="p-2 rounded-3 bg-primary-subtle text-primary">
                        <i class="fa-solid fa-graduation-cap fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-1">₹{{ number_format($totalFees, 2) }}</h3>
                <div class="text-muted small">
                    Course: <strong>{{ $admission->course?->course_name }}</strong>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-success small text-uppercase fw-semibold">Total Paid to Date</span>
                    <div class="p-2 rounded-3 bg-success-subtle text-success">
                        <i class="fa-solid fa-circle-check fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-success mb-1">₹{{ number_format($totalPaid, 2) }}</h3>
                <div class="text-muted small">
                    {{ $totalFees > 0 ? round(($totalPaid / $totalFees) * 100) : 0 }}% of total fee cleared
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-danger small text-uppercase fw-semibold">Remaining Balance</span>
                    <div class="p-2 rounded-3 {{ $totalDues > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                        <i class="fa-solid {{ $totalDues > 0 ? 'fa-hourglass-half' : 'fa-check-double' }} fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold {{ $totalDues > 0 ? 'text-danger' : 'text-success' }} mb-1">
                    ₹{{ number_format($totalDues, 2) }}
                </h3>
                <div class="small {{ $totalDues > 0 ? 'text-danger' : 'text-success' }}">
                    {{ $totalDues > 0 ? 'Pending settlement' : 'All accounts settled' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Instalments Schedule Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-list-ol text-primary me-2"></i> Instalment Breakdown & Due Dates
            </h5>
            <span class="badge bg-light text-muted">{{ $fees->count() }} Instalments</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #64748b;">
                        <tr>
                            <th class="ps-4">Term</th>
                            <th>Description</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fees as $fee)
                            @php
                                $balance = max(0, (float) $fee->amount - (float) $fee->paid_amount);
                            @endphp
                            <tr>
                                <td class="ps-4 font-monospace fw-bold small text-primary">#{{ $fee->installment_no }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $fee->title }}</div>
                                </td>
                                <td class="small text-muted text-nowrap">
                                    {{ $fee->due_date ? $fee->due_date->format('d M, Y') : 'Immediate' }}
                                </td>
                                <td class="fw-semibold text-dark">₹{{ number_format($fee->amount, 2) }}</td>
                                <td class="text-success fw-semibold">₹{{ number_format($fee->paid_amount, 2) }}</td>
                                <td class="fw-bold {{ $balance > 0 ? 'text-danger' : 'text-muted' }}">
                                    ₹{{ number_format($balance, 2) }}
                                </td>
                                <td>
                                    @if($fee->status === 'paid')
                                        <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill small">
                                            <i class="fa-solid fa-circle-check me-1"></i> Paid
                                        </span>
                                    @elseif($fee->status === 'partial')
                                        <span class="badge bg-warning-subtle text-warning px-3 py-1 rounded-pill small">
                                            <i class="fa-solid fa-circle-dot me-1"></i> Partial
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill small">
                                            <i class="fa-solid fa-clock me-1"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    @if($balance > 0)
                                        <a href="{{ route('student.fees.pay') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                            Pay Now
                                        </a>
                                    @else
                                        <span class="text-success small fw-semibold"><i class="fa-solid fa-check"></i> Cleared</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-receipt fa-2x mb-2 text-secondary opacity-50"></i>
                                    <p class="mb-0">No installment schedule configured yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

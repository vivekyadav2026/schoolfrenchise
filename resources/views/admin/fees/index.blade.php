@extends('layouts.admin')

@section('title', 'Fees & Payments Management')

@section('content')
<div class="container-fluid p-0">
    <!-- Header with Action Buttons -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a;">
                <i class="fa-solid fa-indian-rupee-sign me-2" style="color: #0891b2;"></i>Fees & Payment Collections
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Fees & Payments</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.fees.dues') }}" class="btn btn-outline-warning">
                <i class="fa-solid fa-hand-holding-dollar me-1"></i> Outstanding Dues Roster
            </a>
            <a href="{{ route('admin.fees.create') }}" class="btn btn-primary" style="background-color: #0891b2; border-color: #0891b2;">
                <i class="fa-solid fa-plus-circle me-1"></i> Collect Fee Payment
            </a>
        </div>
    </div>

    <!-- Financial Statistics Cards (Strictly separate collected, dues, discounts) -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Collected</div>
                        <h3 class="fw-bold my-1 text-success">₹{{ number_format($stats['total_collected'], 2) }}</h3>
                        <div class="small text-muted">All settled installments</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #dcfce7; color: #16a34a;">
                        <i class="fa-solid fa-cash-register fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Outstanding Dues</div>
                        <h3 class="fw-bold my-1 text-danger">₹{{ number_format($stats['total_dues'], 2) }}</h3>
                        <div class="small text-muted">Pending from active students</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fee2e2; color: #dc2626;">
                        <i class="fa-solid fa-clock-rotate-left fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Online / UPI Collections</div>
                        <h3 class="fw-bold my-1 text-primary">₹{{ number_format($stats['online_collected'], 2) }}</h3>
                        <div class="small text-muted">Digital gateway & QR</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #eff6ff; color: #2563eb;">
                        <i class="fa-solid fa-qrcode fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Cash Collections</div>
                        <h3 class="fw-bold my-1" style="color: #0f172a;">₹{{ number_format($stats['cash_collected'], 2) }}</h3>
                        <div class="small text-muted">Centre physical counter</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #f1f5f9; color: #475569;">
                        <i class="fa-solid fa-money-bill-wave fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.fees.index') }}" class="row g-2">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Receipt No, Student, Roll No..." value="{{ request('search') }}">
                    </div>
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
                    <select name="payment_mode" class="form-select bg-light">
                        <option value="">All Payment Modes</option>
                        <option value="cash" {{ request('payment_mode') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="upi" {{ request('payment_mode') === 'upi' ? 'selected' : '' }}>UPI / QR</option>
                        <option value="online" {{ request('payment_mode') === 'online' ? 'selected' : '' }}>Online Gateway</option>
                        <option value="cheque" {{ request('payment_mode') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="dd" {{ request('payment_mode') === 'dd' ? 'selected' : '' }}>Demand Draft</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control bg-light" placeholder="From Date" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control bg-light" placeholder="To Date" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100 px-0" style="background-color: #0891b2; border-color: #0891b2;" title="Filter">
                        <i class="fa-solid fa-filter"></i>
                    </button>
                    <a href="{{ route('admin.fees.index') }}" class="btn btn-outline-secondary px-2" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Ledger Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-receipt me-2 text-primary"></i>Fee Collections Ledger
            </h6>
            <a href="{{ route('admin.fees.dues') }}" class="btn btn-sm btn-outline-warning">
                <i class="fa-solid fa-hand-holding-dollar me-1"></i> View Students with Dues
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3">Receipt No</th>
                        <th>Student Particulars</th>
                        <th>Course Curriculum</th>
                        <th>Amount Paid</th>
                        <th>Payment Mode</th>
                        <th>Payment Date</th>
                        <th>Collected By</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $pay)
                        <tr>
                            <td class="ps-3">
                                <span class="fw-bold font-monospace text-primary">{{ $pay->receipt_no }}</span>
                                @if($pay->transaction_ref)
                                    <div class="small text-muted font-monospace">Ref: {{ $pay->transaction_ref }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $pay->student->full_name ?? 'N/A' }}</div>
                                <div class="small text-muted font-monospace">Roll: {{ $pay->student->roll_no ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $pay->admission->course->course_name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $pay->centre->centre_name ?? 'Head Office' }}</small>
                            </td>
                            <td>
                                <span class="fw-bold fs-6 text-success">₹{{ number_format($pay->amount, 2) }}</span>
                            </td>
                            <td>
                                @if($pay->payment_mode === 'cash')
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1"><i class="fa-solid fa-money-bill-wave me-1"></i>Cash</span>
                                @elseif($pay->payment_mode === 'upi')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-mobile-screen me-1"></i>UPI / QR</span>
                                @elseif($pay->payment_mode === 'online')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"><i class="fa-solid fa-credit-card me-1"></i>Online</span>
                                @else
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 text-uppercase">{{ $pay->payment_mode }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $pay->payment_date ? \Carbon\Carbon::parse($pay->payment_date)->format('d M, Y') : 'N/A' }}</div>
                                <small class="text-muted">{{ $pay->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <small class="text-secondary">{{ $pay->collector->name ?? 'Admin Staff' }}</small>
                            </td>
                            <td class="text-end pe-3">
                                @if($pay->receipt)
                                    <a href="{{ route('admin.fees.receipt', $pay->receipt) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="View & Print Computerized Receipt">
                                        <i class="fa-solid fa-print me-1"></i> Receipt
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fa-solid fa-indian-rupee-sign fs-1 text-secondary opacity-50"></i></div>
                                <h6>No Payment Transactions Found</h6>
                                <p class="small mb-3">Collect fee installments for enrolled candidates to generate computerized GST receipts.</p>
                                <a href="{{ route('admin.fees.create') }}" class="btn btn-sm btn-primary" style="background-color: #0891b2; border-color: #0891b2;">
                                    <i class="fa-solid fa-plus-circle me-1"></i> Collect Fee Payment Now
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

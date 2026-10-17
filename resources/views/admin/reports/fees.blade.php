@extends('layouts.admin')

@section('title', 'Fees & Revenue Collection Report')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Fees & Financials</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-indian-rupee-sign text-success me-2"></i>Fees & Financial Collection Report
            </h2>
            <p class="text-muted small mb-0">Audited revenue intake strictly separating cleared receipts, pending clearances, failed payments, and refunds.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Reports Hub
            </a>
            <a href="{{ route('admin.reports.fees.export', request()->query()) }}" class="btn btn-success shadow-sm">
                <i class="fa-solid fa-file-csv me-1"></i> Export Financial CSV
            </a>
        </div>
    </div>

    <!-- Financial Accounting Separation Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Total Cleared Collections</div>
                    <div class="h4 fw-bold text-success mb-0">₹{{ number_format($stats['total_collected'], 2) }}</div>
                    <div class="text-muted small mt-1">Verified funds received</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Online / UPI Intake</div>
                    <div class="h4 fw-bold text-info mb-0">₹{{ number_format($stats['online_mode'], 2) }}</div>
                    <div class="text-muted small mt-1">Digital payment gateways</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Cash / Bank Transfer</div>
                    <div class="h4 fw-bold text-primary mb-0">₹{{ number_format($stats['offline_mode'], 2) }}</div>
                    <div class="text-muted small mt-1">Direct centre collections</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-secondary">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Refunded Amounts</div>
                    <div class="h4 fw-bold text-secondary mb-0">₹{{ number_format($stats['total_refunded'], 2) }}</div>
                    <div class="text-muted small mt-1">Fee reversals issued</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-dark">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Total Outstanding Dues</div>
                    <div class="h4 fw-bold text-dark mb-0">₹{{ number_format($stats['total_dues'], 2) }}</div>
                    <div class="text-muted small mt-1">Active student arrears</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.fees') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search receipt, UTR, student..." value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <select name="centre_id" class="form-select form-select-sm">
                        <option value="">All Centres</option>
                        @foreach($centres as $c)
                            <option value="{{ $c->id }}" {{ request('centre_id') == $c->id ? 'selected' : '' }}>{{ $c->centre_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="payment_mode" class="form-select form-select-sm">
                        <option value="">All Payment Modes</option>
                        <option value="upi" {{ request('payment_mode') == 'upi' ? 'selected' : '' }}>UPI / QR</option>
                        <option value="cash" {{ request('payment_mode') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank_transfer" {{ request('payment_mode') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer (NEFT/IMPS)</option>
                        <option value="cheque" {{ request('payment_mode') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="card" {{ request('payment_mode') == 'card' ? 'selected' : '' }}>Debit / Credit Card</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-1">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" title="Date From">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" title="Date To">
                </div>

                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Apply Filter"><i class="fa-solid fa-filter"></i></button>
                    @if(request()->hasAny(['search', 'centre_id', 'payment_mode', 'date_from', 'date_to']))
                        <a href="{{ route('admin.reports.fees') }}" class="btn btn-sm btn-light border" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Receipt No</th>
                        <th>Student Particulars</th>
                        <th>Centre / Branch</th>
                        <th class="text-end">Amount</th>
                        <th>Payment Mode</th>
                        <th>Payment Date</th>
                        <th>Transaction Ref</th>
                        <th class="text-end pe-3">Audit Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                        <tr>
                            <td class="ps-3 font-monospace">
                                <a href="{{ route('admin.fees.receipt', $p) }}" target="_blank" class="fw-bold text-primary text-decoration-none">
                                    {{ $p->receipt_no ?? ('RCP-' . $p->id) }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $p->student ? $p->student->full_name : '-' }}</div>
                                <div class="small text-muted font-monospace">{{ $p->student ? $p->student->roll_no : '-' }}</div>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">{{ $p->centre ? $p->centre->centre_name : '-' }}</div>
                            </td>
                            <td class="text-end font-monospace fw-bold text-dark">
                                ₹{{ number_format($p->amount, 2) }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-uppercase">{{ str_replace('_', ' ', $p->payment_mode) }}</span>
                            </td>
                            <td class="font-monospace small text-muted">
                                {{ $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d M, Y') : '-' }}
                            </td>
                            <td class="font-monospace small text-muted">
                                {{ $p->transaction_ref ?? '-' }}
                            </td>
                            <td class="text-end pe-3">
                                <span class="badge bg-success">Cleared</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                No fee payment records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

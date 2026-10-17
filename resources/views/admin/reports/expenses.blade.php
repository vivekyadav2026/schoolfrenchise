@extends('layouts.admin')

@section('title', 'Expenses & Cost Outflow Report')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Expenses</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-file-invoice-dollar text-danger me-2"></i>Operating & Marketing Expense Report
            </h2>
            <p class="text-muted small mb-0">Reconcile administrative overhead, branch payouts, utilities, hardware investments, and ad spending.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Reports Hub
            </a>
            <a href="{{ route('admin.reports.expenses.export', request()->query()) }}" class="btn btn-success shadow-sm">
                <i class="fa-solid fa-file-csv me-1"></i> Export Expense CSV
            </a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Operating Expenses</div>
                    <div class="h4 fw-bold text-danger mb-0">₹{{ number_format($stats['operating_total'], 2) }}</div>
                    <div class="text-muted small mt-1">Rent, hardware, salaries, utilities</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Marketing & Ad Spends</div>
                    <div class="h4 fw-bold text-warning mb-0">₹{{ number_format($stats['marketing_total'], 2) }}</div>
                    <div class="text-muted small mt-1">Digital ads & print promotions</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-dark">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Total Combined Outflow</div>
                    <div class="h4 fw-bold text-dark mb-0">₹{{ number_format($stats['combined_total'], 2) }}</div>
                    <div class="text-muted small mt-1">Total institutional disbursements</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.expenses') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="centre_id" class="form-select form-select-sm">
                        <option value="">All Centres / Head Office</option>
                        @foreach($centres as $c)
                            <option value="{{ $c->id }}" {{ request('centre_id') == $c->id ? 'selected' : '' }}>{{ $c->centre_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="category" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        <option value="rent" {{ request('category') == 'rent' ? 'selected' : '' }}>Rent</option>
                        <option value="electricity" {{ request('category') == 'electricity' ? 'selected' : '' }}>Electricity</option>
                        <option value="salaries" {{ request('category') == 'salaries' ? 'selected' : '' }}>Salaries</option>
                        <option value="hardware" {{ request('category') == 'hardware' ? 'selected' : '' }}>Hardware</option>
                        <option value="internet" {{ request('category') == 'internet' ? 'selected' : '' }}>Internet</option>
                        <option value="stationery" {{ request('category') == 'stationery' ? 'selected' : '' }}>Stationery</option>
                        <option value="maintenance" {{ request('category') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="payment_mode" class="form-select form-select-sm">
                        <option value="">All Payment Modes</option>
                        <option value="bank_transfer" {{ request('payment_mode') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="upi" {{ request('payment_mode') == 'upi' ? 'selected' : '' }}>UPI / QR</option>
                        <option value="cash" {{ request('payment_mode') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="cheque" {{ request('payment_mode') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="card" {{ request('payment_mode') == 'card' ? 'selected' : '' }}>Card</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-1">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" title="Date From">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" title="Date To">
                </div>

                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Apply Filter"><i class="fa-solid fa-filter"></i></button>
                    @if(request()->hasAny(['centre_id', 'category', 'payment_mode', 'date_from', 'date_to']))
                        <a href="{{ route('admin.reports.expenses') }}" class="btn btn-sm btn-light border" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Date</th>
                        <th>Particulars / Payee</th>
                        <th>Category</th>
                        <th>Centre / Branch</th>
                        <th class="text-end">Amount</th>
                        <th>Mode</th>
                        <th>Voucher / Ref</th>
                        <th class="text-end pe-3">Audited By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $e)
                        <tr>
                            <td class="ps-3 font-monospace small text-muted">
                                {{ $e->expense_date ? $e->expense_date->format('d M, Y') : '-' }}
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $e->title }}</div>
                                @if($e->notes)
                                    <div class="small text-muted">{{ Str::limit($e->notes, 40) }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-uppercase">{{ $e->category }}</span>
                            </td>
                            <td>
                                <div class="small text-dark">{{ $e->centre ? $e->centre->centre_name : 'Central Head Office' }}</div>
                            </td>
                            <td class="text-end font-monospace fw-bold text-danger">
                                ₹{{ number_format($e->amount, 2) }}
                            </td>
                            <td>
                                <span class="badge bg-light text-muted border text-uppercase">{{ str_replace('_', ' ', $e->payment_mode) }}</span>
                            </td>
                            <td class="font-monospace small text-muted">
                                {{ $e->reference_no ?? '-' }}
                            </td>
                            <td class="text-end pe-3 small text-muted">
                                {{ $e->recorder ? $e->recorder->name : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                No expense disbursements logged for specified filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

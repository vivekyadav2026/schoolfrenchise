@extends('layouts.admin')

@section('title', 'Financial P&L & Cashflow Statement')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Financial Statement</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-chart-line text-success me-2"></i>Institutional Financial Statement & P&L
            </h2>
            <p class="text-muted small mb-0">Reconcile Gross Fee Collections, Refunds, Operating Overheads, Marketing Spends, and Net Surplus.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Reports Hub
            </a>
            <a href="{{ route('admin.reports.financial.export', request()->query()) }}" class="btn btn-success shadow-sm">
                <i class="fa-solid fa-file-csv me-1"></i> Export Financial CSV
            </a>
        </div>
    </div>

    <!-- Top KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Gross Collections</div>
                    <div class="h5 fw-bold text-primary mb-0">₹{{ number_format($stats['gross_collections'], 2) }}</div>
                    <div class="text-muted small mt-1">Total fees received</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Refunds & Reversals</div>
                    <div class="h5 fw-bold text-danger mb-0">₹{{ number_format($stats['total_refunds'], 2) }}</div>
                    <div class="text-muted small mt-1">Cancelled receipts</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Net Collections</div>
                    <div class="h5 fw-bold text-info mb-0">₹{{ number_format($stats['net_collections'], 2) }}</div>
                    <div class="text-muted small mt-1">Realized income</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-secondary">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Operating Expenses</div>
                    <div class="h5 fw-bold text-secondary mb-0">₹{{ number_format($stats['operating_expenses'], 2) }}</div>
                    <div class="text-muted small mt-1">Rent, payroll, bills</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Marketing Spends</div>
                    <div class="h5 fw-bold text-warning mb-0">₹{{ number_format($stats['marketing_expenses'], 2) }}</div>
                    <div class="text-muted small mt-1">Digital & print ads</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 {{ $stats['net_profit'] >= 0 ? 'border-success' : 'border-danger' }}">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Net Surplus / Margin</div>
                    <div class="h5 fw-bold {{ $stats['net_profit'] >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                        ₹{{ number_format($stats['net_profit'], 2) }}
                    </div>
                    <div class="small fw-semibold {{ $stats['net_profit'] >= 0 ? 'text-success' : 'text-danger' }} mt-1">
                        {{ $stats['profit_margin'] }}% Margin
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.financial') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1 fw-bold">Accredited Study Centre</label>
                    <select name="centre_id" class="form-select form-select-sm">
                        <option value="">All Centres / Head Office Consolidated</option>
                        @foreach($centres as $c)
                            <option value="{{ $c->id }}" {{ request('centre_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->centre_name }} ({{ $c->centre_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1 fw-bold">Date From</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control form-control-sm">
                </div>

                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1 fw-bold">Date To</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control form-control-sm">
                </div>

                <div class="col-md-2 d-flex gap-2 align-items-end" style="padding-top: 1.55rem;">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1 shadow-sm">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.reports.financial') }}" class="btn btn-outline-secondary btn-sm shadow-sm" title="Reset">
                        <i class="fa-solid fa-rotate"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Expense Breakdown Widgets -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-pie-chart text-secondary me-2"></i>Operating Expenses by Category</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Category</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-end">Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['top_categories'] as $cat)
                                <tr>
                                    <td class="fw-semibold">
                                        <span class="badge bg-light text-dark border me-1">{{ ucfirst(str_replace('_', ' ', $cat->category)) }}</span>
                                    </td>
                                    <td class="text-end fw-bold">₹{{ number_format($cat->cat_total, 2) }}</td>
                                    <td class="text-end text-muted small">
                                        {{ $stats['operating_expenses'] > 0 ? round(($cat->cat_total / $stats['operating_expenses']) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted small">No operating expenses recorded for selected criteria.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-bullhorn text-warning me-2"></i>Marketing Spends by Platform</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Platform / Channel</th>
                                    <th class="text-end">Total Spent</th>
                                    <th class="text-end">Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['top_platforms'] as $plat)
                                <tr>
                                    <td class="fw-semibold">
                                        <span class="badge bg-warning bg-opacity-10 text-dark border border-warning me-1">{{ ucfirst(str_replace('_', ' ', $plat->platform)) }}</span>
                                    </td>
                                    <td class="text-end fw-bold">₹{{ number_format($plat->plat_total, 2) }}</td>
                                    <td class="text-end text-muted small">
                                        {{ $stats['marketing_expenses'] > 0 ? round(($plat->plat_total / $stats['marketing_expenses']) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted small">No marketing expenses logged in this date range.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date-wise Daily Timeline Breakdown -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-calendar-days text-primary me-2"></i>Date-Wise Financial Flow & Net Reconciliation
            </h6>
            <span class="badge bg-light text-muted border">{{ count($dailyTimeline) }} Active Dates</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Date</th>
                            <th class="text-end">Gross Collections</th>
                            <th class="text-end">Operating Expenses</th>
                            <th class="text-end">Marketing Spends</th>
                            <th class="text-end">Total Outflow</th>
                            <th class="text-end">Daily Net Surplus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyTimeline as $row)
                        <tr>
                            <td class="fw-semibold text-dark">
                                <i class="fa-regular fa-calendar text-muted me-1"></i>
                                {{ \Carbon\Carbon::parse($row['date'])->format('d M, Y (D)') }}
                            </td>
                            <td class="text-end fw-bold text-primary">₹{{ number_format($row['gross_collections'], 2) }}</td>
                            <td class="text-end text-muted">₹{{ number_format($row['operating_expenses'], 2) }}</td>
                            <td class="text-end text-muted">₹{{ number_format($row['marketing_expenses'], 2) }}</td>
                            <td class="text-end fw-semibold text-danger">₹{{ number_format($row['total_expenses'], 2) }}</td>
                            <td class="text-end fw-bold {{ $row['net_surplus'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $row['net_surplus'] >= 0 ? '+' : '' }}₹{{ number_format($row['net_surplus'], 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No transactions or expenses recorded in the selected period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($dailyTimeline) > 0)
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td>CONSOLIDATED TOTAL</td>
                            <td class="text-end text-primary">₹{{ number_format($stats['gross_collections'], 2) }}</td>
                            <td class="text-end text-muted">₹{{ number_format($stats['operating_expenses'], 2) }}</td>
                            <td class="text-end text-muted">₹{{ number_format($stats['marketing_expenses'], 2) }}</td>
                            <td class="text-end text-danger">₹{{ number_format($stats['total_expenses'], 2) }}</td>
                            <td class="text-end {{ $stats['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $stats['net_profit'] >= 0 ? '+' : '' }}₹{{ number_format($stats['net_profit'], 2) }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

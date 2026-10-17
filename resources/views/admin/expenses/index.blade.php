@extends('layouts.admin')

@section('title', 'Operating Expenses & Cost Ledger')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Expenses</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-calculator text-primary me-2"></i>Institutional & Operating Expenses
            </h2>
            <p class="text-muted small mb-0">Record daily recurring costs, hardware purchases, facility utilities, staff salaries, and marketing investments.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.expenses.marketing') }}" class="btn btn-outline-info shadow-sm">
                <i class="fa-solid fa-bullhorn me-1"></i> Marketing Campaigns
            </a>
            <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-plus-circle me-1"></i> Record New Expense
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

    <!-- Statistics KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Total Lifetime Expenses</span>
                        <div class="rounded-circle bg-danger bg-opacity-10 p-2 text-danger">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-danger mb-0">₹{{ number_format($stats['total_expenses'], 2) }}</h3>
                    <div class="text-muted small mt-1">All operating overheads</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">This Month Expenses</span>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-2 text-warning">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-warning mb-0">₹{{ number_format($stats['month_expenses'], 2) }}</h3>
                    <div class="text-muted small mt-1">{{ now()->format('F Y') }} running costs</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Marketing Spent</span>
                        <div class="rounded-circle bg-info bg-opacity-10 p-2 text-info">
                            <i class="fa-solid fa-bullhorn"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-info mb-0">₹{{ number_format($stats['marketing_total'], 2) }}</h3>
                    <div class="text-muted small mt-1">Promotional advertising</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-secondary">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Top Cost Center</span>
                        <div class="rounded-circle bg-secondary bg-opacity-10 p-2 text-secondary">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                    </div>
                    <h3 class="h5 fw-bold text-dark mb-0 text-truncate">
                        {{ $stats['top_category'] ? ucfirst($stats['top_category']->category) : 'N/A' }}
                    </h3>
                    <div class="text-muted small mt-1">
                        @if($stats['top_category'])
                            ₹{{ number_format($stats['top_category']->total, 2) }} incurred
                        @else
                            No data logged
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.expenses.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search payee, reference..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <select name="category" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        <option value="rent" {{ request('category') === 'rent' ? 'selected' : '' }}>Facility Rent</option>
                        <option value="electricity" {{ request('category') === 'electricity' ? 'selected' : '' }}>Electricity & Utilities</option>
                        <option value="salaries" {{ request('category') === 'salaries' ? 'selected' : '' }}>Staff Salaries</option>
                        <option value="hardware" {{ request('category') === 'hardware' ? 'selected' : '' }}>Computer Hardware / Lab</option>
                        <option value="internet" {{ request('category') === 'internet' ? 'selected' : '' }}>Internet & Telecom</option>
                        <option value="stationery" {{ request('category') === 'stationery' ? 'selected' : '' }}>Printing & Stationery</option>
                        <option value="maintenance" {{ request('category') === 'maintenance' ? 'selected' : '' }}>Repairs & Maintenance</option>
                        <option value="marketing" {{ request('category') === 'marketing' ? 'selected' : '' }}>Offline Marketing</option>
                        <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Miscellaneous</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="centre_id" class="form-select form-select-sm">
                        <option value="">All Centres / Head Office</option>
                        @foreach($centres as $cnt)
                            <option value="{{ $cnt->id }}" {{ request('centre_id') == $cnt->id ? 'selected' : '' }}>
                                {{ $cnt->centre_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="payment_mode" class="form-select form-select-sm">
                        <option value="">All Payment Modes</option>
                        <option value="cash" {{ request('payment_mode') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank_transfer" {{ request('payment_mode') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer (NEFT/IMPS)</option>
                        <option value="upi" {{ request('payment_mode') === 'upi' ? 'selected' : '' }}>UPI</option>
                        <option value="cheque" {{ request('payment_mode') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="card" {{ request('payment_mode') === 'card' ? 'selected' : '' }}>Card</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'category', 'centre_id', 'payment_mode', 'date_from', 'date_to']))
                        <a href="{{ route('admin.expenses.index') }}" class="btn btn-sm btn-light border" title="Clear Filters">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0 text-dark">
                Operating Cost Entries <span class="badge bg-light text-dark fw-normal border ms-1">{{ $expenses->total() }}</span>
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3">Date</th>
                        <th>Payee / Title</th>
                        <th>Category</th>
                        <th>Facility / Centre</th>
                        <th>Payment Mode</th>
                        <th class="text-end">Amount</th>
                        <th class="text-center">Receipt Bill</th>
                        <th>Recorded By</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-semibold text-dark">{{ $exp->expense_date?->format('d M, Y') }}</div>
                                @if($exp->reference_no)
                                    <div class="small text-muted font-monospace">Ref: {{ $exp->reference_no }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $exp->title }}</div>
                                @if($exp->notes)
                                    <div class="small text-muted text-truncate" style="max-width: 250px;">{{ $exp->notes }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ ucfirst($exp->category) }}
                                </span>
                            </td>
                            <td>
                                @if($exp->centre)
                                    <div class="fw-semibold text-dark">{{ $exp->centre->centre_name }}</div>
                                    <div class="small text-muted font-monospace">{{ $exp->centre->centre_code }}</div>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border">Head Office Direct</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-uppercase">{{ str_replace('_', ' ', $exp->payment_mode) }}</span>
                            </td>
                            <td class="text-end font-monospace fw-bold text-danger">
                                ₹{{ number_format($exp->amount, 2) }}
                            </td>
                            <td class="text-center">
                                @if($exp->receipt_doc)
                                    <a href="{{ asset('storage/' . $exp->receipt_doc) }}" target="_blank" class="btn btn-sm btn-light border text-primary" title="View Uploaded Voucher">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="small">
                                {{ $exp->recorder?->name ?? 'System' }}
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.expenses.edit', $exp) }}" class="btn btn-light border text-dark" title="Edit Entry">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.expenses.destroy', $exp) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this expense entry?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light border text-danger" title="Delete Entry">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-calculator fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h6>No Operating Expenses Found</h6>
                                    <p class="small text-muted mb-3">Record facility overheads, utilities, and vendor payouts.</p>
                                    <a href="{{ route('admin.expenses.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fa-solid fa-plus-circle me-1"></i> Record Expense
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="card-footer bg-white py-3 border-0">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

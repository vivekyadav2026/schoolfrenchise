@extends('layouts.admin')

@section('title', 'Franchise Wallet & Transaction Ledger')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Wallet History</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-wallet text-primary me-2"></i>Franchise Wallet & Ledger History
            </h2>
            <p class="text-muted small mb-0">Monitor nationwide franchise wallet balances, credit top-ups, debit deductions, and audit ledger adjustments.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.wallet.adjust') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-scale-balanced me-1"></i> Manual Ledger Adjustment
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Circulating Balance</span>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 text-primary">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-dark mb-0">₹{{ number_format($stats['total_circulating_balance'], 2) }}</h3>
                    <div class="text-muted small mt-1">Total active franchise funds</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Lifetime Credits</span>
                        <div class="rounded-circle bg-success bg-opacity-10 p-2 text-success">
                            <i class="fa-solid fa-arrow-down-left text-success"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-success mb-0">₹{{ number_format($stats['total_lifetime_credited'], 2) }}</h3>
                    <div class="text-muted small mt-1">Total credited into centres</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Lifetime Debits</span>
                        <div class="rounded-circle bg-danger bg-opacity-10 p-2 text-danger">
                            <i class="fa-solid fa-arrow-up-right text-danger"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-danger mb-0">₹{{ number_format($stats['total_lifetime_debited'], 2) }}</h3>
                    <div class="text-muted small mt-1">Deducted for admissions & kits</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Pending Top-ups</span>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-2 text-warning">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-warning mb-0">
                        {{ $stats['pending_recharge_count'] }} <span class="fs-6 fw-normal text-muted">(₹{{ number_format($stats['pending_recharge_amount'], 2) }})</span>
                    </h3>
                    <div class="text-muted small mt-1">Awaiting approval</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.wallet.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Reference, remarks, centre..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="centre_id" class="form-select form-select-sm">
                        <option value="">All Centres</option>
                        @foreach($centres as $cnt)
                            <option value="{{ $cnt->id }}" {{ request('centre_id') == $cnt->id ? 'selected' : '' }}>
                                {{ $cnt->centre_name }} ({{ $cnt->centre_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="credit" {{ request('type') === 'credit' ? 'selected' : '' }}>Credit (+)</option>
                        <option value="debit" {{ request('type') === 'debit' ? 'selected' : '' }}>Debit (-)</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'centre_id', 'type', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('admin.wallet.index') }}" class="btn btn-sm btn-light border" title="Clear Filters">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0 text-dark">
                Transaction Ledger <span class="badge bg-light text-dark fw-normal border ms-1">{{ $transactions->total() }}</span>
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3">Date / ID</th>
                        <th>Centre Particulars</th>
                        <th class="text-center">Type</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Balance After</th>
                        <th>Reference / UTR</th>
                        <th>Description / Remarks</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $txn)
                        <tr>
                            <td class="ps-3">
                                <span class="font-monospace small text-muted">#{{ $txn->id }}</span>
                                <div class="small fw-semibold text-dark">{{ $txn->created_at?->format('d M, Y') }}</div>
                                <div class="small text-muted" style="font-size: 10px;">{{ $txn->created_at?->format('h:i A') }}</div>
                            </td>
                            <td>
                                @if($txn->centre)
                                    <div class="fw-bold text-dark">{{ $txn->centre->centre_name }}</div>
                                    <div class="small text-primary font-monospace">{{ $txn->centre->centre_code }}</div>
                                @else
                                    <div class="text-muted small">Central System</div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($txn->type === 'credit')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="fa-solid fa-arrow-down-left me-1 small"></i>Credit
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="fa-solid fa-arrow-up-right me-1 small"></i>Debit
                                    </span>
                                @endif
                            </td>
                            <td class="text-end font-monospace fw-bold {{ $txn->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $txn->type === 'credit' ? '+' : '-' }}₹{{ number_format($txn->amount, 2) }}
                            </td>
                            <td class="text-end font-monospace text-dark">
                                ₹{{ number_format($txn->balance_after, 2) }}
                            </td>
                            <td>
                                <span class="font-monospace small text-dark">{{ $txn->reference_no ?? 'SYSTEM-GEN' }}</span>
                            </td>
                            <td>
                                <div class="small text-dark" style="max-width: 250px;">{{ $txn->description }}</div>
                                @if($txn->approver)
                                    <div class="small text-muted" style="font-size: 10px;">By: {{ $txn->approver->name }}</div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($txn->status === 'approved')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Approved</span>
                                @elseif($txn->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Pending</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Rejected</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                @if($txn->status === 'pending')
                                    <div class="btn-group btn-group-sm">
                                        <form action="{{ route('admin.wallet.transactions.approve', $txn) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve & Credit">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $txn->id }}" title="Reject Request">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $txn->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form method="POST" action="{{ route('admin.wallet.transactions.reject', $txn) }}" class="modal-content border-0 shadow">
                                                @csrf
                                                <div class="modal-header bg-danger text-white">
                                                    <h6 class="modal-title fw-bold">Reject Top-up Request #{{ $txn->id }}</h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-start p-3">
                                                    <label class="form-label fw-semibold small">Rejection Reason *</label>
                                                    <textarea name="reason" class="form-control" rows="2" required placeholder="e.g. UTR reference not reflected in bank account."></textarea>
                                                </div>
                                                <div class="modal-footer bg-light p-2">
                                                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-sm btn-danger">Confirm Reject</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @elseif($txn->centre)
                                    <a href="{{ route('admin.wallet.statement', $txn->centre) }}" class="btn btn-sm btn-light border text-primary" title="View Statement">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-receipt fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h6>No Wallet Transactions Found</h6>
                                    <p class="small text-muted mb-0">No ledger transactions matched the active filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="card-footer bg-white py-3 border-0">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

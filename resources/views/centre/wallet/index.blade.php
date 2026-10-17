@extends('layouts.centre')

@section('title', 'Franchise Wallet & Ledger - TECH ERA COMPUTER')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-wallet text-warning me-2"></i>Franchise Wallet & Ledger
        </h3>
        <p class="text-muted small mb-0">Study centre prepaid wallet for candidate admission fee, student kit deductions, and top-up recharges.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('centre.wallet.topup') }}" class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-plus-circle me-1"></i>Request Wallet Top-up
        </a>
    </div>
</div>

<!-- Wallet Balance & Metrics Strip -->
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card border-0 rounded-4 shadow-sm text-white h-100 p-4" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="small text-white-50 text-uppercase fw-semibold">Current Balance</span>
                <span class="badge {{ $wallet->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                    {{ ucfirst($wallet->status) }}
                </span>
            </div>
            <h2 class="fw-extrabold text-warning my-2">₹{{ number_format($wallet->balance, 2) }}</h2>
            <div class="small text-white-50 mb-3">Instant credit for student registration & kit allocations</div>
            <a href="{{ route('centre.wallet.topup') }}" class="btn btn-warning text-dark fw-bold rounded-pill w-100 mt-auto">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Recharge Balance
            </a>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="row g-3 h-100">
            <div class="col-sm-6">
                <div class="stat-card p-4 bg-white h-100 border-start border-4 border-success">
                    <div class="text-muted small fw-semibold text-uppercase">Total Lifetime Credited</div>
                    <h3 class="fw-bold text-success my-2">₹{{ number_format($wallet->total_credited, 2) }}</h3>
                    <span class="small text-muted">Approved top-up recharges and credits</span>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="stat-card p-4 bg-white h-100 border-start border-4 border-primary">
                    <div class="text-muted small fw-semibold text-uppercase">Total Lifetime Debited</div>
                    <h3 class="fw-bold text-dark my-2">₹{{ number_format($wallet->total_debited, 2) }}</h3>
                    <span class="small text-muted">Admissions & exam fee settlements</span>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="stat-card p-4 bg-white h-100 border-start border-4 border-warning">
                    <div class="text-muted small fw-semibold text-uppercase">Pending Top-up Requests</div>
                    <h3 class="fw-bold text-warning-emphasis my-2">{{ $stats['pending_requests'] }}</h3>
                    <span class="small text-muted">Amount: ₹{{ number_format($stats['pending_amount'], 2) }}</span>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="stat-card p-4 bg-white h-100">
                    <div class="text-muted small fw-semibold text-uppercase">Centre Affiliation Code</div>
                    <h3 class="fw-bold text-primary font-monospace my-2">{{ $centre->centre_code }}</h3>
                    <span class="small text-muted">{{ $centre->centre_name }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ledger Transactions Table -->
<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list-check text-primary me-2"></i>Wallet Transaction Ledger</h6>
        <span class="small text-muted">Showing {{ $transactions->total() }} records</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>Reference / UTR</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Balance After</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $txn)
                    <tr>
                        <td>
                            <div class="fw-semibold text-dark">{{ $txn->created_at->format('d M, Y') }}</div>
                            <div class="small text-muted">{{ $txn->created_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-dark font-monospace fw-bold">{{ $txn->reference_no }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $txn->type === 'credit' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} text-uppercase">
                                <i class="fa-solid {{ $txn->type === 'credit' ? 'fa-arrow-down' : 'fa-arrow-up' }} me-1"></i>{{ $txn->type }}
                            </span>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $txn->description }}</div>
                            @if($txn->approver)
                            <div class="small text-muted">Approved by: {{ $txn->approver->name }}</div>
                            @endif
                        </td>
                        <td class="fw-bold {{ $txn->type === 'credit' ? 'text-success' : 'text-danger' }} fs-6">
                            {{ $txn->type === 'credit' ? '+' : '-' }}₹{{ number_format($txn->amount, 2) }}
                        </td>
                        <td class="font-monospace fw-bold text-dark">
                            ₹{{ number_format($txn->balance_after, 2) }}
                        </td>
                        <td>
                            <span class="badge {{ $txn->status === 'approved' ? 'bg-success' : ($txn->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ ucfirst($txn->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-wallet fs-1 text-black-50 mb-3 d-block"></i>
                            <h5>No Wallet Transactions Recorded</h5>
                            <p class="small text-muted mb-0">Submit a top-up request to fund your centre wallet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="p-3 border-top">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

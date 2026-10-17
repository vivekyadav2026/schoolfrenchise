@extends('layouts.admin')

@section('title', 'Ledger Statement - ' . $centre->centre_code)

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 no-print">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.wallet.index') }}">Wallet</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $centre->centre_code }}</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-file-invoice text-primary me-2"></i>Account Statement: {{ $centre->centre_name }}
            </h2>
            <p class="text-muted small mb-0">Running transaction ledger and balance statement for {{ $centre->centre_code }}.</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-print me-1"></i> Print Statement
            </button>
            <a href="{{ route('admin.wallet.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Ledger
            </a>
        </div>
    </div>

    <!-- Statement Card -->
    <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
        <!-- Letterhead -->
        <div class="border-bottom pb-4 mb-4">
            <div class="row align-items-center">
                <div class="col-8">
                    <h3 class="fw-bold text-dark mb-1">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h3>
                    <div class="text-muted small">{{ $instituteSettings['site_tagline'] ?? 'Building Digital Skills for the Future' }}</div>
                    <div class="small text-muted">{{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }} | Phone: {{ $instituteSettings['contact_phone'] ?? '9162384660' }}</div>
                </div>
                <div class="col-4 text-end">
                    <div class="h5 fw-bold text-primary mb-0 font-monospace">{{ $centre->centre_code }}</div>
                    <div class="small text-muted">Generated: {{ now()->format('d M, Y h:i A') }}</div>
                </div>
            </div>
        </div>

        <!-- Centre & Balance Overview -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="p-3 bg-light rounded">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Franchise Particulars</span>
                    <div class="fw-bold text-dark fs-6">{{ $centre->centre_name }}</div>
                    <div class="small text-muted">Director: {{ $centre->director_name }}</div>
                    <div class="small text-muted">{{ $centre->city }}, {{ $centre->state?->name }} (PIN: {{ $centre->pincode }})</div>
                    <div class="small text-muted">Phone: {{ $centre->mobile }} | Email: {{ $centre->email }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <span class="text-muted small fw-bold text-uppercase d-block">Lifetime Credits</span>
                            <span class="fs-5 fw-bold text-success font-monospace">₹{{ number_format($stats['total_credited'], 2) }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <span class="text-muted small fw-bold text-uppercase d-block">Lifetime Debits</span>
                            <span class="fs-5 fw-bold text-danger font-monospace">₹{{ number_format($stats['total_debited'], 2) }}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-primary bg-opacity-10 border border-primary rounded text-center">
                            <span class="text-muted small fw-bold text-uppercase d-block">Current Available Balance</span>
                            <span class="fs-4 fw-bold text-primary font-monospace">₹{{ number_format($stats['current_balance'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statement Table -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th style="width: 130px;">Date & Time</th>
                        <th>Reference / UTR</th>
                        <th>Description / Details</th>
                        <th class="text-end" style="width: 120px;">Credit (+)</th>
                        <th class="text-end" style="width: 120px;">Debit (-)</th>
                        <th class="text-end" style="width: 130px;">Balance</th>
                        <th class="text-center" style="width: 90px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $txn)
                        <tr>
                            <td class="small">
                                <div>{{ $txn->created_at?->format('d/m/Y') }}</div>
                                <div class="text-muted" style="font-size: 10px;">{{ $txn->created_at?->format('h:i A') }}</div>
                            </td>
                            <td class="font-monospace small">{{ $txn->reference_no ?? '-' }}</td>
                            <td class="small">
                                <div>{{ $txn->description }}</div>
                                @if($txn->approver)
                                    <div class="text-muted" style="font-size: 10px;">Approved by: {{ $txn->approver->name }}</div>
                                @endif
                            </td>
                            <td class="text-end font-monospace text-success fw-semibold">
                                {{ $txn->type === 'credit' ? '₹' . number_format($txn->amount, 2) : '-' }}
                            </td>
                            <td class="text-end font-monospace text-danger fw-semibold">
                                {{ $txn->type === 'debit' ? '₹' . number_format($txn->amount, 2) : '-' }}
                            </td>
                            <td class="text-end font-monospace fw-bold text-dark">
                                ₹{{ number_format($txn->balance_after, 2) }}
                            </td>
                            <td class="text-center small">
                                <span class="badge {{ $txn->status === 'approved' ? 'bg-success' : ($txn->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                    {{ ucfirst($txn->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No transactions recorded for this centre yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="pt-3 no-print">
                {{ $transactions->links() }}
            </div>
        @endif

        <!-- Footer Signatures -->
        <div class="row pt-5 mt-5 border-top">
            <div class="col-6 text-center">
                <div class="border-top pt-2 mx-auto" style="width: 200px;">
                    <div class="small fw-bold">Centre In-Charge Signature</div>
                </div>
            </div>
            <div class="col-6 text-center">
                <div class="border-top pt-2 mx-auto" style="width: 200px;">
                    <div class="small fw-bold">Authorized Accounts Officer</div>
                    <div class="text-muted" style="font-size: 10px;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    body {
        background: #fff;
    }
    .card {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
}
</style>
@endsection

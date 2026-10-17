@extends('layouts.admin')

@section('title', 'Manual Wallet Ledger Adjustment')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.wallet.index') }}">Wallet History</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Adjustment</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-scale-balanced text-primary me-2"></i>Execute Ledger Adjustment
            </h2>
            <p class="text-muted small mb-0">Credit offline payments/top-ups or debit authorized charges against a franchise centre's ledger.</p>
        </div>
        <div>
            <a href="{{ route('admin.wallet.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Ledger
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Please correct the following errors:</h6>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.wallet.adjust.store') }}" class="card border-0 shadow-sm rounded-3 bg-white">
                @csrf
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Select Centre / Franchise <span class="text-danger">*</span></label>
                        <select name="centre_id" class="form-select @error('centre_id') is-invalid @enderror" required>
                            <option value="">-- Choose Franchise Centre --</option>
                            @foreach($centres as $cnt)
                                <option value="{{ $cnt->id }}" {{ old('centre_id') == $cnt->id ? 'selected' : '' }}>
                                    {{ $cnt->centre_name }} ({{ $cnt->centre_code }}) — Available Balance: ₹{{ number_format($cnt->wallet?->balance ?? 0.00, 2) }}
                                </option>
                            @endforeach
                        </select>
                        @error('centre_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Adjustment Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="credit" {{ old('type') === 'credit' ? 'selected' : '' }}>Credit (+) Top-up / Fund Addition</option>
                                <option value="debit" {{ old('type') === 'debit' ? 'selected' : '' }}>Debit (-) Fee / Material Deduction</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Amount (INR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control font-monospace @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required placeholder="0.00" min="1">
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Bank Reference / UTR / Voucher No <span class="text-danger">*</span></label>
                        <input type="text" name="reference_no" class="form-control font-monospace @error('reference_no') is-invalid @enderror" value="{{ old('reference_no') }}" required placeholder="e.g. UTR-20261017001 or VCHR-ADJ-09">
                        @error('reference_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Adjustment Description / Justification <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required placeholder="State exact reason, e.g. Offline bank transfer confirmed on 17/10/2026 via HDFC bank.">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="alert alert-warning border-0 small mb-4">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        <strong>Notice:</strong> This action directly alters the financial balance of the selected franchise. The operation is strictly logged in audit trails.
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.wallet.index') }}" class="btn btn-light border px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fa-solid fa-check me-1"></i> Execute Adjustment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

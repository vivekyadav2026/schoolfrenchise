@extends('layouts.admin')

@section('title', 'Log New Order')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.downloads.index') }}">Downloads</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.downloads.orders') }}">Orders</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Log Order</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-cart-plus text-primary me-2"></i>Log Resource / Kit Order
            </h2>
            <p class="text-muted small mb-0">Record an order for franchise welcome kit, certificate blank sheets, or study books.</p>
        </div>
        <div>
            <a href="{{ route('admin.downloads.orders') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Orders
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
            <form method="POST" action="{{ route('admin.downloads.orders.store') }}" class="card border-0 shadow-sm rounded-3 bg-white">
                @csrf
                <div class="card-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Ordering Centre / Branch</label>
                            <select name="centre_id" class="form-select @error('centre_id') is-invalid @enderror">
                                <option value="">-- Central / Direct Order --</option>
                                @foreach($centres as $cnt)
                                    <option value="{{ $cnt->id }}" {{ old('centre_id') == $cnt->id ? 'selected' : '' }}>
                                        {{ $cnt->centre_name }} ({{ $cnt->centre_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('centre_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Item Type <span class="text-danger">*</span></label>
                            <select name="item_type" class="form-select @error('item_type') is-invalid @enderror" required>
                                <option value="kit" {{ old('item_type') === 'kit' ? 'selected' : '' }}>Student Welcome Kit (Bag, I-Card, Book)</option>
                                <option value="certificates" {{ old('item_type') === 'certificates' ? 'selected' : '' }}>Certificate Blank Paper Sheets</option>
                                <option value="stationery" {{ old('item_type') === 'stationery' ? 'selected' : '' }}>Institute Stationery & Banners</option>
                                <option value="download" {{ old('item_type') === 'download' ? 'selected' : '' }}>Paid Digital Resource</option>
                                <option value="other" {{ old('item_type') === 'other' ? 'selected' : '' }}>Other Supplies</option>
                            </select>
                            @error('item_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Item Name / Description <span class="text-danger">*</span></label>
                        <input type="text" name="item_name" class="form-control @error('item_name') is-invalid @enderror" value="{{ old('item_name') }}" required placeholder="e.g. 50 Sets - Student Welcome Kit with DCE Bags">
                        @error('item_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Order Total Amount (INR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control font-monospace @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required placeholder="0.00" min="0">
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Payment Status <span class="text-danger">*</span></label>
                            <select name="payment_status" class="form-select @error('payment_status') is-invalid @enderror" required>
                                <option value="completed" {{ old('payment_status', 'completed') === 'completed' ? 'selected' : '' }}>Completed (Paid)</option>
                                <option value="pending" {{ old('payment_status') === 'pending' ? 'selected' : '' }}>Pending Payment</option>
                                <option value="failed" {{ old('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ old('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                            @error('payment_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                <option value="online" {{ old('payment_method') === 'online' ? 'selected' : '' }}>Online Payment Gateway</option>
                                <option value="upi" {{ old('payment_method') === 'upi' ? 'selected' : '' }}>UPI / QR Code</option>
                                <option value="wallet" {{ old('payment_method') === 'wallet' ? 'selected' : '' }}>Centre Wallet Balance</option>
                                <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>NEFT / IMPS / Bank</option>
                                <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                            </select>
                            @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Transaction Reference / UTR</label>
                            <input type="text" name="transaction_ref" class="form-control font-monospace @error('transaction_ref') is-invalid @enderror" value="{{ old('transaction_ref') }}" placeholder="e.g. UTR-20261017001">
                            @error('transaction_ref')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Dispatch / Order Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" placeholder="e.g. Courier tracking ID, delivery instructions, or kit contents...">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.downloads.orders') }}" class="btn btn-light border px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fa-solid fa-check me-1"></i> Save Order
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

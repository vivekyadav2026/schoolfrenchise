@extends('layouts.admin')

@section('title', 'Edit Expense Entry')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.expenses.index') }}">Expenses</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Expense Entry
            </h2>
            <p class="text-muted small mb-0">Modify operating expense particulars, amount, or voucher.</p>
        </div>
        <div>
            <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Expenses
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
            <form method="POST" action="{{ route('admin.expenses.update', $expense) }}" enctype="multipart/form-data" class="card border-0 shadow-sm rounded-3 bg-white">
                @csrf
                @method('PUT')

                <div class="card-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Centre / Branch Location</label>
                            <select name="centre_id" class="form-select @error('centre_id') is-invalid @enderror">
                                <option value="">-- Central Head Office --</option>
                                @foreach($centres as $cnt)
                                    <option value="{{ $cnt->id }}" {{ old('centre_id', $expense->centre_id) == $cnt->id ? 'selected' : '' }}>
                                        {{ $cnt->centre_name }} ({{ $cnt->centre_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('centre_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Expense Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="rent" {{ old('category', $expense->category) === 'rent' ? 'selected' : '' }}>Facility Rent</option>
                                <option value="electricity" {{ old('category', $expense->category) === 'electricity' ? 'selected' : '' }}>Electricity & Utilities</option>
                                <option value="salaries" {{ old('category', $expense->category) === 'salaries' ? 'selected' : '' }}>Staff / Faculty Salaries</option>
                                <option value="hardware" {{ old('category', $expense->category) === 'hardware' ? 'selected' : '' }}>Lab Hardware / Computer Parts</option>
                                <option value="internet" {{ old('category', $expense->category) === 'internet' ? 'selected' : '' }}>Internet & Networking</option>
                                <option value="stationery" {{ old('category', $expense->category) === 'stationery' ? 'selected' : '' }}>Stationery & Printing</option>
                                <option value="maintenance" {{ old('category', $expense->category) === 'maintenance' ? 'selected' : '' }}>Repairs & Maintenance</option>
                                <option value="marketing" {{ old('category', $expense->category) === 'marketing' ? 'selected' : '' }}>Offline Marketing & Pamphlets</option>
                                <option value="other" {{ old('category', $expense->category) === 'other' ? 'selected' : '' }}>Miscellaneous</option>
                            </select>
                            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Expense Title / Payee <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $expense->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Amount (INR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control font-monospace @error('amount') is-invalid @enderror" value="{{ old('amount', $expense->amount) }}" required min="0.01">
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control @error('expense_date') is-invalid @enderror" value="{{ old('expense_date', $expense->expense_date?->toDateString()) }}" required>
                            @error('expense_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Payment Mode <span class="text-danger">*</span></label>
                            <select name="payment_mode" class="form-select @error('payment_mode') is-invalid @enderror" required>
                                <option value="bank_transfer" {{ old('payment_mode', $expense->payment_mode) === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer (NEFT / IMPS)</option>
                                <option value="upi" {{ old('payment_mode', $expense->payment_mode) === 'upi' ? 'selected' : '' }}>UPI / QR</option>
                                <option value="cash" {{ old('payment_mode', $expense->payment_mode) === 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="cheque" {{ old('payment_mode', $expense->payment_mode) === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="card" {{ old('payment_mode', $expense->payment_mode) === 'card' ? 'selected' : '' }}>Debit / Credit Card</option>
                            </select>
                            @error('payment_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Voucher / Cheque / UTR Reference</label>
                            <input type="text" name="reference_no" class="form-control font-monospace @error('reference_no') is-invalid @enderror" value="{{ old('reference_no', $expense->reference_no) }}">
                            @error('reference_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Replace Bill / Voucher (Optional)</label>
                        <input type="file" name="receipt_doc" class="form-control @error('receipt_doc') is-invalid @enderror">
                        @if($expense->receipt_doc)
                            <div class="small text-muted mt-1">Current: <a href="{{ asset('storage/' . $expense->receipt_doc) }}" target="_blank">View Uploaded Bill</a></div>
                        @endif
                        @error('receipt_doc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Notes / Purpose Description</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes', $expense->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.expenses.index') }}" class="btn btn-light border px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fa-solid fa-check me-1"></i> Update Expense
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

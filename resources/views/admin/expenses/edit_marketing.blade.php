@extends('layouts.admin')

@section('title', 'Edit Marketing Campaign')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.expenses.index') }}">Expenses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.expenses.marketing') }}">Marketing</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Campaign</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Marketing Campaign
            </h2>
            <p class="text-muted small mb-0">Modify campaign details, actual ad spends, and lead performance metrics.</p>
        </div>
        <div>
            <a href="{{ route('admin.expenses.marketing') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Marketing List
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-2"></i>Please correct the following errors:</h6>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i>Campaign #{{ $marketing->id }} Details</h6>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.expenses.marketing.update', $marketing) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="campaign_name" class="form-label small fw-semibold">Campaign Title / Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('campaign_name') is-invalid @enderror" id="campaign_name" name="campaign_name" value="{{ old('campaign_name', $marketing->campaign_name) }}" required>
                        @error('campaign_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="platform" class="form-label small fw-semibold">Advertising Platform <span class="text-danger">*</span></label>
                        <select class="form-select @error('platform') is-invalid @enderror" id="platform" name="platform" required>
                            <option value="">-- Select Platform --</option>
                            @php
                                $platforms = [
                                    'Facebook & Instagram Ads',
                                    'Google Ads (Search & Display)',
                                    'YouTube Video Ads',
                                    'Meta Lead Generation',
                                    'WhatsApp Business Broadcast',
                                    'SMS Campaign',
                                    'Local Newspaper Insertion',
                                    'Outdoor Hoardings / Banners',
                                    'Pamphlets / Leaflet Distribution',
                                    'School / College Seminar',
                                    'Education Fair / Expo',
                                    'Other Offline Promotion'
                                ];
                            @endphp
                            @foreach($platforms as $p)
                                <option value="{{ $p }}" {{ old('platform', $marketing->platform) == $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                            @if(!in_array($marketing->platform, $platforms))
                                <option value="{{ $marketing->platform }}" selected>{{ $marketing->platform }}</option>
                            @endif
                        </select>
                        @error('platform')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="centre_id" class="form-label small fw-semibold">Associated Centre / Branch</label>
                        <select class="form-select @error('centre_id') is-invalid @enderror" id="centre_id" name="centre_id">
                            <option value="">Head Office / General / Central</option>
                            @foreach($centres as $c)
                                <option value="{{ $c->id }}" {{ old('centre_id', $marketing->centre_id) == $c->id ? 'selected' : '' }}>
                                    [{{ $c->centre_code }}] {{ $c->centre_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small">Leave blank if this is a nationwide / central head-office campaign.</div>
                        @error('centre_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="budget" class="form-label small fw-semibold">Planned Budget (₹) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">₹</span>
                            <input type="number" step="0.01" class="form-control @error('budget') is-invalid @enderror" id="budget" name="budget" value="{{ old('budget', $marketing->budget) }}" required>
                        </div>
                        @error('budget')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="amount_spent" class="form-label small fw-semibold">Actual Amount Spent (₹) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">₹</span>
                            <input type="number" step="0.01" class="form-control @error('amount_spent') is-invalid @enderror" id="amount_spent" name="amount_spent" value="{{ old('amount_spent', $marketing->amount_spent) }}" required>
                        </div>
                        @error('amount_spent')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="start_date" class="form-label small fw-semibold">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $marketing->start_date ? \Carbon\Carbon::parse($marketing->start_date)->toDateString() : '') }}" required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="end_date" class="form-label small fw-semibold">End Date</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $marketing->end_date ? \Carbon\Carbon::parse($marketing->end_date)->toDateString() : '') }}">
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="leads_generated" class="form-label small fw-semibold">Inquiries / Leads Generated</label>
                        <input type="number" class="form-control @error('leads_generated') is-invalid @enderror" id="leads_generated" name="leads_generated" value="{{ old('leads_generated', $marketing->leads_generated) }}" min="0">
                        @error('leads_generated')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="conversions_count" class="form-label small fw-semibold">Admissions / Conversions</label>
                        <input type="number" class="form-control @error('conversions_count') is-invalid @enderror" id="conversions_count" name="conversions_count" value="{{ old('conversions_count', $marketing->conversions_count) }}" min="0">
                        @error('conversions_count')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label small fw-semibold">Campaign Description / Strategy Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $marketing->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('admin.expenses.marketing') }}" class="btn btn-light px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-check me-1"></i> Update Campaign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

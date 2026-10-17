@extends('layouts.admin')

@section('title', 'Edit Admission - TECH ERA COMPUTER ERP')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.admissions.index') }}" class="text-decoration-none">Admissions</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Admission</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Modify Course Admission</h3>
        <p class="text-muted small mb-0">Update batch allocation, training status, or internal remarks for <strong class="text-primary">{{ $admission->student?->full_name }}</strong></p>
    </div>
    <div>
        <a href="{{ route('admin.admissions.index', ['tab' => 'active']) }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Admissions
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.admissions.update', $admission->id) }}">
    @csrf
    @method('PUT')

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card dce-table-card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-graduation-cap me-2"></i>Admission Dossier</h6>
                    <span class="badge bg-secondary font-monospace">{{ $admission->student?->roll_no }}</span>
                </div>
                <div class="card-body">
                    <!-- Read-only Summary -->
                    <div class="alert alert-light border mb-4">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <span class="text-muted small">Student Name:</span>
                                <div class="fw-bold text-dark">{{ $admission->student?->full_name }}</div>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small">Enrolled Course:</span>
                                <div class="fw-bold text-primary">{{ $admission->course?->title }} ({{ $admission->course?->course_code }})</div>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small">Study Centre:</span>
                                <div>[{{ $admission->centre?->centre_code }}] {{ $admission->centre?->centre_name }}</div>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small">Financials:</span>
                                <div>Net: ₹{{ number_format($admission->net_fee, 2) }} | Paid: ₹{{ number_format($admission->total_paid, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Allocated Training Batch</label>
                            <select name="batch_id" class="form-select @error('batch_id') is-invalid @enderror">
                                <option value="">-- No Batch Assigned --</option>
                                @foreach($batches as $b)
                                    <option value="{{ $b->id }}" {{ old('batch_id', $admission->batch_id) == $b->id ? 'selected' : '' }}>
                                        {{ $b->batch_name }} ({{ $b->start_time }} - {{ $b->end_time }})
                                    </option>
                                @endforeach
                            </select>
                            @error('batch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Admission Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $admission->status) == 'active' ? 'selected' : '' }}>Active Admission</option>
                                <option value="completed" {{ old('status', $admission->status) == 'completed' ? 'selected' : '' }}>Completed Course</option>
                                <option value="cancelled" {{ old('status', $admission->status) == 'cancelled' ? 'selected' : '' }}>Cancelled / Discontinued</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Administrative Remarks / Notes</label>
                            <textarea name="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror" 
                                      placeholder="Internal admission notes...">{{ old('remarks', $admission->remarks) }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white text-end py-3">
                    <a href="{{ route('admin.admissions.index', ['tab' => 'active']) }}" class="btn btn-outline-secondary px-4 me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5 shadow-sm">
                        <i class="fa-solid fa-save me-2"></i>Update Admission
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

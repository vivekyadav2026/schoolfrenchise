@extends('layouts.admin')

@section('title', 'Edit Fee Structure - ' . $structure->name . ' - TECH ERA COMPUTER')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.fees.structures') }}" class="text-decoration-none">Fee Structures</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Plan</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Edit Fee Structure</h3>
        <p class="text-muted small mb-0">Modify fee packages, pricing components and installment rules.</p>
    </div>
    <div>
        <a href="{{ route('admin.fees.structures') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Structures
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card dce-table-card border-0 shadow-sm p-4">
            <form method="POST" action="{{ route('admin.fees.structures.update', $structure->id) }}">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Target Course <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}" {{ old('course_id', $structure->course_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->course_name }} ({{ $c->course_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('course_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Centre / Franchise Scope</label>
                        <select name="centre_id" class="form-select @error('centre_id') is-invalid @enderror">
                            <option value="">All Centres (Global Franchise Plan)</option>
                            @foreach($centres as $cnt)
                                <option value="{{ $cnt->id }}" {{ old('centre_id', $structure->centre_id) == $cnt->id ? 'selected' : '' }}>
                                    {{ $cnt->centre_name }} [{{ $cnt->centre_code }}]
                                </option>
                            @endforeach
                        </select>
                        @error('centre_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Structure / Plan Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $structure->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Number of Instalments <span class="text-danger">*</span></label>
                        <input type="number" name="installments_count" class="form-control @error('installments_count') is-invalid @enderror" 
                               min="1" max="24" value="{{ old('installments_count', $structure->installments_count) }}" required>
                        @error('installments_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Financial Component Breakdown -->
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="fa-solid fa-calculator text-primary me-2"></i>Fee Breakdown & Components
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Total Applicable Fee (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="total_fee" class="form-control fw-bold text-primary @error('total_fee') is-invalid @enderror" 
                               value="{{ old('total_fee', $structure->total_fee) }}" required>
                        @error('total_fee') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Admission / Registration Part (₹)</label>
                        <input type="number" step="0.01" name="admission_fee" class="form-control @error('admission_fee') is-invalid @enderror" 
                               value="{{ old('admission_fee', $structure->admission_fee) }}">
                        @error('admission_fee') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tuition Fee Component (₹)</label>
                        <input type="number" step="0.01" name="tuition_fee" class="form-control @error('tuition_fee') is-invalid @enderror" 
                               value="{{ old('tuition_fee', $structure->tuition_fee) }}">
                        @error('tuition_fee') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Examination Fee Part (₹)</label>
                        <input type="number" step="0.01" name="exam_fee" class="form-control @error('exam_fee') is-invalid @enderror" 
                               value="{{ old('exam_fee', $structure->exam_fee) }}">
                        @error('exam_fee') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Certification & Verification Fee (₹)</label>
                        <input type="number" step="0.01" name="certificate_fee" class="form-control @error('certificate_fee') is-invalid @enderror" 
                               value="{{ old('certificate_fee', $structure->certificate_fee) }}">
                        @error('certificate_fee') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Terms & Payment Instructions</label>
                        <textarea name="terms" rows="2" class="form-control">{{ old('terms', $structure->terms) }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeSwitch" {{ old('is_active', $structure->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="activeSwitch">Active Plan</label>
                        </div>
                    </div>
                </div>

                <div class="text-end border-top pt-3">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Update Fee Structure
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

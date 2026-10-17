@extends('layouts.admin')

@section('title', 'Edit Batch - ' . $batch->batch_name . ' - TECH ERA COMPUTER')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.batches.index') }}" class="text-decoration-none">Batches</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Batch</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Modify Training Batch</h3>
        <p class="text-muted small mb-0">Update schedule timing, allocated centre, course, or operational status.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.batches.show', $batch->id) }}" class="btn btn-outline-info btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-eye me-1"></i> View Batch
        </a>
        <a href="{{ route('admin.batches.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Batches
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.batches.update', $batch->id) }}">
    @csrf
    @method('PUT')

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card dce-table-card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-calendar-check me-2"></i>Batch Details</h6>
                    <span class="badge bg-secondary font-monospace">{{ $batch->centre?->centre_code }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Study Centre <span class="text-danger">*</span></label>
                            <select name="centre_id" class="form-select @error('centre_id') is-invalid @enderror" required>
                                @foreach($centres as $c)
                                    <option value="{{ $c->id }}" {{ old('centre_id', $batch->centre_id) == $c->id ? 'selected' : '' }}>
                                        [{{ $c->centre_code }}] {{ $c->centre_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('centre_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Course Curriculum <span class="text-danger">*</span></label>
                            <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                                @foreach($courses as $crs)
                                    <option value="{{ $crs->id }}" {{ old('course_id', $batch->course_id) == $crs->id ? 'selected' : '' }}>
                                        [{{ $crs->course_code }}] {{ $crs->course_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Batch Name / Shift Title <span class="text-danger">*</span></label>
                            <input type="text" name="batch_name" class="form-control @error('batch_name') is-invalid @enderror" 
                                   value="{{ old('batch_name', $batch->batch_name) }}" required>
                            @error('batch_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror" 
                                   value="{{ old('start_time', substr($batch->start_time, 0, 5)) }}" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">End Time <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror" 
                                   value="{{ old('end_time', substr($batch->end_time, 0, 5)) }}" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Days Schedule <span class="text-danger">*</span></label>
                            <input type="text" name="days" class="form-control @error('days') is-invalid @enderror" 
                                   value="{{ old('days', $batch->days) }}" required>
                            @error('days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Batch Operational Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $batch->status) == 'active' ? 'selected' : '' }}>Active & Ongoing</option>
                                <option value="completed" {{ old('status', $batch->status) == 'completed' ? 'selected' : '' }}>Completed / Graduated</option>
                                <option value="inactive" {{ old('status', $batch->status) == 'inactive' ? 'selected' : '' }}>Inactive / Suspended</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white text-end py-3">
                    <a href="{{ route('admin.batches.show', $batch->id) }}" class="btn btn-outline-secondary px-4 me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5 shadow-sm">
                        <i class="fa-solid fa-save me-2"></i>Update Batch
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

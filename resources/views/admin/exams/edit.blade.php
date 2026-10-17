@extends('layouts.admin')

@section('title', 'Edit Examination')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a;">
                <i class="fa-solid fa-pen-to-square me-2" style="color: #0891b2;"></i>Edit Examination Session
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.exams.index') }}">Examinations</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.exams.show', $exam) }}">{{ $exam->exam_code }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Exam Details
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-file-pen me-2 text-primary"></i>Modify Exam Details
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.exams.update', $exam) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="exam_title" class="form-label fw-semibold">Examination Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('exam_title') is-invalid @enderror" id="exam_title" name="exam_title" value="{{ old('exam_title', $exam->exam_title) }}" required>
                                @error('exam_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="course_id" class="form-label fw-semibold">Course Curriculum <span class="text-danger">*</span></label>
                                <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
                                    <option value="">Select Course</option>
                                    @foreach($courses as $c)
                                        <option value="{{ $c->id }}" {{ old('course_id', $exam->course_id) == $c->id ? 'selected' : '' }}>
                                            {{ $c->course_name }} ({{ $c->course_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="session_name" class="form-label fw-semibold">Academic Session / Cycle <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('session_name') is-invalid @enderror" id="session_name" name="session_name" value="{{ old('session_name', $exam->session_name) }}" required>
                                @error('session_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="exam_code" class="form-label fw-semibold">Exam Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('exam_code') is-invalid @enderror font-monospace" id="exam_code" name="exam_code" value="{{ old('exam_code', $exam->exam_code) }}" required>
                                @error('exam_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="exam_date" class="form-label fw-semibold">Examination Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('exam_date') is-invalid @enderror" id="exam_date" name="exam_date" value="{{ old('exam_date', $exam->exam_date ? $exam->exam_date->format('Y-m-d') : '') }}" required>
                                @error('exam_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold">Cycle Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="scheduled" {{ old('status', $exam->status) === 'scheduled' ? 'selected' : '' }}>Scheduled (Upcoming)</option>
                                    <option value="ongoing" {{ old('status', $exam->status) === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="evaluated" {{ old('status', $exam->status) === 'evaluated' ? 'selected' : '' }}>Evaluated</option>
                                    <option value="published" {{ old('status', $exam->status) === 'published' ? 'selected' : '' }}>Published</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4" style="background-color: #0891b2; border-color: #0891b2;">
                                <i class="fa-solid fa-save me-1"></i> Update Exam
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

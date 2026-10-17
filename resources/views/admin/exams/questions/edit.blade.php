@extends('layouts.admin')

@section('title', 'Edit Question')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a;">
                <i class="fa-solid fa-pen-to-square me-2" style="color: #0891b2;"></i>Edit Question #{{ $question->id }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.exams.index') }}">Examinations</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.exams.questions') }}">Question Bank</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit #{{ $question->id }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.exams.questions') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Question Bank
        </a>
    </div>

    <div class="row">
        <div class="col-lg-9 mx-auto">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-file-circle-question me-2 text-primary"></i>Modify Question Details
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.exams.questions.update', $question) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="course_id" class="form-label fw-semibold">Target Course <span class="text-danger">*</span></label>
                                <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
                                    <option value="">Select Course</option>
                                    @foreach($courses as $c)
                                        <option value="{{ $c->id }}" {{ old('course_id', $question->course_id) == $c->id ? 'selected' : '' }}>
                                            {{ $c->course_name }} ({{ $c->course_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="course_subject_id" class="form-label fw-semibold">Subject Module (Optional)</label>
                                <select class="form-select @error('course_subject_id') is-invalid @enderror" id="course_subject_id" name="course_subject_id">
                                    <option value="">General / All Modules</option>
                                    @foreach($courses as $c)
                                        @foreach($c->subjects as $s)
                                            <option value="{{ $s->id }}" {{ old('course_subject_id', $question->course_subject_id) == $s->id ? 'selected' : '' }}>
                                                {{ $c->course_code }} - {{ $s->subject_name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                                @error('course_subject_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="question_text" class="form-label fw-semibold">Question Statement <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('question_text') is-invalid @enderror" id="question_text" name="question_text" rows="3" required>{{ old('question_text', $question->question_text) }}</textarea>
                                @error('question_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="option_a" class="form-label fw-semibold">Option A <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">A</span>
                                    <input type="text" class="form-control @error('option_a') is-invalid @enderror" id="option_a" name="option_a" value="{{ old('option_a', $question->option_a) }}" required>
                                </div>
                                @error('option_a')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="option_b" class="form-label fw-semibold">Option B <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">B</span>
                                    <input type="text" class="form-control @error('option_b') is-invalid @enderror" id="option_b" name="option_b" value="{{ old('option_b', $question->option_b) }}" required>
                                </div>
                                @error('option_b')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="option_c" class="form-label fw-semibold">Option C <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">C</span>
                                    <input type="text" class="form-control @error('option_c') is-invalid @enderror" id="option_c" name="option_c" value="{{ old('option_c', $question->option_c) }}" required>
                                </div>
                                @error('option_c')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="option_d" class="form-label fw-semibold">Option D <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">D</span>
                                    <input type="text" class="form-control @error('option_d') is-invalid @enderror" id="option_d" name="option_d" value="{{ old('option_d', $question->option_d) }}" required>
                                </div>
                                @error('option_d')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="correct_option" class="form-label fw-semibold">Correct Answer <span class="text-danger">*</span></label>
                                <select class="form-select @error('correct_option') is-invalid @enderror" id="correct_option" name="correct_option" required>
                                    <option value="a" {{ old('correct_option', $question->correct_option) === 'a' ? 'selected' : '' }}>Option A</option>
                                    <option value="b" {{ old('correct_option', $question->correct_option) === 'b' ? 'selected' : '' }}>Option B</option>
                                    <option value="c" {{ old('correct_option', $question->correct_option) === 'c' ? 'selected' : '' }}>Option C</option>
                                    <option value="d" {{ old('correct_option', $question->correct_option) === 'd' ? 'selected' : '' }}>Option D</option>
                                </select>
                                @error('correct_option')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="marks" class="form-label fw-semibold">Marks Weightage <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('marks') is-invalid @enderror" id="marks" name="marks" value="{{ old('marks', $question->marks) }}" min="1" max="100" required>
                                @error('marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="difficulty" class="form-label fw-semibold">Difficulty Level <span class="text-danger">*</span></label>
                                <select class="form-select @error('difficulty') is-invalid @enderror" id="difficulty" name="difficulty" required>
                                    <option value="easy" {{ old('difficulty', $question->difficulty) === 'easy' ? 'selected' : '' }}>Easy</option>
                                    <option value="medium" {{ old('difficulty', $question->difficulty) === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="hard" {{ old('difficulty', $question->difficulty) === 'hard' ? 'selected' : '' }}>Hard</option>
                                </select>
                                @error('difficulty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" {{ old('status', $question->status) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="status">Active for Online & Offline Examination Papers</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.exams.questions') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4" style="background-color: #0891b2; border-color: #0891b2;">
                                <i class="fa-solid fa-save me-1"></i> Update Question
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

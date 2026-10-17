@extends('layouts.centre')

@section('title', 'Exam Marks Evaluation - ' . $exam->exam_title)

@section('content')
<div class="mb-4">
    <a href="{{ route('centre.exams.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill mb-2">
        <i class="fa-solid fa-arrow-left me-1"></i>Back to Exams
    </a>
    <h3 class="fw-bold text-dark mb-1">
        <i class="fa-solid fa-pen-to-square text-success me-2"></i>Internal Marks Evaluation Sheet
    </h3>
    <p class="text-muted small mb-0">
        <strong>{{ $exam->exam_title }}</strong> &bull; Course: {{ $exam->course?->course_name }} ({{ $exam->course?->course_code }})
    </p>
</div>

<!-- Select Student Card -->
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-body p-4">
        <label class="form-label fw-bold">Select Centre Candidate for Marks Entry:</label>
        <form method="GET" action="{{ route('centre.exams.marks', $exam->id) }}">
            <div class="row g-2 align-items-center">
                <div class="col-md-9">
                    <select name="student_id" class="form-select" onchange="this.form.submit()" required>
                        <option value="">-- Choose Candidate --</option>
                        @foreach($students as $s)
                        <option value="{{ $s->id }}" {{ ($selectedStudent && $selectedStudent->id == $s->id) ? 'selected' : '' }}>
                            {{ $s->roll_no }} - {{ $s->full_name }} (Father: {{ $s->father_name }})
                            @if($s->results->isNotEmpty()) [Evaluated: {{ $s->results->first()->grade }} ({{ $s->results->first()->percentage }}%)] @endif
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">Load Subjects</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($selectedStudent)
<form method="POST" action="{{ route('centre.exams.marks.store', $exam->id) }}">
    @csrf
    <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">

    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold mb-0 text-dark">{{ $selectedStudent->full_name }}</h6>
                <div class="small text-muted font-monospace">ROLL: {{ $selectedStudent->roll_no }} | REG: {{ $selectedStudent->registration_no }}</div>
            </div>
            <span class="badge bg-primary-subtle text-primary font-monospace">{{ $exam->course?->course_code }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Subject</th>
                            <th class="text-center" style="width: 140px;">Theory Marks</th>
                            <th class="text-center" style="width: 140px;">Practical Marks</th>
                            <th class="text-center" style="width: 140px;">Viva / Project</th>
                            <th class="text-center" style="width: 110px;">Pass Marks</th>
                            <th class="text-center" style="width: 110px;">Total Max</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exam->course?->subjects ?? [] as $sub)
                        <tr>
                            <td>
                                <input type="hidden" name="subjects[{{ $sub->id }}][id]" value="{{ $sub->id }}">
                                <div class="fw-bold text-dark">{{ $sub->subject_name }}</div>
                                <div class="small text-muted font-monospace">{{ $sub->subject_code }}</div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.5" min="0" max="{{ $sub->theory_max }}" name="subjects[{{ $sub->id }}][theory_obtained]" class="form-control text-center fw-bold" placeholder="0" value="{{ old('subjects.' . $sub->id . '.theory_obtained', 0) }}">
                                    <span class="input-group-text bg-light text-muted">/ {{ $sub->theory_max }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.5" min="0" max="{{ $sub->practical_max }}" name="subjects[{{ $sub->id }}][practical_obtained]" class="form-control text-center fw-bold" placeholder="0" value="{{ old('subjects.' . $sub->id . '.practical_obtained', 0) }}">
                                    <span class="input-group-text bg-light text-muted">/ {{ $sub->practical_max }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.5" min="0" max="{{ $sub->viva_max }}" name="subjects[{{ $sub->id }}][viva_obtained]" class="form-control text-center fw-bold" placeholder="0" value="{{ old('subjects.' . $sub->id . '.viva_obtained', 0) }}">
                                    <span class="input-group-text bg-light text-muted">/ {{ $sub->viva_max }}</span>
                                </div>
                            </td>
                            <td class="text-center text-muted font-monospace fw-semibold">{{ $sub->pass_marks }}</td>
                            <td class="text-center fw-bold text-primary font-monospace">{{ $sub->total_max }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No course subjects defined.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white p-3 border-top d-flex justify-content-end">
                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-cloud-arrow-up me-1"></i>Save Marks & Generate Evaluation
                </button>
            </div>
        </div>
    </div>
</form>
@endif
@endsection

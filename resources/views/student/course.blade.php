@extends('layouts.student')

@section('title', 'My Course - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-book-open-reader text-primary me-2"></i> My Enrolled Course
            </h4>
            <div class="text-muted small">Course syllabus, curriculum subjects and learning modules</div>
        </div>
        <div>
            <a href="{{ route('student.study-materials') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fa-solid fa-folder-open me-1"></i> Access Notes
            </a>
        </div>
    </div>

    @if($course)
        <!-- Course Hero Card -->
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
            <div class="row align-items-center g-4">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary-subtle text-primary font-monospace px-3 py-1 fw-bold fs-6">
                            {{ $course->course_code }}
                        </span>
                        <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill small">
                            <i class="fa-solid fa-circle-check me-1"></i> Enrolled
                        </span>
                    </div>
                    <h3 class="fw-bold text-dark mb-2">{{ $course->course_name }}</h3>
                    <p class="text-muted mb-3">{{ $course->description ?? 'Comprehensive professional vocational and software development program designed in alignment with national IT industry standards.' }}</p>
                    
                    <div class="d-flex flex-wrap gap-4 text-secondary small">
                        <div>
                            <i class="fa-regular fa-clock text-primary me-1"></i>
                            Duration: <strong class="text-dark">{{ $course->duration ?? 'Standard' }}</strong>
                        </div>
                        <div>
                            <i class="fa-solid fa-award text-primary me-1"></i>
                            Eligibility: <strong class="text-dark">{{ $course->eligibility ?? '10th / 12th Pass or Equivalent' }}</strong>
                        </div>
                        <div>
                            <i class="fa-solid fa-calendar-days text-primary me-1"></i>
                            Batch: <strong class="text-dark">{{ $admission->batch?->batch_name ?? 'Regular Shift' }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="p-3 bg-light rounded-4 border text-center">
                        <div class="text-muted small mb-1">Standard Course Fee</div>
                        <h3 class="fw-bold text-primary mb-1">₹{{ number_format($course->course_fee, 2) }}</h3>
                        <div class="text-muted small">Inclusive of curriculum & study notes</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Curriculum & Subjects Breakdown -->
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-list-check text-primary me-2"></i> Curriculum Subjects & Marking Scheme
                    </h5>
                    <div class="text-muted small mt-1">Subjects covered under this certification curriculum</div>
                </div>
                <span class="badge bg-light text-muted">{{ $course->subjects->count() }} Subjects</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background-color: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #64748b;">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Subject Code</th>
                                <th>Subject Title</th>
                                <th class="text-center">Theory Marks</th>
                                <th class="text-center">Practical Marks</th>
                                <th class="pe-4 text-center">Total Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($course->subjects as $index => $subject)
                                <tr>
                                    <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="font-monospace fw-bold small text-primary">{{ $subject->subject_code ?? 'SUB-'.($index+1) }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $subject->subject_name }}</div>
                                    </td>
                                    <td class="text-center text-secondary small">{{ $subject->theory_marks ?? 50 }}</td>
                                    <td class="text-center text-secondary small">{{ $subject->practical_marks ?? 50 }}</td>
                                    <td class="pe-4 text-center fw-bold text-dark">{{ $subject->total_marks ?? 100 }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-book fa-2x mb-2 text-secondary opacity-50"></i>
                                        <p class="mb-0">Curriculum subjects will be updated shortly by the academic controller.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center">
            <i class="fa-solid fa-graduation-cap fa-3x text-muted mb-3 opacity-50"></i>
            <h5 class="fw-bold text-dark">No Enrolled Course Found</h5>
            <p class="text-muted">You do not currently have an approved course enrolment record linked to your profile.</p>
        </div>
    @endif
</div>
@endsection

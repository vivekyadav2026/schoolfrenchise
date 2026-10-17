@extends('layouts.admin')

@section('title', 'Question Bank Management')

@section('content')
<div class="container-fluid p-0">
    <!-- Header with Action Buttons -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a;">
                <i class="fa-solid fa-circle-question me-2" style="color: #0891b2;"></i>Question Bank & Assessments
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.exams.index') }}">Examinations</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Question Bank</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-calendar-days me-1"></i> Exam Cycles
            </a>
            <a href="{{ route('admin.exams.questions.create') }}" class="btn btn-primary" style="background-color: #0891b2; border-color: #0891b2;">
                <i class="fa-solid fa-plus me-1"></i> Add Question
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total MCQs</div>
                        <h3 class="fw-bold my-1" style="color: #0f172a;">{{ number_format($stats['total']) }}</h3>
                        <div class="small text-muted">All active question items</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #f0fdf4; color: #16a34a;">
                        <i class="fa-solid fa-clipboard-question fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Easy Difficulty</div>
                        <h3 class="fw-bold my-1 text-success">{{ number_format($stats['easy']) }}</h3>
                        <div class="small text-muted">Basic fundamentals</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #dcfce7; color: #15803d;">
                        <i class="fa-solid fa-face-smile fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Medium Difficulty</div>
                        <h3 class="fw-bold my-1 text-warning">{{ number_format($stats['medium']) }}</h3>
                        <div class="small text-muted">Intermediate conceptual</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fef3c7; color: #b45309;">
                        <i class="fa-solid fa-face-meh fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Hard Difficulty</div>
                        <h3 class="fw-bold my-1 text-danger">{{ number_format($stats['hard']) }}</h3>
                        <div class="small text-muted">Advanced practical</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fee2e2; color: #b91c1c;">
                        <i class="fa-solid fa-brain fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.exams.questions') }}" class="row g-2">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Search question text..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="course_id" class="form-select bg-light">
                        <option value="">All Courses</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->course_name }} ({{ $c->course_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="difficulty" class="form-select bg-light">
                        <option value="">All Difficulties</option>
                        <option value="easy" {{ request('difficulty') === 'easy' ? 'selected' : '' }}>Easy</option>
                        <option value="medium" {{ request('difficulty') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="hard" {{ request('difficulty') === 'hard' ? 'selected' : '' }}>Hard</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100" style="background-color: #0891b2; border-color: #0891b2;">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.exams.questions') }}" class="btn btn-outline-secondary" title="Reset Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Questions Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-list-ol me-2 text-primary"></i>Multiple Choice Questions Roster
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3" style="width: 45%;">Question & Options</th>
                        <th>Course / Subject</th>
                        <th>Correct Answer</th>
                        <th>Marks</th>
                        <th>Difficulty</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $q)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-semibold text-dark mb-1">{{ Str::limit($q->question_text, 120) }}</div>
                                <div class="small text-muted d-flex flex-wrap gap-2">
                                    <span class="badge bg-light text-secondary border">A: {{ Str::limit($q->option_a, 25) }}</span>
                                    <span class="badge bg-light text-secondary border">B: {{ Str::limit($q->option_b, 25) }}</span>
                                    <span class="badge bg-light text-secondary border">C: {{ Str::limit($q->option_c, 25) }}</span>
                                    <span class="badge bg-light text-secondary border">D: {{ Str::limit($q->option_d, 25) }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $q->course->course_name ?? 'General' }}</div>
                                <small class="text-muted">{{ $q->subject->subject_name ?? 'General Module' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-success text-white fw-bold px-2 py-1 font-monospace text-uppercase">
                                    Option {{ strtoupper($q->correct_option) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $q->marks }} pt</span>
                            </td>
                            <td>
                                @if($q->difficulty === 'easy')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Easy</span>
                                @elseif($q->difficulty === 'medium')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Medium</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Hard</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.exams.questions.edit', $q) }}" class="btn btn-outline-secondary" title="Edit Question">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.exams.questions.destroy', $q) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete Question">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fa-solid fa-clipboard-question fs-1 text-secondary opacity-50"></i></div>
                                <h6>No Questions Found</h6>
                                <p class="small mb-3">Build your examination question bank by adding MCQs.</p>
                                <a href="{{ route('admin.exams.questions.create') }}" class="btn btn-sm btn-primary" style="background-color: #0891b2; border-color: #0891b2;">
                                    <i class="fa-solid fa-plus me-1"></i> Add Question Now
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($questions->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $questions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

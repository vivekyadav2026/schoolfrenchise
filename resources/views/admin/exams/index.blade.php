@extends('layouts.admin')

@section('title', 'Examinations Management')

@section('content')
<div class="container-fluid p-0">
    <!-- Header with Action Buttons -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a;">
                <i class="fa-solid fa-pen-nib me-2" style="color: #0891b2;"></i>Examinations & Assessment
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Examinations</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.exams.questions') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-circle-question me-1"></i> Question Bank
            </a>
            <a href="{{ route('admin.exams.results') }}" class="btn btn-outline-info">
                <i class="fa-solid fa-square-poll-vertical me-1"></i> Exam Results
            </a>
            <a href="{{ route('admin.exams.results.create') }}" class="btn btn-outline-success">
                <i class="fa-solid fa-marker me-1"></i> Enter Marks
            </a>
            <a href="{{ route('admin.exams.create') }}" class="btn btn-primary" style="background-color: #0891b2; border-color: #0891b2;">
                <i class="fa-solid fa-plus me-1"></i> Schedule Exam
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Exam Sessions</div>
                        <h3 class="fw-bold my-1" style="color: #0f172a;">{{ number_format($stats['total']) }}</h3>
                        <div class="small text-muted">All registered cycles</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #e0f2fe; color: #0284c7;">
                        <i class="fa-solid fa-calendar-days fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Scheduled / Upcoming</div>
                        <h3 class="fw-bold my-1 text-primary">{{ number_format($stats['scheduled']) }}</h3>
                        <div class="small text-muted">Awaiting exam date</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #eff6ff; color: #2563eb;">
                        <i class="fa-solid fa-clock fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Evaluated</div>
                        <h3 class="fw-bold my-1 text-warning">{{ number_format($stats['evaluated']) }}</h3>
                        <div class="small text-muted">Marks compiled</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fef3c7; color: #d97706;">
                        <i class="fa-solid fa-check-to-slot fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Published</div>
                        <h3 class="fw-bold my-1 text-success">{{ number_format($stats['published']) }}</h3>
                        <div class="small text-muted">Marksheets ready</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #dcfce7; color: #16a34a;">
                        <i class="fa-solid fa-circle-check fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.exams.index') }}" class="row g-2">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Search by Exam Title, Code, Session..." value="{{ request('search') }}">
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
                    <select name="status" class="form-select bg-light">
                        <option value="">All Statuses</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="evaluated" {{ request('status') === 'evaluated' ? 'selected' : '' }}>Evaluated</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100" style="background-color: #0891b2; border-color: #0891b2;">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Exam Sessions Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-list-check me-2 text-primary"></i>Examination Cycles Roster
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3">Exam Details</th>
                        <th>Course</th>
                        <th>Session</th>
                        <th>Exam Date</th>
                        <th>Candidates / Results</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">{{ $exam->exam_title }}</div>
                                <span class="badge bg-light text-secondary border font-monospace">{{ $exam->exam_code }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary fw-semibold">
                                    {{ $exam->course->course_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="fw-medium text-secondary">
                                <i class="fa-regular fa-calendar me-1"></i>{{ $exam->session_name }}
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $exam->exam_date ? $exam->exam_date->format('d M, Y') : 'N/A' }}</div>
                                <small class="text-muted">{{ $exam->exam_date ? $exam->exam_date->diffForHumans() : '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info fw-bold">
                                    {{ $exam->results_count }} Evaluated
                                </span>
                            </td>
                            <td>
                                @if($exam->status === 'scheduled')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"><i class="fa-solid fa-clock me-1"></i> Scheduled</span>
                                @elseif($exam->status === 'ongoing')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="fa-solid fa-spinner me-1"></i> Ongoing</span>
                                @elseif($exam->status === 'evaluated')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1"><i class="fa-solid fa-check-to-slot me-1"></i> Evaluated</span>
                                @elseif($exam->status === 'published')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i> Published</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-outline-info" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-outline-secondary" title="Edit Exam">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this examination cycle?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete Exam">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fa-solid fa-pen-nib fs-1 text-secondary opacity-50"></i></div>
                                <h6>No Examination Cycles Found</h6>
                                <p class="small mb-3">Schedule your first examination session or adjust your search filters.</p>
                                <a href="{{ route('admin.exams.create') }}" class="btn btn-sm btn-primary" style="background-color: #0891b2; border-color: #0891b2;">
                                    <i class="fa-solid fa-plus me-1"></i> Schedule Exam Now
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($exams->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $exams->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

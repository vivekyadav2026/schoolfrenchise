@extends('layouts.centre')

@section('title', 'Centre Batches & Timetable - TECH ERA COMPUTER')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-users text-info me-2"></i>Batches & Shifts Timetable
        </h3>
        <p class="text-muted small mb-0">Manage training batches, lab workstations schedule, and daily student attendance.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('centre.batches.create') }}" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
            <i class="fa-solid fa-plus me-1"></i>Create New Batch
        </a>
        <a href="{{ route('centre.batches.attendance.report') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="fa-solid fa-chart-line me-1"></i>Attendance Report
        </a>
    </div>
</div>

<!-- Batches Grid -->
<div class="row g-4">
    @forelse($batches as $batch)
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 rounded-4 shadow-sm h-100 hover-card">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <span class="badge {{ $batch->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                    {{ ucfirst($batch->status) }}
                </span>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('centre.batches.attendance', $batch->id) }}">
                                <i class="fa-solid fa-calendar-check text-success me-2"></i>Daily Attendance Sheet
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('centre.batches.edit', $batch->id) }}">
                                <i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Batch Schedule
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-1">{{ $batch->batch_name }}</h5>
                <div class="text-primary small fw-semibold mb-3">
                    <i class="fa-solid fa-graduation-cap me-1"></i>{{ $batch->course?->course_name ?? 'All Courses' }}
                </div>

                <div class="bg-light p-3 rounded-3 small mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><i class="fa-solid fa-clock me-1"></i>Shift Timing:</span>
                        <span class="fw-bold text-dark">{{ $batch->start_time }} - {{ $batch->end_time }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><i class="fa-solid fa-calendar-days me-1"></i>Working Days:</span>
                        <span class="fw-semibold text-dark">{{ $batch->days }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted"><i class="fa-solid fa-users-line me-1"></i>Enrolled Students:</span>
                        <span class="fw-bold text-primary">{{ $batch->admissions_count }} Candidates</span>
                    </div>
                </div>

                <a href="{{ route('centre.batches.attendance', $batch->id) }}" class="btn btn-outline-primary w-100 rounded-pill fw-semibold">
                    <i class="fa-solid fa-clipboard-user me-1"></i>Mark Today's Attendance
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="fa-solid fa-calendar-xmark fs-1 text-black-50 mb-3 d-block"></i>
        <h5>No Batches Created Yet</h5>
        <p class="text-muted small mb-3">Organize your students into morning, afternoon or evening lab batches.</p>
        <a href="{{ route('centre.batches.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fa-solid fa-plus me-1"></i>Create First Batch
        </a>
    </div>
    @endforelse
</div>

@if($batches->hasPages())
<div class="mt-4">
    {{ $batches->links() }}
</div>
@endif
@endsection

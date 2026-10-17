@extends('layouts.admin')

@section('title', $batch->batch_name . ' - TECH ERA COMPUTER ERP')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.batches.index') }}" class="text-decoration-none">Batches</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $batch->batch_name }}</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Batch Dossier & Schedule</h3>
        <p class="text-muted small mb-0">Shift timings, student roster, daily attendance logs, and curriculum progress.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.batches.attendance.sheet', $batch->id) }}" class="btn btn-success btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-clipboard-user me-1"></i> Mark Daily Attendance
        </a>
        <a href="{{ route('admin.batches.edit', $batch->id) }}" class="btn btn-outline-primary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-pen me-1"></i> Edit Batch
        </a>
        <a href="{{ route('admin.batches.index') }}" class="btn btn-light btn-sm px-3 shadow-sm border">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Batches
        </a>
    </div>
</div>

<!-- Batch Banner Card -->
<div class="card dce-table-card mb-4 border-top border-4 border-info">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-dark font-monospace fs-6">
                        <i class="fa-solid fa-building-columns me-1 text-primary"></i> {{ $batch->centre?->centre_code }}
                    </span>
                    @php
                        $statusBadge = match($batch->status) {
                            'active' => 'bg-success',
                            'completed' => 'bg-info',
                            'inactive' => 'bg-secondary',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <span class="badge {{ $statusBadge }}">{{ ucfirst($batch->status) }}</span>
                </div>
                <h4 class="fw-bold mb-1 text-dark">{{ $batch->batch_name }}</h4>
                <div class="text-muted small">
                    <span><i class="fa-solid fa-book-bookmark text-primary me-1"></i>Course: <strong class="text-dark">{{ $batch->course?->course_name }} ({{ $batch->course?->course_code }})</strong></span>
                    <span class="mx-2">•</span>
                    <span><i class="fa-solid fa-clock text-info me-1"></i>Shift: <strong class="font-monospace text-dark">{{ \Carbon\Carbon::parse($batch->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($batch->end_time)->format('h:i A') }}</strong></span>
                    <span class="mx-2">•</span>
                    <span><i class="fa-solid fa-calendar-days text-secondary me-1"></i>Days: {{ $batch->days }}</span>
                </div>
            </div>

            <div class="text-end">
                <div class="text-muted small fw-semibold text-uppercase">Enrolled Trainees</div>
                <div class="h3 fw-bold text-primary mb-0 font-monospace">{{ $batch->admissions->count() }} Students</div>
                <div class="text-muted small">{{ $batch->centre?->centre_name }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Enrolled Students Table -->
    <div class="col-lg-7">
        <div class="card dce-table-card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-user-graduate text-primary me-2"></i>Enrolled Students in this Batch ({{ $batch->admissions->count() }})
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Roll No / Reg</th>
                                <th>Student Name</th>
                                <th>Contact</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batch->admissions as $adm)
                            <tr>
                                <td>
                                    <div class="font-monospace fw-bold text-primary">{{ $adm->student?->roll_no }}</div>
                                    <div class="text-muted small" style="font-size: 11px;">{{ $adm->student?->registration_no }}</div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.students.show', $adm->student_id) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $adm->student?->full_name }}
                                    </a>
                                    <div class="text-muted small">S/o: {{ $adm->student?->father_name }}</div>
                                </td>
                                <td>
                                    <span class="small font-monospace">{{ $adm->student?->mobile }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success">{{ ucfirst($adm->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-users-slash fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">No students allocated to this batch yet.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance Activity -->
    <div class="col-lg-5">
        <div class="card dce-table-card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-clipboard-check text-success me-2"></i>Recent Attendance Logs
                </h6>
                <a href="{{ route('admin.batches.attendance.sheet', $batch->id) }}" class="btn btn-sm btn-outline-success">Mark Today</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Student</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAttendances as $att)
                            <tr>
                                <td class="font-monospace small text-dark">{{ \Carbon\Carbon::parse($att->attendance_date)->format('d M Y') }}</td>
                                <td>
                                    <div class="small fw-semibold text-dark">{{ $att->student?->full_name }}</div>
                                    <div class="text-muted font-monospace" style="font-size: 11px;">{{ $att->student?->roll_no }}</div>
                                </td>
                                <td>
                                    @if($att->status === 'present')
                                        <span class="badge bg-success">Present</span>
                                    @elseif($att->status === 'absent')
                                        <span class="badge bg-danger">Absent</span>
                                    @elseif($att->status === 'late')
                                        <span class="badge bg-warning text-dark">Late</span>
                                    @else
                                        <span class="badge bg-info">{{ ucfirst($att->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-clipboard fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">No attendance marked for this batch yet.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

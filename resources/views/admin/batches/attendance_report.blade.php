@extends('layouts.admin')

@section('title', 'Attendance Reports - TECH ERA COMPUTER ERP')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.batches.index') }}" class="text-decoration-none">Batches</a></li>
                <li class="breadcrumb-item active" aria-current="page">Attendance Reports</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Institutional Attendance Reports</h3>
        <p class="text-muted small mb-0">Nationwide classroom attendance records, discipline ratios, and student session tracking.</p>
    </div>
    <div>
        <a href="{{ route('admin.batches.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Batches
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
@php
    $total = $summary['total'];
    $present = $summary['present'];
    $ratio = $total > 0 ? round(($present / $total) * 100) : 0;
@endphp
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card dce-stat-card border-start border-4 border-primary">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase">Total Logged Sessions</div>
                <div class="h4 fw-bold mb-0 text-dark">{{ number_format($total) }}</div>
                <div class="text-muted small mt-1">Classroom marks recorded</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card dce-stat-card border-start border-4 border-success">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase">Present Attendance</div>
                <div class="h4 fw-bold mb-0 text-success">{{ number_format($present) }}</div>
                <div class="text-muted small mt-1">Sessions attended</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card dce-stat-card border-start border-4 border-danger">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase">Absent Sessions</div>
                <div class="h4 fw-bold mb-0 text-danger">{{ number_format($summary['absent']) }}</div>
                <div class="text-muted small mt-1">Unexcused absences</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card dce-stat-card border-start border-4 border-info">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase">Overall Attendance Ratio</div>
                <div class="h4 fw-bold mb-0 text-info">{{ $ratio }}%</div>
                <div class="text-muted small mt-1">Institutional presence metric</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card dce-table-card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.batches.attendance.report') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="centre_id" class="form-select">
                    <option value="">-- All Centres --</option>
                    @foreach($centres as $c)
                        <option value="{{ $c->id }}" {{ request('centre_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->centre_code }} - {{ $c->centre_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="batch_id" class="form-select">
                    <option value="">-- All Batches --</option>
                    @foreach($batches as $b)
                        <option value="{{ $b->id }}" {{ request('batch_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->batch_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">-- All Statuses --</option>
                    <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Present</option>
                    <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Late</option>
                    <option value="leave" {{ request('status') === 'leave' ? 'selected' : '' }}>Leave</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="From Date">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i>Filter</button>
                @if(request()->anyFilled(['centre_id', 'batch_id', 'status', 'from_date', 'to_date']))
                    <a href="{{ route('admin.batches.attendance.report') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="fa-solid fa-xmark"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Attendance Logs Table -->
<div class="card dce-table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-list-check text-primary me-2"></i>Attendance Log Entries
            <span class="badge bg-secondary-subtle text-secondary ms-2">{{ $attendances->total() }} Records</span>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Centre</th>
                        <th>Batch</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                    <tr>
                        <td class="fw-semibold font-monospace">{{ \Carbon\Carbon::parse($att->attendance_date)->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.students.show', $att->student_id) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $att->student?->full_name }}
                            </a>
                            <div class="text-muted font-monospace small">Roll: {{ $att->student?->roll_no }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark font-monospace border">{{ $att->centre?->centre_code }}</span>
                        </td>
                        <td>
                            <span class="small fw-semibold text-primary">{{ $att->batch?->batch_name }}</span>
                        </td>
                        <td>
                            @php
                                $attBadge = match($att->status) {
                                    'present' => 'bg-success',
                                    'absent' => 'bg-danger',
                                    'late' => 'bg-warning text-dark',
                                    'leave' => 'bg-info',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $attBadge }}">{{ ucfirst($att->status) }}</span>
                        </td>
                        <td class="text-muted small">
                            {{ $att->remarks ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-clipboard-question fa-2x mb-3 text-secondary"></i>
                            <p class="mb-0">No attendance records found matching your filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($attendances->hasPages())
    <div class="card-footer bg-white py-3 border-top">
        {{ $attendances->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

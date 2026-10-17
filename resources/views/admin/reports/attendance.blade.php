@extends('layouts.admin')

@section('title', 'Attendance Audit Report')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Attendance</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-clipboard-user text-info me-2"></i>Classroom Attendance & Engagement Report
            </h2>
            <p class="text-muted small mb-0">Track student regularity rates, batch attendance percentages, and absenteeism across franchises.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Reports Hub
            </a>
            <a href="{{ route('admin.reports.attendance.export', request()->query()) }}" class="btn btn-success shadow-sm">
                <i class="fa-solid fa-file-csv me-1"></i> Export Attendance CSV
            </a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Total Sessions</div>
                    <div class="h4 fw-bold text-dark mb-0">{{ number_format($stats['total']) }}</div>
                    <div class="text-muted small mt-1">Queried attendance entries</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Overall Attendance Rate</div>
                    <div class="h4 fw-bold text-success mb-0">{{ $stats['present_rate'] }}%</div>
                    <div class="text-muted small mt-1">{{ number_format($stats['present']) }} present sessions</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Absent Sessions</div>
                    <div class="h4 fw-bold text-danger mb-0">{{ number_format($stats['absent']) }}</div>
                    <div class="text-muted small mt-1">Unexcused missed classes</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Late Arrivals</div>
                    <div class="h4 fw-bold text-warning mb-0">{{ number_format($stats['late']) }}</div>
                    <div class="text-muted small mt-1">Punctuality discrepancies</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.attendance') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="centre_id" class="form-select form-select-sm">
                        <option value="">All Centres</option>
                        @foreach($centres as $c)
                            <option value="{{ $c->id }}" {{ request('centre_id') == $c->id ? 'selected' : '' }}>{{ $c->centre_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="batch_id" class="form-select form-select-sm">
                        <option value="">All Batches</option>
                        @foreach($batches as $b)
                            <option value="{{ $b->id }}" {{ request('batch_id') == $b->id ? 'selected' : '' }}>{{ $b->batch_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                        <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                        <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                        <option value="excused" {{ request('status') == 'excused' ? 'selected' : '' }}>Excused</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-1">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" title="Date From">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" title="Date To">
                </div>

                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Apply Filter"><i class="fa-solid fa-filter"></i></button>
                    @if(request()->hasAny(['centre_id', 'batch_id', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('admin.reports.attendance') }}" class="btn btn-sm btn-light border" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Date</th>
                        <th>Student Name & Roll</th>
                        <th>Batch Particulars</th>
                        <th>Centre / Branch</th>
                        <th>In / Out Timing</th>
                        <th class="text-end pe-3">Attendance Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $a)
                        <tr>
                            <td class="ps-3 font-monospace small text-muted">
                                {{ $a->attendance_date ? $a->attendance_date->format('d M, Y') : '-' }}
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $a->student ? $a->student->full_name : '-' }}</div>
                                <div class="small text-muted font-monospace">{{ $a->student ? $a->student->roll_no : '-' }}</div>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">{{ $a->batch ? $a->batch->batch_name : '-' }}</div>
                            </td>
                            <td>
                                <div class="small text-dark">{{ $a->centre ? $a->centre->centre_name : '-' }}</div>
                            </td>
                            <td class="font-monospace small text-muted">
                                {{ $a->in_time ? \Carbon\Carbon::parse($a->in_time)->format('h:i A') : '--:--' }} -
                                {{ $a->out_time ? \Carbon\Carbon::parse($a->out_time)->format('h:i A') : '--:--' }}
                            </td>
                            <td class="text-end pe-3">
                                @if($a->status == 'present')
                                    <span class="badge bg-success">Present</span>
                                @elseif($a->status == 'absent')
                                    <span class="badge bg-danger">Absent</span>
                                @elseif($a->status == 'late')
                                    <span class="badge bg-warning text-dark">Late</span>
                                @else
                                    <span class="badge bg-secondary">Excused</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                No attendance records found for specified dates.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

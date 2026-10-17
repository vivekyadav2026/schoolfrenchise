@extends('layouts.admin')

@section('title', 'Batches & Attendance - TECH ERA COMPUTER ERP')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Batches</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Batches & Attendance Management</h3>
        <p class="text-muted small mb-0">Organize training shifts, allocate enrolled students, and track daily attendance records.</p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('admin.batches.attendance.report') }}" class="btn btn-outline-info btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-chart-pie me-1"></i> Attendance Reports
        </a>
        <a href="{{ route('admin.batches.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Create New Batch
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card dce-stat-card border-start border-4 border-primary">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase">Total Batches</div>
                <div class="h4 fw-bold mb-0 text-dark">{{ $counts['total'] }}</div>
                <div class="text-muted small mt-1">Classroom shifts defined</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card dce-stat-card border-start border-4 border-success">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase">Active Batches</div>
                <div class="h4 fw-bold mb-0 text-success">{{ $counts['active'] }}</div>
                <div class="text-muted small mt-1">Ongoing classroom sessions</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card dce-stat-card border-start border-4 border-info">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase">Completed Batches</div>
                <div class="h4 fw-bold mb-0 text-info">{{ $counts['completed'] }}</div>
                <div class="text-muted small mt-1">Graduated groups</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card dce-stat-card border-start border-4 border-warning">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase">Total Attendance Logs</div>
                <div class="h4 fw-bold mb-0 text-warning">{{ $counts['total_attendances'] }}</div>
                <div class="text-muted small mt-1">Marked student sessions</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card dce-table-card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.batches.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="Search batch name..." value="{{ request('search') }}">
                </div>
            </div>
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
                <select name="course_id" class="form-select">
                    <option value="">-- All Courses --</option>
                    @foreach($courses as $crs)
                        <option value="{{ $crs->id }}" {{ request('course_id') == $crs->id ? 'selected' : '' }}>
                            {{ $crs->course_code }} - {{ $crs->course_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i>Filter</button>
                @if(request()->anyFilled(['search', 'centre_id', 'course_id', 'status']))
                    <a href="{{ route('admin.batches.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="fa-solid fa-xmark"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Batches Table -->
<div class="card dce-table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-calendar-check text-primary me-2"></i>Batches Directory
            <span class="badge bg-secondary-subtle text-secondary ms-2">{{ $batches->total() }} Batches</span>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Batch Name</th>
                        <th>Course</th>
                        <th>Centre</th>
                        <th>Timings & Schedule</th>
                        <th>Students Enrolled</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $b)
                    <tr>
                        <td>
                            <a href="{{ route('admin.batches.show', $b->id) }}" class="fw-bold text-primary text-decoration-none">
                                {{ $b->batch_name }}
                            </a>
                            <div class="text-muted small">Days: {{ $b->days }}</div>
                        </td>
                        <td>
                            <span class="badge bg-info-subtle text-info">{{ $b->course?->course_code }}</span>
                            <div class="small text-truncate" style="max-width: 160px;">{{ $b->course?->course_name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-dark-subtle text-dark font-monospace">{{ $b->centre?->centre_code }}</span>
                            <div class="small text-muted text-truncate" style="max-width: 140px;">{{ $b->centre?->centre_name }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark font-monospace small">
                                <i class="fa-solid fa-clock text-primary me-1"></i>
                                {{ \Carbon\Carbon::parse($b->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($b->end_time)->format('h:i A') }}
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary font-monospace fs-6">
                                <i class="fa-solid fa-users me-1"></i>{{ $b->admissions_count }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusBadge = match($b->status) {
                                    'active' => 'bg-success',
                                    'completed' => 'bg-info',
                                    'inactive' => 'bg-secondary',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $statusBadge }}">{{ ucfirst($b->status) }}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.batches.attendance.sheet', $b->id) }}" class="btn btn-outline-success" title="Mark Daily Attendance">
                                    <i class="fa-solid fa-clipboard-user"></i>
                                </a>
                                <a href="{{ route('admin.batches.show', $b->id) }}" class="btn btn-outline-primary" title="View Batch">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.batches.edit', $b->id) }}" class="btn btn-outline-secondary" title="Edit Batch">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.batches.destroy', $b->id) }}" class="d-inline" data-confirm="Are you sure you want to soft-delete this batch?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Soft Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-calendar-xmark fa-2x mb-3 text-secondary"></i>
                            <p class="mb-0">No batches found matching your criteria.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($batches->hasPages())
    <div class="card-footer bg-white py-3 border-top">
        {{ $batches->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

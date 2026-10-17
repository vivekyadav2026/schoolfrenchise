@extends('layouts.admin')

@section('title', 'Student & Admissions Report')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Students</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-user-graduate text-primary me-2"></i>Student & Admissions Audit Report
            </h2>
            <p class="text-muted small mb-0">Demographic breakdowns, course preferences, gender metrics, and admission status ledger.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Reports Hub
            </a>
            <a href="{{ route('admin.reports.students.export', request()->query()) }}" class="btn btn-success shadow-sm">
                <i class="fa-solid fa-file-csv me-1"></i> Export Filtered CSV
            </a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Total Matches</div>
                    <div class="h4 fw-bold text-dark mb-0">{{ number_format($stats['total']) }}</div>
                    <div class="text-muted small mt-1">Queried student records</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Active Students</div>
                    <div class="h4 fw-bold text-success mb-0">{{ number_format($stats['active']) }}</div>
                    <div class="text-muted small mt-1">Currently enrolled & attending</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Male Candidates</div>
                    <div class="h4 fw-bold text-info mb-0">{{ number_format($stats['male']) }}</div>
                    <div class="text-muted small mt-1">{{ $stats['total'] > 0 ? round(($stats['male'] / $stats['total']) * 100, 1) : 0 }}% of filtered dataset</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Female Candidates</div>
                    <div class="h4 fw-bold text-warning mb-0">{{ number_format($stats['female']) }}</div>
                    <div class="text-muted small mt-1">{{ $stats['total'] > 0 ? round(($stats['female'] / $stats['total']) * 100, 1) : 0 }}% of filtered dataset</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.students') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, roll no, phone..." value="{{ request('search') }}">
                </div>

                <div class="col-md-2">
                    <select name="centre_id" class="form-select form-select-sm">
                        <option value="">All Centres</option>
                        @foreach($centres as $c)
                            <option value="{{ $c->id }}" {{ request('centre_id') == $c->id ? 'selected' : '' }}>{{ $c->centre_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="course_id" class="form-select form-select-sm">
                        <option value="">All Courses</option>
                        @foreach($courses as $crs)
                            <option value="{{ $crs->id }}" {{ request('course_id') == $crs->id ? 'selected' : '' }}>{{ $crs->course_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive / Dropped</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="gender" class="form-select form-select-sm">
                        <option value="">All Genders</option>
                        <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Apply Filter"><i class="fa-solid fa-filter"></i></button>
                    @if(request()->hasAny(['search', 'centre_id', 'course_id', 'status', 'gender', 'date_from', 'date_to']))
                        <a href="{{ route('admin.reports.students') }}" class="btn btn-sm btn-light border" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
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
                        <th class="ps-3">Roll & Reg No</th>
                        <th>Student Name</th>
                        <th>Mobile & Email</th>
                        <th>Centre / Branch</th>
                        <th>Current Course</th>
                        <th>Admission Date</th>
                        <th>Gender</th>
                        <th class="text-end pe-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $s)
                        @php $adm = $s->admissions->first(); @endphp
                        <tr>
                            <td class="ps-3 font-monospace">
                                <div class="fw-bold text-dark">{{ $s->roll_no ?? '-' }}</div>
                                <div class="small text-muted">{{ $s->registration_no ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $s->full_name }}</div>
                            </td>
                            <td>
                                <div class="small text-dark font-monospace">{{ $s->mobile }}</div>
                                @if($s->email)
                                    <div class="small text-muted">{{ $s->email }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">{{ $adm && $adm->centre ? $adm->centre->centre_name : '-' }}</div>
                                <div class="small text-muted font-monospace">{{ $adm && $adm->centre ? $adm->centre->centre_code : '' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $adm && $adm->course ? $adm->course->course_name : '-' }}</span>
                            </td>
                            <td class="font-monospace small text-muted">
                                {{ ($adm && $adm->admission_date) ? \Carbon\Carbon::parse($adm->admission_date)->format('d M, Y') : ($s->created_at ? $s->created_at->format('d M, Y') : '-') }}
                            </td>
                            <td>
                                <span class="badge bg-light text-muted border text-capitalize">{{ $s->gender ?? '-' }}</span>
                            </td>
                            <td class="text-end pe-3">
                                @if($s->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($s->status == 'completed')
                                    <span class="badge bg-info">Completed</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                No students matched the specified criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

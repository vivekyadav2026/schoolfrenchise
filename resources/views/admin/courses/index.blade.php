@extends('layouts.admin')

@section('title', 'Courses Management - TECH ERA COMPUTER ERP')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Courses</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Course Curriculum Management</h3>
        <p class="text-muted small mb-0">Create computer software, hardware, accounting and vocational courses with standardized fees and syllabi.</p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Add New Course
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card dce-stat-card border-start border-4 border-primary">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase">Total Courses</div>
                <div class="h4 fw-bold mb-0 text-dark">{{ $counts['total'] }}</div>
                <div class="text-muted small mt-1">Institutional catalog</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card dce-stat-card border-start border-4 border-success">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase">Active & Published</div>
                <div class="h4 fw-bold mb-0 text-success">{{ $counts['active'] }}</div>
                <div class="text-muted small mt-1">Available for admissions</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card dce-stat-card border-start border-4 border-warning">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase">Popular Courses</div>
                <div class="h4 fw-bold mb-0 text-warning">{{ $counts['popular'] }}</div>
                <div class="text-muted small mt-1">Featured on homepage</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card dce-stat-card border-start border-4 border-info">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase">Categories</div>
                <div class="h4 fw-bold mb-0 text-info">{{ $counts['categories'] }}</div>
                <div class="text-muted small mt-1">Academic disciplines</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card dce-table-card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.courses.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="Search course code, name, duration..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">-- All Categories --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">-- All Statuses --</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i>Filter</button>
                @if(request()->anyFilled(['search', 'category_id', 'status']))
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="fa-solid fa-xmark"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Courses Table -->
<div class="card dce-table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-book-bookmark text-primary me-2"></i>Curriculum Directory
            <span class="badge bg-secondary-subtle text-secondary ms-2">{{ $courses->total() }} Courses</span>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Course Title & Category</th>
                        <th>Duration</th>
                        <th>Eligibility</th>
                        <th>Total Fee</th>
                        <th>Enrollments</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $c)
                    <tr>
                        <td>
                            <span class="badge bg-dark font-monospace fs-6">{{ $c->course_code }}</span>
                            @if($c->is_popular)
                                <div class="mt-1"><span class="badge bg-warning text-dark" style="font-size: 10px;">★ POPULAR</span></div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $c->course_name }}</div>
                            <div class="text-muted small">{{ $c->category?->name ?? 'General IT' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">{{ $c->duration }}</span>
                        </td>
                        <td>
                            <span class="small text-muted">{{ $c->eligibility }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-success fs-6">₹{{ number_format($c->total_fee, 2) }}</div>
                            <div class="text-muted" style="font-size: 11px;">Base: ₹{{ number_format($c->default_course_fee) }}</div>
                        </td>
                        <td>
                            <span class="badge bg-info-subtle text-info font-monospace">{{ $c->admissions_count }} Admitted</span>
                            <div class="text-muted" style="font-size: 11px;">{{ $c->batches_count }} Batches</div>
                        </td>
                        <td>
                            @if($c->status)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.courses.show', $c->id) }}" class="btn btn-outline-primary" title="View Syllabus">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.courses.edit', $c->id) }}" class="btn btn-outline-secondary" title="Edit Course">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.courses.destroy', $c->id) }}" class="d-inline" data-confirm="Are you sure you want to soft-delete this course?">
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
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-book-open fa-2x mb-3 text-secondary"></i>
                            <p class="mb-0">No courses found matching your query.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($courses->hasPages())
    <div class="card-footer bg-white py-3 border-top">
        {{ $courses->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

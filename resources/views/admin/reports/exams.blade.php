@extends('layouts.admin')

@section('title', 'Examination Results & Performance Report')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Examinations</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-award text-warning me-2"></i>Examination Performance & Results Analytics
            </h2>
            <p class="text-muted small mb-0">Evaluate student examination outcomes, pass percentages, average scores, and grade distributions.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Reports Hub
            </a>
            <a href="{{ route('admin.reports.exams.export', request()->query()) }}" class="btn btn-success shadow-sm">
                <i class="fa-solid fa-file-csv me-1"></i> Export Results CSV
            </a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Total Evaluated</div>
                    <div class="h4 fw-bold text-dark mb-0">{{ number_format($stats['total']) }}</div>
                    <div class="text-muted small mt-1">Appeared candidate marks</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Pass Rate</div>
                    <div class="h4 fw-bold text-success mb-0">{{ $stats['pass_rate'] }}%</div>
                    <div class="text-muted small mt-1">{{ number_format($stats['passed']) }} passed candidates</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Failed Candidates</div>
                    <div class="h4 fw-bold text-danger mb-0">{{ number_format($stats['failed']) }}</div>
                    <div class="text-muted small mt-1">Below minimum passing score</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Average Score</div>
                    <div class="h4 fw-bold text-info mb-0">{{ $stats['avg_marks'] }}%</div>
                    <div class="text-muted small mt-1">Mean percentage across tests</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.exams') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="exam_id" class="form-select form-select-sm">
                        <option value="">All Examinations</option>
                        @foreach($exams as $ex)
                            <option value="{{ $ex->id }}" {{ request('exam_id') == $ex->id ? 'selected' : '' }}>{{ $ex->exam_title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="course_id" class="form-select form-select-sm">
                        <option value="">All Courses</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->course_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="centre_id" class="form-select form-select-sm">
                        <option value="">All Centres</option>
                        @foreach($centres as $cnt)
                            <option value="{{ $cnt->id }}" {{ request('centre_id') == $cnt->id ? 'selected' : '' }}>{{ $cnt->centre_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="result_status" class="form-select form-select-sm">
                        <option value="">All Result Statuses</option>
                        <option value="pass" {{ request('result_status') == 'pass' ? 'selected' : '' }}>Passed</option>
                        <option value="fail" {{ request('result_status') == 'fail' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Apply Filter"><i class="fa-solid fa-filter"></i></button>
                    @if(request()->hasAny(['exam_id', 'course_id', 'centre_id', 'result_status']))
                        <a href="{{ route('admin.reports.exams') }}" class="btn btn-sm btn-light border" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Student & Roll No</th>
                        <th>Examination Title</th>
                        <th>Course</th>
                        <th>Centre / Branch</th>
                        <th class="text-end">Marks Obtained</th>
                        <th class="text-center">Percentage</th>
                        <th class="text-center">Grade</th>
                        <th class="text-end pe-3">Result</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $r)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">{{ $r->student ? $r->student->full_name : '-' }}</div>
                                <div class="small text-muted font-monospace">{{ $r->student ? $r->student->roll_no : '-' }}</div>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">{{ $r->exam ? $r->exam->exam_title : '-' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $r->course ? $r->course->course_name : '-' }}</span>
                            </td>
                            <td>
                                <div class="small text-dark">{{ $r->centre ? $r->centre->centre_name : '-' }}</div>
                            </td>
                            <td class="text-end font-monospace">
                                <span class="fw-bold text-dark">{{ $r->total_marks_obtained }}</span>
                                <span class="text-muted small">/ {{ $r->total_max_marks }}</span>
                            </td>
                            <td class="text-center font-monospace fw-semibold">
                                {{ $r->percentage }}%
                            </td>
                            <td class="text-center font-monospace">
                                <span class="badge bg-light text-dark border fw-bold">{{ $r->grade ?? '-' }}</span>
                            </td>
                            <td class="text-end pe-3">
                                @if(in_array($r->result_status, ['pass', 'passed']))
                                    <span class="badge bg-success">Passed</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                No examination results found matching the query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($results->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $results->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

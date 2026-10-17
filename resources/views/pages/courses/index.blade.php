@extends('layouts.app')

@section('title', 'Courses Directory | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))
@section('meta_description', 'Explore industry-demand computer courses: ADCA, DCA, BCC, CCC, Tally Prime with GST, Typing, Python, and Graphic Design with practical lab sessions and digital certificate verification at TECH ERA COMPUTER.')

@section('content')
    <div class="dce-page-banner">
        <div class="container">
            <h1>Our Certified IT & Vocational Courses</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Courses</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <!-- Filter Bar & Search -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light">
                <form action="{{ route('courses.index') }}" method="GET" class="row g-3 align-items-center">
                    <div class="col-lg-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Search by course name, code or keyword..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 d-flex gap-2">
                        <button type="submit" class="btn btn-dce-primary w-100 fw-bold">
                            <i class="fa-solid fa-filter me-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'category']))
                            <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Courses Grid -->
            @if($courses->isEmpty())
                <div class="text-center py-5">
                    <div class="text-muted fs-1 mb-3"><i class="fa-solid fa-folder-open"></i></div>
                    <h4 class="fw-bold text-dark">No courses found matching your criteria</h4>
                    <p class="text-muted">Try adjusting your search keyword or clearing category filters.</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary mt-2">View All Courses</a>
                </div>
            @else
                <div class="row g-4">
                    @foreach($courses as $course)
                        <div class="col-lg-4 col-md-6">
                            <div class="dce-course-card">
                                <div class="dce-course-header">
                                    <span class="dce-course-badge">{{ $course->category->name ?? 'Course' }}</span>
                                    <div class="dce-course-code">{{ $course->course_code }}</div>
                                    <h3 class="dce-course-title">
                                        <a href="{{ route('courses.show', $course->slug) }}" class="text-decoration-none text-dark">
                                            {{ $course->course_name }}
                                        </a>
                                    </h3>
                                </div>
                                <div class="dce-course-body">
                                    <div class="dce-course-meta">
                                        <span><i class="fa-regular fa-clock"></i> {{ $course->duration }}</span>
                                        <span><i class="fa-solid fa-user-graduate"></i> {{ $course->eligibility }}</span>
                                    </div>
                                    <p class="text-muted small mb-3">
                                        {{ Str::limit(strip_tags($course->syllabus), 120) }}
                                    </p>
                                    <hr>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <small class="text-muted d-block" style="font-size:0.75rem;">Standard Fee</small>
                                            <span class="fw-bold text-primary fs-5">
                                                ₹{{ number_format($course->default_course_fee, 2) }}
                                            </span>
                                        </div>
                                        <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-sm btn-dce-primary">
                                            View Details <i class="fa-solid fa-chevron-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-5 d-flex justify-content-center">
                    {{ $courses->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection

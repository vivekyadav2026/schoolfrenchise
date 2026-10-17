@extends('layouts.app')

@section('title', 'Check Admission Status | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center text-md-start">
            <h1>Online Student Admission Verification</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Student Zone</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Check Admission</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Search Box -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h4 class="fw-bold text-primary mb-3"><i class="fa-solid fa-id-card-clip me-2"></i>Verify Admission Record</h4>
                        <p class="text-muted small mb-4">Enter Student Roll Number or Registration Number provided by your study centre.</p>

                        <form action="{{ route('verify.admission.submit') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Registration No. / Roll No. <span class="text-danger">*</span></label>
                                <input type="text" name="search_key" class="form-control" placeholder="e.g. TEC2601001" value="{{ $searchKey ?? old('search_key') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date of Birth (Optional for extra security)</label>
                                <input type="date" name="dob" class="form-control" value="{{ $dob ?? old('dob') }}">
                            </div>
                            <button type="submit" class="btn btn-dce-primary w-100 py-2 fw-bold">
                                <i class="fa-solid fa-magnifying-glass me-2"></i> Verify Admission
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Result Box -->
                @if(isset($searched))
                    <div class="col-lg-8">
                        @if($student)
                            <div class="dce-verified-card mb-4">
                                <div class="dce-verified-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="fw-bold mb-0 text-white"><i class="fa-solid fa-circle-check me-2"></i>Verified Student Admission Record</h4>
                                        <small class="text-white-50">Central Database of {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</small>
                                    </div>
                                    <span class="badge bg-white text-success px-3 py-2 fw-bold uppercase">
                                        {{ $student->status == 'completed' ? 'Course Completed' : 'Active Student' }}
                                    </span>
                                </div>
                                <div class="p-4 bg-white">
                                    <div class="row g-4 align-items-center">
                                        <div class="col-md-3 text-center">
                                            <div class="rounded-circle border p-1 bg-light d-inline-block">
                                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 110px; height: 110px; font-size: 3rem;">
                                                    <i class="fa-solid fa-user-graduate"></i>
                                                </div>
                                            </div>
                                            <div class="mt-2 fw-bold text-dark">{{ $student->full_name }}</div>
                                            <small class="text-muted d-block">ID: {{ $student->roll_no }}</small>
                                        </div>

                                        <div class="col-md-9">
                                            <div class="row g-2 small">
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Student Name:</span>
                                                    <strong>{{ $student->full_name }}</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Father's Name:</span>
                                                    <strong>{{ $student->father_name }}</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Registration Number:</span>
                                                    <code>{{ $student->registration_no }}</code>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Roll Number:</span>
                                                    <code>{{ $student->roll_no }}</code>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Enrolled Study Centre:</span>
                                                    <strong>{{ $student->centre->centre_name ?? 'Corporate Head Office' }} ({{ $student->centre->centre_code ?? 'DCE-1001' }})</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">City / District:</span>
                                                    <strong>{{ $student->city }}, {{ $student->district->name ?? '' }}</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Date of Birth:</span>
                                                    <strong>{{ $student->dob ? $student->dob->format('d M, Y') : 'N/A' }}</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <span class="text-muted d-block">Admission Date:</span>
                                                    <strong>{{ $student->admissions->first()?->admission_date ? $student->admissions->first()->admission_date->format('d M, Y') : $student->created_at->format('d M, Y') }}</strong>
                                                </div>
                                            </div>

                                            @if($student->admissions->isNotEmpty())
                                                <div class="mt-3 p-3 bg-light rounded-3 border">
                                                    <strong class="d-block text-primary"><i class="fa-solid fa-book-open me-2"></i>Enrolled Academic Program:</strong>
                                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                                        <span><strong>{{ $student->admissions->first()->course->course_name }}</strong> ({{ $student->admissions->first()->course->course_code }})</span>
                                                        <span class="badge bg-primary">{{ $student->admissions->first()->course->duration }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-danger shadow-sm rounded-4 p-4 text-center">
                                <div class="fs-1 text-danger mb-2"><i class="fa-solid fa-triangle-exclamation"></i></div>
                                <h4 class="fw-bold text-danger">No Admission Record Found</h4>
                                <p class="text-muted mb-3">No active or completed student admission was located for query: <code>{{ $searchKey }}</code>.</p>
                                <p class="small text-secondary mb-0">Please verify your Registration Number or Roll Number with your study centre administrator.</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

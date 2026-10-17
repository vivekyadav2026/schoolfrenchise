@extends('layouts.app')

@section('title', 'Online Admission Enquiry | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container">
            <h1>Online Student Admission Enquiry</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Admission Enquiry</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5">
                        <div class="text-center mb-4">
                            <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill uppercase mb-2">Student Admissions</span>
                            <h2 class="fw-extrabold text-dark mb-1">Apply or Enquire for Computer Courses</h2>
                            <p class="text-muted">Fill out this quick form to receive course fee concession, syllabus, and nearest study centre details.</p>
                        </div>

                        <form action="{{ route('enquiry.admission.submit') }}" method="POST">
                            @csrf
                            <input type="hidden" name="subject" value="Student Direct Admission Enquiry">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Student Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter student full name" value="{{ old('name') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">WhatsApp Mobile Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="mobile" class="form-control" placeholder="10-digit mobile number" value="{{ old('mobile') }}" required>
                                    <small class="text-muted" style="font-size:0.75rem;">Prospectus will be sent to this WhatsApp number.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" placeholder="student@example.com" value="{{ old('email') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Interested Course <span class="text-danger">*</span></label>
                                    <select name="course_id" class="form-select" required>
                                        <option value="">Select Course</option>
                                        @foreach($courses as $c)
                                            <option value="{{ $c->id }}" {{ old('course_id') == $c->id ? 'selected' : '' }}>
                                                {{ $c->course_name }} ({{ $c->course_code }} - {{ $c->duration }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">State <span class="text-danger">*</span></label>
                                    <select name="state_id" class="form-select dce-state-select" data-district-target="adm_district_id" required>
                                        <option value="">Select State</option>
                                        @foreach($states as $st)
                                            <option value="{{ $st->id }}" {{ old('state_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">District <span class="text-danger">*</span></label>
                                    <select name="district_id" id="adm_district_id" class="form-select" required>
                                        <option value="">Select District</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Questions / Preference</label>
                                    <textarea name="message" class="form-control" rows="3" placeholder="Tell us if you prefer morning/evening batch, online/offline, etc.">{{ old('message') }}</textarea>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-dce-accent w-100 py-3 fw-bold fs-6">
                                        <i class="fa-solid fa-paper-plane me-2"></i> Submit Admission Enquiry
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

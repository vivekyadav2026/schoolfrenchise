@extends('layouts.app')

@section('title', 'Apply for New Franchise | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))
@section('meta_description', 'Start your own authorized computer institute with ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER') . '. Complete practical curriculum, student management portal, and verified credentials.')

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center text-md-start">
            <h1>Apply for New Authorized Franchise</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Franchise</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Apply</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row g-5">
                <!-- Left: Benefits & Information -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-primary text-white">
                        <h4 class="fw-bold mb-3"><i class="fa-solid fa-handshake me-2 text-warning"></i>Why Partner With {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}?</h4>
                        <ul class="list-unstyled small mb-0">
                            <li class="py-2 border-bottom border-white-50"><i class="fa-solid fa-check text-warning me-2"></i>Structured Job-Oriented Curricula</li>
                            <li class="py-2 border-bottom border-white-50"><i class="fa-solid fa-check text-warning me-2"></i>Complete Student Self-Service Portal</li>
                            <li class="py-2 border-bottom border-white-50"><i class="fa-solid fa-check text-warning me-2"></i>Instant QR-Verified Marksheets & Certificates</li>
                            <li class="py-2 border-bottom border-white-50"><i class="fa-solid fa-check text-warning me-2"></i>Automated Admission & Receipt Generation</li>
                            <li class="py-2 border-bottom border-white-50"><i class="fa-solid fa-check text-warning me-2"></i>Bilingual Typing (Hindi/English) Lab Setup Guidance</li>
                            <li class="py-2 border-bottom border-white-50"><i class="fa-solid fa-check text-warning me-2"></i>Dedicated Operational & Technical Support</li>
                            <li class="py-2"><i class="fa-solid fa-check text-warning me-2"></i>Digital Examination & Grading Framework</li>
                        </ul>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-light">
                        <h5 class="fw-bold text-dark mb-2"><i class="fa-solid fa-circle-question text-info me-2"></i>Need Franchise Help?</h5>
                        <p class="text-muted small mb-3">Speak with our campus expansion team for guidance on infrastructure requirements and affiliation setup.</p>
                        <a href="tel:{{ $instituteSettings['contact_phone'] ?? '9162384660' }}" class="btn btn-warning w-100 fw-bold text-dark">
                            <i class="fa-solid fa-phone me-1"></i> Call {{ $instituteSettings['contact_phone'] ?? '9162384660' }}
                        </a>
                    </div>
                </div>

                <!-- Right: Application Form -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5">
                        <div class="border-bottom pb-3 mb-4">
                            <h3 class="fw-bold text-primary mb-1">Franchise Affiliation Application Form</h3>
                            <small class="text-muted">Enter your personal and proposed computer centre details below.</small>
                        </div>

                        <form action="{{ route('franchise.apply.submit') }}" method="POST">
                            @csrf
                            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-user text-primary me-2"></i>1. Applicant / Director Information</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Applicant Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="applicant_name" class="form-control" placeholder="Director Name" value="{{ old('applicant_name') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Director Qualification <span class="text-danger">*</span></label>
                                    <input type="text" name="qualification" class="form-control" placeholder="e.g. BCA, MCA, B.Tech, Graduate" value="{{ old('qualification') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="mobile" class="form-control" placeholder="10-digit mobile number" value="{{ old('mobile') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" placeholder="director@example.com" value="{{ old('email') }}" required>
                                </div>
                            </div>

                            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-building text-primary me-2"></i>2. Proposed Study Centre & Infrastructure</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Proposed Centre Name <span class="text-danger">*</span></label>
                                    <input type="text" name="centre_name" class="form-control" placeholder="e.g. Tech Era Computer Centre" value="{{ old('centre_name') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">City / Town / Village <span class="text-danger">*</span></label>
                                    <input type="text" name="city" class="form-control" placeholder="City Name" value="{{ old('city') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">State <span class="text-danger">*</span></label>
                                    <select name="state_id" class="form-select dce-state-select" data-district-target="fr_district_id" required>
                                        <option value="">Select State</option>
                                        @foreach($states as $st)
                                            <option value="{{ $st->id }}" {{ old('state_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">District <span class="text-danger">*</span></label>
                                    <select name="district_id" id="fr_district_id" class="form-select" required>
                                        <option value="">Select District</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">No. of Computers in Lab <span class="text-danger">*</span></label>
                                    <input type="number" min="3" name="computers_count" class="form-control" placeholder="Minimum 3 systems" value="{{ old('computers_count', 5) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Total Carpet Area (Sq. Ft.) <span class="text-danger">*</span></label>
                                    <input type="number" min="100" name="carpet_area_sqft" class="form-control" placeholder="e.g. 400" value="{{ old('carpet_area_sqft', 350) }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Full Physical Address of Centre <span class="text-danger">*</span></label>
                                    <textarea name="address" class="form-control" rows="2" placeholder="Building name, landmark, road, pincode" required>{{ old('address') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Additional Remarks / Prior Experience</label>
                                    <textarea name="remarks" class="form-control" rows="2" placeholder="Mention if you already run an existing centre or institute...">{{ old('remarks') }}</textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-dce-accent w-100 py-3 fw-bold fs-5">
                                <i class="fa-solid fa-paper-plane me-2"></i> Submit Franchise Application
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('layouts.app')

@section('title', ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER') . ' - Building Digital Skills for the Future')
@section('meta_description', 'TECH ERA COMPUTER, Sasbahana, Sheikhpura, Bihar - Leading Computer Training Institute offering ADCA, DCA, Typing, Tally Prime with GST, and Programming courses.')

@section('content')
    <!-- 1. Hero Section & Quick Admission Enquiry Form -->
    <section class="dce-hero">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center g-5">
                <!-- Left Hero Pitch -->
                <div class="col-lg-7">
                    <div class="dce-hero-badge">
                        <i class="fa-solid fa-microchip text-warning"></i>
                        <span>{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</span>
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" 
                             alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" 
                             class="img-fluid bg-white p-2 rounded-3 shadow-lg" 
                             style="max-height: 80px; width: auto; object-fit: contain;">
                        <div>
                            <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill text-uppercase">Admissions Open 2026</span>
                            <div class="text-white-50 small mt-1"><i class="fa-solid fa-location-dot text-info me-1"></i> Sasbahana, Sheikhpura, Bihar</div>
                        </div>
                    </div>

                    <h1 class="dce-hero-title">
                        Empowering Youth with <span>Practical Computer Education</span> & Career Skills
                    </h1>

                    <p class="dce-hero-subtitle">
                        Welcome to <strong>{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</strong>. We deliver hands-on computer training in Software, Accounting, Bilingual Typing, and Programming designed to prepare you for government exams and IT careers.
                    </p>

                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <a href="{{ route('admission.apply') }}" class="btn btn-dce-accent btn-lg shadow-sm">
                            <i class="fa-solid fa-file-signature me-2"></i> Apply Online Admission
                        </a>
                        <a href="{{ route('courses.index') }}" class="btn btn-dce-primary btn-lg shadow-sm">
                            <i class="fa-solid fa-graduation-cap me-2"></i> Explore Courses
                        </a>
                        <a href="{{ route('verify.certificate') }}" class="btn btn-outline-light btn-lg shadow-sm">
                            <i class="fa-solid fa-qrcode me-2"></i> Verify Certificate
                        </a>
                    </div>

                    <div class="row text-white-50 g-3 pt-2">
                        <div class="col-sm-4 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-desktop text-info fs-4"></i>
                            <span class="small text-white">100% Practical Computer Labs</span>
                        </div>
                        <div class="col-sm-4 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-keyboard text-warning fs-4"></i>
                            <span class="small text-white">English & Hindi Typing Lab</span>
                        </div>
                        <div class="col-sm-4 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-award text-success fs-4"></i>
                            <span class="small text-white">Verified Marksheet & Certificate</span>
                        </div>
                    </div>
                </div>

                <!-- Right Quick Admission Enquiry Form -->
                <div class="col-lg-5">
                    <div class="dce-form-card shadow-lg bg-white text-dark p-4 rounded-4 border-0">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <div>
                                <h4 class="fw-bold text-primary mb-0"><i class="fa-solid fa-user-plus text-warning me-2"></i>Admission Enquiry</h4>
                                <small class="text-muted">Get course details, fee schedule & batch timings</small>
                            </div>
                            <span class="badge bg-success-subtle text-success px-2 py-1 small fw-bold">Free Counseling</span>
                        </div>

                        <form action="{{ route('enquiry.admission.submit') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Enter student full name" value="{{ old('name') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Mobile Number <span class="text-danger">*</span></label>
                                <input type="tel" name="mobile" class="form-control font-monospace" placeholder="10-digit mobile number" value="{{ old('mobile') }}" required>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-semibold">Course Interested</label>
                                    <select name="course_id" class="form-select" required>
                                        <option value="">Select Course</option>
                                        @foreach($allCourses as $c)
                                            <option value="{{ $c->id }}">{{ $c->course_code }} - {{ $c->course_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-semibold">State <span class="text-danger">*</span></label>
                                    <select name="state_id" class="form-select dce-state-select" data-district-target="home_enq_district" required>
                                        <option value="">Select State</option>
                                        @foreach($states as $st)
                                            <option value="{{ $st->id }}" {{ $st->name === 'Bihar' ? 'selected' : '' }}>{{ $st->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">District <span class="text-danger">*</span></label>
                                <select name="district_id" id="home_enq_district" class="form-select" required>
                                    <option value="1">Sheikhpura</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Message / Enquiry Details</label>
                                <input type="text" name="subject" class="form-control" value="Interested in admission at TECH ERA COMPUTER, Sheikhpura." required>
                            </div>
                            <button type="submit" class="btn btn-dce-accent w-100 py-2 fs-6 fw-bold shadow-sm">
                                <i class="fa-solid fa-paper-plane me-2"></i> Submit Admission Enquiry
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Institute Highlights & Statistics -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-sm-6">
                    <div class="dce-stat-card bg-white p-4 rounded-4 shadow-sm text-center h-100 border-0">
                        <div class="dce-stat-icon text-primary fs-1 mb-2">
                            <i class="fa-solid fa-laptop-code"></i>
                        </div>
                        <div class="dce-stat-number dce-counter fw-bold text-dark fs-2 mb-1" data-target="2500">2,500+</div>
                        <div class="dce-stat-title text-muted fw-semibold">Students Enrolled & Trained</div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="dce-stat-card bg-white p-4 rounded-4 shadow-sm text-center h-100 border-0">
                        <div class="dce-stat-icon text-warning fs-1 mb-2">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                        <div class="dce-stat-number dce-counter fw-bold text-dark fs-2 mb-1" data-target="{{ $allCourses->count() }}">{{ $allCourses->count() }}</div>
                        <div class="dce-stat-title text-muted fw-semibold">Job-Oriented Courses</div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="dce-stat-card bg-white p-4 rounded-4 shadow-sm text-center h-100 border-0">
                        <div class="dce-stat-icon text-info fs-1 mb-2">
                            <i class="fa-solid fa-desktop"></i>
                        </div>
                        <div class="dce-stat-number dce-counter fw-bold text-dark fs-2 mb-1" data-target="50">50+</div>
                        <div class="dce-stat-title text-muted fw-semibold">Modern Lab Workstations</div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="dce-stat-card bg-white p-4 rounded-4 shadow-sm text-center h-100 border-0">
                        <div class="dce-stat-icon text-success fs-1 mb-2">
                            <i class="fa-solid fa-certificate"></i>
                        </div>
                        <div class="dce-stat-number dce-counter fw-bold text-dark fs-2 mb-1" data-target="100">100%</div>
                        <div class="dce-stat-title text-muted fw-semibold">Verified Online Certificates</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Why Choose TECH ERA COMPUTER -->
    <section class="py-5">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill text-uppercase mb-2">Why Study Here</span>
                <h2 class="fw-extrabold display-6">Why Choose TECH ERA COMPUTER?</h2>
                <p class="text-muted">Equipping students with modern technical skills for competitive exams, accounting roles, and high-growth IT careers.</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="dce-facility-card h-100 p-4 bg-white rounded-4 shadow-sm border border-light">
                        <div class="dce-facility-icon text-primary fs-2 mb-3">
                            <i class="fa-solid fa-desktop"></i>
                        </div>
                        <h4 class="fw-bold mb-2">1:1 Practical Computer Ratio</h4>
                        <p class="text-muted mb-0">Every student gets dedicated computer terminal time with high-speed internet, real software suites, and personalized faculty assistance.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="dce-facility-card h-100 p-4 bg-white rounded-4 shadow-sm border border-light">
                        <div class="dce-facility-icon text-success fs-2 mb-3">
                            <i class="fa-solid fa-qrcode"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Tamper-Proof Online QR Verification</h4>
                        <p class="text-muted mb-0">Instant 24x7 online verification of student diplomas, certificates, and marksheets through mobile QR code scans and roll number searches.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="dce-facility-card h-100 p-4 bg-white rounded-4 shadow-sm border border-light">
                        <div class="dce-facility-icon text-warning fs-2 mb-3">
                            <i class="fa-solid fa-keyboard"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Bilingual Typing Mastery</h4>
                        <p class="text-muted mb-0">Dedicated typing lab for government exam preparation: Hindi (Mangal Inscript, KrutiDev) and English typing software with accuracy and speed analytics.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="dce-facility-card h-100 p-4 bg-white rounded-4 shadow-sm border border-light">
                        <div class="dce-facility-icon text-info fs-2 mb-3">
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Tally Prime & GST Accounting</h4>
                        <p class="text-muted mb-0">Comprehensive computerized accounting covering purchase, sales, GST returns, e-way bills, TDS, payroll, and final financial reports.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="dce-facility-card h-100 p-4 bg-white rounded-4 shadow-sm border border-light">
                        <div class="dce-facility-icon text-danger fs-2 mb-3">
                            <i class="fa-solid fa-code"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Programming & Web Skills</h4>
                        <p class="text-muted mb-0">Practical coding modules in Python, HTML/CSS, JavaScript, and Office automation to prepare students for tech industry roles.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="dce-facility-card h-100 p-4 bg-white rounded-4 shadow-sm border border-light">
                        <div class="dce-facility-icon text-primary fs-2 mb-3">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Affordable Monthly Installments</h4>
                        <p class="text-muted mb-0">Transparent course fee packages with zero hidden costs, easy monthly installment options, and computerized payment receipts.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Featured Job-Oriented Courses -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
                <div>
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill text-uppercase mb-2">Academic Programs</span>
                    <h2 class="fw-extrabold display-6 mb-0">Featured Computer Courses</h2>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-primary fw-bold rounded-pill px-4">
                        View All {{ $allCourses->count() }} Courses <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <div class="row g-4">
                @foreach($featuredCourses as $course)
                    <div class="col-lg-4 col-md-6">
                        <div class="dce-course-card bg-white rounded-4 shadow-sm border-0 overflow-hidden h-100 d-flex flex-column">
                            <div class="dce-course-header p-4 border-bottom bg-white">
                                <span class="dce-course-badge badge bg-primary-subtle text-primary fw-bold px-2 py-1 mb-2">{{ $course->category->name ?? 'Diploma' }}</span>
                                <div class="dce-course-code font-monospace text-muted small">{{ $course->course_code }}</div>
                                <h4 class="dce-course-title fw-bold mb-0 mt-1">
                                    <a href="{{ route('courses.show', $course->slug) }}" class="text-decoration-none text-dark">
                                        {{ $course->course_name }}
                                    </a>
                                </h4>
                            </div>
                            <div class="dce-course-body p-4 d-flex flex-column justify-content-between flex-grow-1">
                                <div>
                                    <div class="dce-course-meta d-flex justify-content-between text-muted small mb-3">
                                        <span><i class="fa-regular fa-clock text-primary me-1"></i> {{ $course->duration }}</span>
                                        <span><i class="fa-solid fa-user-graduate text-success me-1"></i> {{ $course->eligibility }}</span>
                                    </div>
                                    <p class="text-muted small mb-3">
                                        {{ Str::limit(strip_tags($course->syllabus), 130) }}
                                    </p>
                                </div>
                                <div>
                                    <hr class="my-3 opacity-25">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="text-muted small d-block">Course Fee</span>
                                            <span class="fw-bold text-primary fs-5 font-monospace">
                                                ₹{{ number_format($course->default_course_fee ?? $course->course_fee ?? 0, 2) }}
                                            </span>
                                        </div>
                                        <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-sm btn-dce-primary rounded-pill px-3">
                                            View Details <i class="fa-solid fa-chevron-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 5. Facilities & Smart Computer Labs -->
    <section class="py-5">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="badge bg-info-subtle text-info fw-bold px-3 py-2 rounded-pill text-uppercase mb-2">Campus Infrastructure</span>
                <h2 class="fw-extrabold display-6">Smart Lab Facilities & Learning Environment</h2>
                <p class="text-muted">Designed to deliver an engaging, productive, and practical technical education experience.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card p-4 rounded-4 shadow-sm border-0 text-center h-100 bg-light">
                        <div class="text-primary fs-1 mb-3"><i class="fa-solid fa-network-wired"></i></div>
                        <h5 class="fw-bold">Connected Lab Network</h5>
                        <p class="text-muted small mb-0">High-speed broadband network supporting software installations, project work, and online assessments.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card p-4 rounded-4 shadow-sm border-0 text-center h-100 bg-light">
                        <div class="text-warning fs-1 mb-3"><i class="fa-solid fa-bolt"></i></div>
                        <h5 class="fw-bold">Continuous Power Backup</h5>
                        <p class="text-muted small mb-0">Full inverter and generator power backup ensuring uninterrupted practical lab sessions and exam typing practice.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card p-4 rounded-4 shadow-sm border-0 text-center h-100 bg-light">
                        <div class="text-success fs-1 mb-3"><i class="fa-solid fa-chalkboard-user"></i></div>
                        <h5 class="fw-bold">Experienced Instructors</h5>
                        <p class="text-muted small mb-0">Friendly, qualified instructors who guide students patiently from foundational typing to advanced coding.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card p-4 rounded-4 shadow-sm border-0 text-center h-100 bg-light">
                        <div class="text-danger fs-1 mb-3"><i class="fa-solid fa-file-shield"></i></div>
                        <h5 class="fw-bold">Digital Student Portal</h5>
                        <p class="text-muted small mb-0">Online access to study materials, batch timetables, attendance analytics, marks statements, and computerized fee receipts.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. About Institute & Location Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill text-uppercase mb-2">About The Institute</span>
                    <h2 class="fw-extrabold display-6 mb-3">Welcome to TECH ERA COMPUTER</h2>
                    <p class="text-muted lead">
                        <strong>TECH ERA COMPUTER</strong> is an advanced technical education institute situated in <strong>Sasbahana, Sheikhpura, Bihar - 801105</strong>.
                    </p>
                    <p class="text-muted">
                        Our mission is to empower youth and students across Sheikhpura and neighboring areas with high-demand digital skills, software literacy, accounting competence, and certified vocational training. From basic computer concepts to advanced programming, accounting with GST, and rapid bilingual typing, we guide each student toward real-world self-reliance and career success.
                    </p>
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="{{ route('about') }}" class="btn btn-dce-primary rounded-pill px-4 shadow-sm">
                            <i class="fa-solid fa-circle-info me-1"></i> Learn More About Us
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                            <i class="fa-solid fa-location-dot me-1"></i> Visit Campus
                        </a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" 
                                 alt="Tech Era Logo" 
                                 class="img-fluid" 
                                 style="max-height: 58px; width: auto;">
                            <div>
                                <h4 class="fw-bold text-dark mb-0">TECH ERA COMPUTER</h4>
                                <span class="text-muted small">Campus & Admissions Desk</span>
                            </div>
                        </div>

                        <div class="vstack gap-3 text-secondary small">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-primary-subtle text-primary p-2 rounded-circle">
                                    <i class="fa-solid fa-location-dot fs-5"></i>
                                </div>
                                <div>
                                    <strong class="text-dark d-block">Campus Address:</strong>
                                    {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-success-subtle text-success p-2 rounded-circle">
                                    <i class="fa-solid fa-phone fs-5"></i>
                                </div>
                                <div>
                                    <strong class="text-dark d-block">Helpline & WhatsApp:</strong>
                                    <a href="tel:{{ $instituteSettings['contact_phone'] ?? '9162384660' }}" class="text-decoration-none text-dark fw-bold">
                                        +91 {{ $instituteSettings['contact_phone'] ?? '9162384660' }}
                                    </a>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-warning-subtle text-warning p-2 rounded-circle">
                                    <i class="fa-solid fa-clock fs-5"></i>
                                </div>
                                <div>
                                    <strong class="text-dark d-block">Working Hours:</strong>
                                    Monday – Saturday: 08:00 AM – 07:00 PM
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex gap-2">
                            <a href="https://api.whatsapp.com/send?phone=91{{ $instituteSettings['contact_phone'] ?? '9162384660' }}&text=Hi%20Tech%20Era%20Computer" target="_blank" class="btn btn-success rounded-pill px-4 flex-grow-1 shadow-sm">
                                <i class="fa-brands fa-whatsapp me-1"></i> Chat on WhatsApp
                            </a>
                            <a href="tel:{{ $instituteSettings['contact_phone'] ?? '9162384660' }}" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
                                <i class="fa-solid fa-phone"></i> Call
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('layouts.app')

@section('title', ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER') . ' - Building Digital Skills for the Future')
@section('meta_description', 'TECH ERA COMPUTER, Sasbahana, Sheikhpura, Bihar - Leading Computer Training Institute offering ADCA, DCA, Typing, Tally Prime with GST, and Programming courses.')

@section('content')
    <!-- 1. Hero Section & Quick Admission Enquiry Form -->
    <section class="dce-hero">
        <div class="dce-hero-grid-bg"></div>
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center g-4 g-lg-5">
                <!-- Left Hero Pitch -->
                <div class="col-lg-7">
                    <!-- Top Pill Badge with Live Pulse -->
                    <div class="dce-hero-badge mb-3">
                        <span class="dce-pulse-dot me-1"></span>
                        <span class="fw-bold text-white font-monospace" style="font-size: 0.75rem; letter-spacing: 0.5px;">ADMISSIONS OPEN 2026-27</span>
                        <span class="opacity-40">|</span>
                        <span class="text-white-50 small"><i class="fa-solid fa-location-dot text-warning me-1"></i>Sasbahana, Sheikhpura</span>
                    </div>

                    <!-- Mobile Institute Brand Header -->
                    <div class="d-flex align-items-center gap-2.5 mb-3 d-md-none">
                        <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" 
                             alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" 
                             class="bg-white p-1 rounded-2 shadow-sm" 
                             style="max-height: 40px; width: auto; object-fit: contain;">
                        <div>
                            <div class="fw-extrabold text-white lh-1 small">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                            <div class="text-info" style="font-size: 0.65rem;"><i class="fa-solid fa-circle-check text-success me-0.5"></i> ISO Certified Computer Training</div>
                        </div>
                    </div>

                    <!-- Institute Header Card (Shown on Tablet & Desktop) -->
                    <div class="d-none d-md-flex align-items-center gap-3 mb-3">
                        <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" 
                             alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" 
                             class="img-fluid bg-white p-2 rounded-3 shadow-lg" 
                             style="max-height: 72px; width: auto; object-fit: contain;">
                        <div>
                            <div class="fw-bold text-white fs-5 lh-1">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                            <div class="text-white-50 small mt-1"><i class="fa-solid fa-award text-warning me-1"></i> {{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</div>
                        </div>
                    </div>

                    <h1 class="dce-hero-title mb-3">
                        Empowering Youth With <span class="dce-hero-gradient-text">Practical Tech Skills</span> & Career Excellence
                    </h1>

                    <p class="dce-hero-subtitle mb-4">
                        Official institute for professional computer education in <strong>Sheikhpura, Bihar</strong>. High-speed 1:1 practical labs, bilingual typing, computerized Tally Prime accounting, and government-recognized certifications.
                    </p>

                    <!-- Smart Responsive Buttons Layout (Single Primary + 2-Col Secondary on Mobile) -->
                    <div class="dce-hero-cta-wrapper mb-4">
                        <!-- Primary CTA Button with Glow -->
                        <a href="{{ route('admission.apply') }}" class="btn btn-dce-accent btn-lg w-100 shadow-sm d-flex align-items-center justify-content-center gap-2 mb-2.5 py-2.5 rounded-pill">
                            <i class="fa-solid fa-bolt text-warning"></i>
                            <span class="fw-bold">Apply Online for Admission</span>
                            <i class="fa-solid fa-arrow-right ms-1 small"></i>
                        </a>

                        <!-- Secondary CTAs in 2-Column Equal Grid on Mobile -->
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('courses.index') }}" class="btn btn-dce-primary btn-lg w-100 shadow-sm d-flex align-items-center justify-content-center gap-1.5 py-2 px-2 rounded-pill">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                    <span>All Courses</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('verify.certificate') }}" class="btn btn-dce-outline btn-lg w-100 shadow-sm d-flex align-items-center justify-content-center gap-1.5 py-2 px-2 rounded-pill">
                                    <i class="fa-solid fa-qrcode"></i>
                                    <span>Verify Cert</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Trust Micro-Badges Strip (Glassmorphism) -->
                    <div class="dce-hero-trust-bar p-3 rounded-4 mb-4">
                        <div class="row g-2.5 align-items-center">
                            <div class="col-6 col-sm-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="dce-hero-icon-pill bg-primary-subtle text-primary"><i class="fa-solid fa-laptop-code"></i></div>
                                    <div>
                                        <div class="text-white small fw-bold lh-1">1:1 Practical Ratio</div>
                                        <div class="text-white-50" style="font-size: 0.68rem;">Dedicated workstation</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="dce-hero-icon-pill bg-warning-subtle text-warning"><i class="fa-solid fa-keyboard"></i></div>
                                    <div>
                                        <div class="text-white small fw-bold lh-1">Bilingual Typing</div>
                                        <div class="text-white-50" style="font-size: 0.68rem;">Hindi & English lab</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="d-flex align-items-center justify-content-start gap-2 pt-2 pt-sm-0 border-top border-top-sm-0 border-white-10">
                                    <div class="dce-hero-icon-pill bg-success-subtle text-success"><i class="fa-solid fa-shield-halved"></i></div>
                                    <div>
                                        <div class="text-white small fw-bold lh-1">Verified Diplomas</div>
                                        <div class="text-white-50" style="font-size: 0.68rem;">QR code verifiable</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Quick Admission Enquiry Form -->
                <div class="col-lg-5">
                    <div class="dce-hero-form-card">
                        <div class="dce-hero-form-header d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="fw-bold mb-0 text-white"><i class="fa-solid fa-graduation-cap text-warning me-2"></i>Quick Admission Enquiry</h5>
                                <small class="text-white-50" style="font-size: 0.75rem;">Get fees, syllabus & batch timing details</small>
                            </div>
                            <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill small fw-bold">Instant Callback</span>
                        </div>
                        <div class="p-3 p-sm-4 bg-white">
                            <form action="{{ route('enquiry.admission.submit') }}" method="POST">
                                @csrf
                                <div class="mb-2.5">
                                    <label class="form-label small fw-bold mb-1 text-dark">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter student full name" value="{{ old('name') }}" required>
                                </div>
                                <div class="mb-2.5">
                                    <label class="form-label small fw-bold mb-1 text-dark">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="mobile" class="form-control font-monospace" placeholder="10-digit mobile number" value="{{ old('mobile') }}" required>
                                </div>
                                <div class="row g-2 mb-2.5">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold mb-1 text-dark">Course <span class="text-danger">*</span></label>
                                        <select name="course_id" class="form-select" required>
                                            <option value="">Select Course</option>
                                            @foreach($allCourses as $c)
                                                <option value="{{ $c->id }}">{{ $c->course_code }} - {{ $c->course_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold mb-1 text-dark">State <span class="text-danger">*</span></label>
                                        <select name="state_id" class="form-select dce-state-select" data-district-target="home_enq_district" required>
                                            <option value="">Select State</option>
                                            @foreach($states as $st)
                                                <option value="{{ $st->id }}" {{ $st->name === 'Bihar' ? 'selected' : '' }}>{{ $st->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-2.5">
                                    <label class="form-label small fw-bold mb-1 text-dark">District <span class="text-danger">*</span></label>
                                    <select name="district_id" id="home_enq_district" class="form-select" required>
                                        <option value="1">Sheikhpura</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold mb-1 text-dark">Message / Remarks</label>
                                    <input type="text" name="subject" class="form-control" value="Interested in admission at TECH ERA COMPUTER, Sheikhpura." required>
                                </div>
                                <button type="submit" class="btn btn-dce-accent w-100 py-2.5 fs-6 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 rounded-pill">
                                    <i class="fa-solid fa-paper-plane"></i>
                                    <span>Submit Admission Enquiry</span>
                                </button>
                                <div class="text-center mt-2">
                                    <small class="text-muted" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-lock text-success me-1"></i> 100% Privacy Protected & Confidential
                                    </small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Institute Highlights & Statistics -->
    <section class="py-4 py-md-5 bg-light">
        <div class="container">
            <div class="row g-2.5 g-sm-3 g-lg-4">
                <div class="col-6 col-lg-3">
                    <div class="dce-modern-stat-card h-100">
                        <div class="dce-stat-badge bg-primary-subtle text-primary">
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>
                        <div class="dce-stat-val dce-counter" data-target="2500">2,500+</div>
                        <div class="dce-stat-lbl">Students Trained</div>
                        <div class="dce-stat-sub d-none d-sm-block">Job & Career Ready</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dce-modern-stat-card h-100">
                        <div class="dce-stat-badge bg-warning-subtle text-warning-emphasis">
                            <i class="fa-solid fa-book-bookmark"></i>
                        </div>
                        <div class="dce-stat-val dce-counter" data-target="{{ $allCourses->count() }}">{{ $allCourses->count() }}</div>
                        <div class="dce-stat-lbl">Certified Courses</div>
                        <div class="dce-stat-sub d-none d-sm-block">Basic to Advanced</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dce-modern-stat-card h-100">
                        <div class="dce-stat-badge bg-info-subtle text-info">
                            <i class="fa-solid fa-desktop"></i>
                        </div>
                        <div class="dce-stat-val dce-counter" data-target="50">50+</div>
                        <div class="dce-stat-lbl">Modern PCs</div>
                        <div class="dce-stat-sub d-none d-sm-block">1:1 Practical Ratio</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dce-modern-stat-card h-100">
                        <div class="dce-stat-badge bg-success-subtle text-success">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div class="dce-stat-val dce-counter" data-target="100">100%</div>
                        <div class="dce-stat-lbl">Verified Diplomas</div>
                        <div class="dce-stat-sub d-none d-sm-block">QR Code Enabled</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Why Choose TECH ERA COMPUTER -->
    <section class="py-4 py-md-5">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-4 mb-md-5">
                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-1.5 rounded-pill text-uppercase mb-2" style="font-size: 0.75rem;">Why Study Here</span>
                <h2 class="fw-extrabold display-6 fs-3 fs-md-2">Why Choose TECH ERA COMPUTER?</h2>
                <p class="text-muted small">Equipping students with modern technical skills for competitive exams, accounting roles, and high-growth IT careers.</p>
            </div>

            <div class="row g-2.5 g-sm-3 g-lg-4">
                <div class="col-6 col-lg-4">
                    <div class="dce-modern-feature-card h-100">
                        <div class="dce-feature-badge" style="background: #eff6ff; color: #2563eb;">
                            <i class="fa-solid fa-desktop"></i>
                        </div>
                        <h6 class="dce-feature-title">1:1 Practical Ratio</h6>
                        <p class="dce-feature-desc">Dedicated computer terminal time with high-speed internet and personal mentor guidance.</p>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="dce-modern-feature-card h-100">
                        <div class="dce-feature-badge" style="background: #ecfdf5; color: #059669;">
                            <i class="fa-solid fa-qrcode"></i>
                        </div>
                        <h6 class="dce-feature-title">QR Code Verification</h6>
                        <p class="dce-feature-desc">Instant 24x7 online verification of student diplomas and marksheets via QR scan.</p>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="dce-modern-feature-card h-100">
                        <div class="dce-feature-badge" style="background: #fffbeb; color: #d97706;">
                            <i class="fa-solid fa-keyboard"></i>
                        </div>
                        <h6 class="dce-feature-title">Bilingual Typing Lab</h6>
                        <p class="dce-feature-desc">Govt exam preparation: Hindi (Mangal, KrutiDev) and English typing software.</p>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="dce-modern-feature-card h-100">
                        <div class="dce-feature-badge" style="background: #eef2ff; color: #4f46e5;">
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                        <h6 class="dce-feature-title">Tally Prime & GST</h6>
                        <p class="dce-feature-desc">Comprehensive computerized accounting: GST returns, e-way bills, TDS, and ledger.</p>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="dce-modern-feature-card h-100">
                        <div class="dce-feature-badge" style="background: #faf5ff; color: #9333ea;">
                            <i class="fa-solid fa-code"></i>
                        </div>
                        <h6 class="dce-feature-title">Coding & Web Skills</h6>
                        <p class="dce-feature-desc">Practical coding in Python, HTML/CSS, JavaScript, and Office automation for IT roles.</p>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="dce-modern-feature-card h-100">
                        <div class="dce-feature-badge" style="background: #ecfeff; color: #0891b2;">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                        </div>
                        <h6 class="dce-feature-title">Easy Installments</h6>
                        <p class="dce-feature-desc">Transparent fee packages with zero hidden fees, monthly installment options, and receipts.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Featured Job-Oriented Courses -->
    <section class="py-4 py-md-5 bg-light">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-3 mb-md-4">
                <div>
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-1.5 rounded-pill text-uppercase mb-2" style="font-size: 0.75rem;">Academic Programs</span>
                    <h2 class="fw-extrabold display-6 fs-3 fs-md-2 mb-0">Featured Computer Courses</h2>
                </div>
                <div class="mt-2 mt-md-0">
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-primary btn-sm fw-bold rounded-pill px-3">
                        View All {{ $allCourses->count() }} Courses <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <div class="row g-2.5 g-sm-3 g-lg-4">
                @foreach($featuredCourses as $course)
                    <div class="col-6 col-lg-4">
                        <div class="dce-modern-course-card h-100">
                            <div class="dce-course-top">
                                <span class="dce-code-pill">{{ $course->course_code }}</span>
                                <span class="dce-cat-pill">{{ $course->category->name ?? 'Diploma' }}</span>
                            </div>
                            <div class="dce-course-content">
                                <h6 class="dce-course-name">
                                    <a href="{{ route('courses.show', $course->slug) }}">
                                        {{ $course->course_name }}
                                    </a>
                                </h6>
                                <div class="dce-course-features">
                                    <span class="dce-feat-tag"><i class="fa-regular fa-clock text-primary me-1"></i>{{ $course->duration }}</span>
                                    <span class="dce-feat-tag"><i class="fa-solid fa-certificate text-success me-1"></i>ISO Cert</span>
                                </div>
                            </div>
                            <div class="dce-course-footer">
                                <div class="dce-price-box">
                                    <span class="dce-price-lbl">Fee</span>
                                    <span class="dce-price-val">₹{{ number_format($course->default_course_fee ?? $course->course_fee ?? 0, 0) }}</span>
                                </div>
                                <a href="{{ route('courses.show', $course->slug) }}" class="dce-explore-btn">
                                    <span>Details</span>
                                    <i class="fa-solid fa-chevron-right" style="font-size: 0.6rem;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 5. Facilities & Smart Computer Labs -->
    <section class="py-4 py-md-5">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-4 mb-md-5">
                <span class="badge bg-info-subtle text-info fw-bold px-3 py-1.5 rounded-pill text-uppercase mb-2" style="font-size: 0.75rem;">Campus Infrastructure</span>
                <h2 class="fw-extrabold display-6 fs-3 fs-md-2">Smart Lab Facilities & Environment</h2>
                <p class="text-muted small">Engaging, productive, and practical technical education setup.</p>
            </div>

            <div class="row g-2.5 g-sm-3 g-lg-4">
                <div class="col-6 col-lg-3">
                    <div class="dce-modern-lab-card h-100">
                        <div class="dce-lab-badge bg-primary-subtle text-primary">
                            <i class="fa-solid fa-network-wired"></i>
                        </div>
                        <h6 class="dce-lab-title">Connected Network</h6>
                        <p class="dce-lab-desc">High-speed gigabit Wi-Fi & LAN network for online lab exams & learning.</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dce-modern-lab-card h-100">
                        <div class="dce-lab-badge bg-warning-subtle text-warning">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <h6 class="dce-lab-title">Power Backup</h6>
                        <p class="dce-lab-desc">Continuous inverter & generator power for uninterrupted practical sessions.</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dce-modern-lab-card h-100">
                        <div class="dce-lab-badge bg-success-subtle text-success">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                        <h6 class="dce-lab-title">Expert Trainers</h6>
                        <p class="dce-lab-desc">Qualified instructors guiding from foundational typing to live coding.</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dce-modern-lab-card h-100">
                        <div class="dce-lab-badge bg-danger-subtle text-danger">
                            <i class="fa-solid fa-file-shield"></i>
                        </div>
                        <h6 class="dce-lab-title">Student Portal</h6>
                        <p class="dce-lab-desc">Online access to study notes, timetables, results, and fee receipts.</p>
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

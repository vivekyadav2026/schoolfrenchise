@extends('layouts.app')

@section('title', 'Our Facilities | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center text-md-start">
            <h1>Our Modern Learning Facilities</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Facilities</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill uppercase mb-2">Campus Infrastructure</span>
                <h2 class="fw-extrabold display-6">World-Class Computer Lab & Learning Setup</h2>
                <p class="text-muted">Designed to deliver an optimal hands-on practical computing environment for all students.</p>
            </div>

            <div class="row g-2.5 g-sm-3 g-lg-4 mb-4 mb-md-5">
                <div class="col-6 col-lg-4">
                    <div class="dce-modern-feature-card h-100">
                        <div class="dce-feature-badge" style="background: #eff6ff; color: #2563eb;">
                            <i class="fa-solid fa-computer"></i>
                        </div>
                        <h6 class="dce-feature-title">1:1 Student Ratio</h6>
                        <p class="dce-feature-desc">Dedicated workstation during lab practical hours, ensuring no shared systems.</p>
                    </div>
                </div>

                <div class="col-6 col-lg-4">
                    <div class="dce-modern-feature-card h-100">
                        <div class="dce-feature-badge" style="background: #ecfeff; color: #0891b2;">
                            <i class="fa-solid fa-wifi"></i>
                        </div>
                        <h6 class="dce-feature-title">High-Speed Broadband</h6>
                        <p class="dce-feature-desc">Dedicated internet across all labs for web design, cloud accounting, and testing.</p>
                    </div>
                </div>

                <div class="col-6 col-lg-4">
                    <div class="dce-modern-feature-card h-100">
                        <div class="dce-feature-badge" style="background: #fffbeb; color: #d97706;">
                            <i class="fa-solid fa-battery-three-quarters"></i>
                        </div>
                        <h6 class="dce-feature-title">Power Backup & UPS</h6>
                        <p class="dce-feature-desc">Continuous inverter power ensures uninterrupted learning during power outages.</p>
                    </div>
                </div>

                <div class="col-6 col-lg-4">
                    <div class="dce-modern-feature-card h-100">
                        <div class="dce-feature-badge" style="background: #ecfdf5; color: #059669;">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                        <h6 class="dce-feature-title">Certified Trainers</h6>
                        <p class="dce-feature-desc">Instructors undergo continuous updates on Tally Prime, GST, and coding.</p>
                    </div>
                </div>

                <div class="col-6 col-lg-4">
                    <div class="dce-modern-feature-card h-100">
                        <div class="dce-feature-badge" style="background: #eef2ff; color: #4f46e5;">
                            <i class="fa-solid fa-keyboard"></i>
                        </div>
                        <h6 class="dce-feature-title">Speed Typing Labs</h6>
                        <p class="dce-feature-desc">Automated typing software for English and Hindi (Remington & Inscript).</p>
                    </div>
                </div>

                <div class="col-6 col-lg-4">
                    <div class="dce-modern-feature-card h-100">
                        <div class="dce-feature-badge" style="background: #f0fdf4; color: #16a34a;">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <h6 class="dce-feature-title">WhatsApp Student Desk</h6>
                        <p class="dce-feature-desc">Direct WhatsApp delivery of admit cards, fee receipts, marks, and certificates.</p>
                    </div>
                </div>
            </div>

            <!-- Call to action -->
            <div class="p-5 rounded-4 bg-primary text-white text-center shadow-lg">
                <h3 class="fw-bold mb-2">Ready to Upgrade Your Digital Skills at {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}?</h3>
                <p class="lead mb-4 text-white-50">Join our modern practical batches at {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }} today.</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('enquiry.admission') }}" class="btn btn-warning btn-lg fw-bold text-dark px-4">
                        Enquire For Admission
                    </a>
                    <a href="tel:{{ $instituteSettings['contact_phone'] ?? '9162384660' }}" class="btn btn-outline-light btn-lg fw-bold px-4">
                        <i class="fa-solid fa-phone me-1"></i> Call: {{ $instituteSettings['contact_phone'] ?? '9162384660' }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

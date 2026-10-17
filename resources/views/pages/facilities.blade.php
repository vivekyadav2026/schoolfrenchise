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

            <div class="row g-4 mb-5">
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <div class="dce-facility-icon">
                            <i class="fa-solid fa-computer"></i>
                        </div>
                        <h4 class="fw-bold mb-2">1:1 Student to Computer Ratio</h4>
                        <p class="text-muted">Every enrolled student is allocated a dedicated workstation during lab practical hours, ensuring no shared systems or compromised practical practice.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <div class="dce-facility-icon">
                            <i class="fa-solid fa-wifi"></i>
                        </div>
                        <h4 class="fw-bold mb-2">High-Speed Optical Broadband</h4>
                        <p class="text-muted">High-bandwidth dedicated internet connections across all centres enabling seamless web designing, cloud accounting, software downloads, and online testing.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <div class="dce-facility-icon">
                            <i class="fa-solid fa-battery-three-quarters"></i>
                        </div>
                        <h4 class="fw-bold mb-2">100% Power Backup & UPS</h4>
                        <p class="text-muted">Continuous power backup systems and inverters ensure uninterrupted learning during power outages or load shedding.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <div class="dce-facility-icon">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Certified Faculty & Trainers</h4>
                        <p class="text-muted">Instructors undergo mandatory periodic training on Tally Prime, GST updates, advanced Excel formulas, and modern software paradigms.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <div class="dce-facility-icon">
                            <i class="fa-solid fa-keyboard"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Specialized Speed Typing Labs</h4>
                        <p class="text-muted">Equipped with specialized mechanical keyboards and automated typing tutor software for English and Hindi (Remington Gail & Inscript layouts).</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <div class="dce-facility-icon">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Instant WhatsApp Student Desk</h4>
                        <p class="text-muted">Direct WhatsApp integration that automatically delivers admit cards, fee confirmations, exam marks, and verified certificates straight to student smartphones.</p>
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

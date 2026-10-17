@extends('layouts.app')

@section('title', 'About Us | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center text-md-start">
            <h1>About {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">About Us</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-7">
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill text-uppercase mb-2">Modern IT Training & Digital Skills</span>
                    <h2 class="fw-extrabold display-6 mb-3">Empowering Students with Real-World Technology Skills</h2>
                    <p class="lead text-muted">
                        <strong>{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</strong> is dedicated to delivering industry-relevant computer education, practical technical proficiencies, and digital empowerment to youth.
                    </p>
                    <p class="text-secondary">
                        Located at {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}, our campus is equipped with modern high-speed computer labs, individual 1:1 practice stations, bilingual typing modules (Hindi & English), and comprehensive syllabi designed to prepare students for corporate careers and competitive employment.
                    </p>
                    <p class="text-secondary">
                        Every student undergoes structured practical assignments, rigorous periodical evaluations, and receives tamper-proof computerized marksheet and diploma credentials verifiable online through our dedicated portal.
                    </p>
                    <div class="row g-3 pt-3">
                        <div class="col-sm-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <h5 class="fw-bold text-primary mb-1"><i class="fa-solid fa-laptop-code me-2"></i>1:1 Practical Ratio</h5>
                                <small class="text-muted">Dedicated computer workstation for every learner</small>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <h5 class="fw-bold text-success mb-1"><i class="fa-solid fa-certificate me-2"></i>Verified Credentials</h5>
                                <small class="text-muted">QR code enabled digital certificates & marksheets</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="p-4 text-white text-center" style="background: linear-gradient(135deg, #0a192f 0%, #0284c7 100%);">
                            <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-2 mb-3 shadow-sm" style="max-height: 80px; width: auto; object-fit: contain;">
                            <h4 class="fw-bold mb-1">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h4>
                            <p class="small text-white-50 mb-0">{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</p>
                        </div>
                        <div class="card-body p-4">
                            <ul class="list-group list-group-flush small">
                                <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="fa-solid fa-map-pin text-primary me-2"></i>Campus Location:</span>
                                    <span class="fw-semibold text-end" style="max-width: 220px;">{{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}</span>
                                </li>
                                <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="fa-solid fa-phone text-success me-2"></i>Official Helpline:</span>
                                    <strong class="font-monospace text-dark">{{ $instituteSettings['contact_phone'] ?? '9162384660' }}</strong>
                                </li>
                                <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="fa-brands fa-whatsapp text-success me-2"></i>WhatsApp Support:</span>
                                    <strong class="font-monospace text-dark">{{ $instituteSettings['whatsapp_number'] ?? '9162384660' }}</strong>
                                </li>
                                <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="fa-solid fa-book text-info me-2"></i>Featured Diplomas:</span>
                                    <span class="badge bg-primary">ADCA, DCA, Tally Prime</span>
                                </li>
                                <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="fa-solid fa-keyboard text-warning me-2"></i>Typing Lab:</span>
                                    <span class="badge bg-warning text-dark">Hindi (Kruti Dev / Remington) & English</span>
                                </li>
                            </ul>
                            <div class="mt-4 text-center">
                                <a href="{{ route('admission.apply') }}" class="btn btn-dce-accent w-100 py-2 fw-bold shadow-sm">
                                    <i class="fa-solid fa-user-plus me-1"></i> Apply for Direct Admission
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

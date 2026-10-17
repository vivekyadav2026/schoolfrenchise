@extends('layouts.app')

@section('title', 'Institute Profile | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center text-md-start">
            <h1>Institute Profile</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Institute Profile</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="max-w-900 mx-auto">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-2 shadow-sm" style="max-height: 70px; width: auto; object-fit: contain;">
                        <div>
                            <h2 class="fw-extrabold text-primary mb-1">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h2>
                            <p class="text-muted mb-0">{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</p>
                        </div>
                    </div>
                    
                    <p class="lead text-muted">
                        <strong>{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</strong> is a dedicated computer training and digital skill development centre providing high-quality technical education, vocational diploma certifications, and practical job-oriented training.
                    </p>
                    <hr class="my-4">

                    <h4 class="fw-bold mb-3"><i class="fa-solid fa-briefcase text-info me-2"></i>Core Objectives & Scope of Work</h4>
                    <p class="text-secondary">
                        Our primary objective is to bridge the digital divide and provide students, job aspirants, and working professionals with modern technical competence. We offer comprehensive curricula in software applications, financial accounting with GST, office automation, database concepts, bilingual typing, and web technologies.
                    </p>
                    <p class="text-secondary">
                        Through rigorous laboratory sessions, every learner gains confidence using standard operating environments, accounting packages like Tally Prime, spreadsheets, document design, and fast typing in English and Hindi (Kruti Dev / Remington).
                    </p>

                    <h4 class="fw-bold mt-4 mb-3"><i class="fa-solid fa-star text-warning me-2"></i>Key Academic Advantages</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Feature</th>
                                    <th>Implementation Details</th>
                                    <th>Benefit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>1:1 Lab Ratio</strong></td>
                                    <td>Dedicated individual workstation during scheduled lab hours</td>
                                    <td><span class="badge bg-success">100% Hands-on Practice</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Dual Typing Lab</strong></td>
                                    <td>Bilingual English & Hindi typing training with speed benchmarks</td>
                                    <td><span class="badge bg-primary">Govt. & Private Exam Ready</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Digital Verification</strong></td>
                                    <td>QR-coded online certificate & marksheet verification portal</td>
                                    <td><span class="badge bg-info text-dark">Instant Verification</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Industry Syllabus</strong></td>
                                    <td>ADCA, DCA, Tally Prime with GST, MS Office 365, DTP & Internet</td>
                                    <td><span class="badge bg-secondary">Job-Oriented Curriculum</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 p-4 bg-light rounded-3 border">
                        <strong class="d-block text-dark fs-6 mb-2"><i class="fa-solid fa-location-dot me-2 text-danger"></i>Institute Campus & Contact Information:</strong>
                        <div class="text-secondary">
                            <strong>Address:</strong> {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}<br>
                            <strong>Mobile / Helpline:</strong> <a href="tel:{{ $instituteSettings['contact_phone'] ?? '9162384660' }}" class="text-decoration-none fw-bold text-primary">{{ $instituteSettings['contact_phone'] ?? '9162384660' }}</a><br>
                            <strong>WhatsApp:</strong> <a href="https://wa.me/91{{ $instituteSettings['whatsapp_number'] ?? '9162384660' }}" target="_blank" class="text-decoration-none fw-bold text-success">{{ $instituteSettings['whatsapp_number'] ?? '9162384660' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

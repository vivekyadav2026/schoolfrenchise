@extends('layouts.app')

@section('title', 'Mission & Vision | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center text-md-start">
            <h1>Mission & Vision</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mission & Vision</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 p-md-5 border-start border-primary border-5">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-primary-subtle text-primary p-3 fs-3">
                                <i class="fa-solid fa-bullseye"></i>
                            </div>
                            <h3 class="fw-bold text-primary mb-0">Our Mission</h3>
                        </div>
                        <p class="text-secondary lead">
                            To deliver accessible, affordable, and practical computer education that bridges the digital skill divide and prepares every student for immediate employment opportunities.
                        </p>
                        <ul class="text-secondary small mt-3">
                            <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Provide practical-first training in software applications, financial accounting, and modern digital tools.</li>
                            <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Ensure individual 1:1 workstation access during lab sessions with hands-on instructor mentorship.</li>
                            <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Maintain authentic digital student records, tamper-proof marksheets, and instant QR verification.</li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 p-md-5 border-start border-info border-5">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-info-subtle text-info p-3 fs-3">
                                <i class="fa-solid fa-eye"></i>
                            </div>
                            <h3 class="fw-bold text-info mb-0">Our Vision</h3>
                        </div>
                        <p class="text-secondary lead">
                            To be recognized as the premier IT training and digital empowerment institution in Bihar, empowering every youth with the confidence and skills to thrive in the modern digital era.
                        </p>
                        <ul class="text-secondary small mt-3">
                            <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Continuously modernize our lab facilities with high-speed workstations, ergonomic labs, and power backups.</li>
                            <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Expand bilingual typing training (English & Hindi) to help aspirants excel in competitive government examinations.</li>
                            <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Promote digital literacy across all demographic sections through community workshops and career counseling.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Academic Quality Policy -->
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-light">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <i class="fa-solid fa-award text-warning fs-2"></i>
                    <h4 class="fw-bold mb-0">Our Academic Quality & Integrity Commitment</h4>
                </div>
                <p class="text-muted mb-0">
                    <strong>{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</strong> is committed to rigorous educational standards, practical assessment criteria, and regular syllabus updates aligned with current industry standards. Every student receives personalized faculty attention, detailed lab manuals, and authentic digital credentials verifiable anytime via our institutional portal.
                </p>
            </div>
        </div>
    </section>
@endsection

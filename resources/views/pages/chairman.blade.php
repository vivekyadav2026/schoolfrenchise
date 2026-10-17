@extends('layouts.app')

@section('title', "Director's Message | " . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center text-md-start">
            <h1>Director's Message</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Director's Message</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-4 text-center">
                    <div class="p-4 bg-light rounded-4 shadow-sm border">
                        <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-2 mx-auto mb-3 shadow-sm" style="max-height: 100px; width: auto; object-fit: contain;">
                        <h4 class="fw-bold text-dark mb-1">Director's Desk</h4>
                        <div class="badge bg-warning text-dark px-3 py-1 mb-2">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                        <p class="small text-muted mb-0">{{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}</p>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                        <h2 class="fw-extrabold text-primary mb-3">Dear Students & Aspiring Professionals,</h2>
                        <p class="lead text-muted">
                            "In an era transformed by technology and automation, practical computer literacy is the foundation of every successful career."
                        </p>
                        <hr class="my-4">
                        <p class="text-secondary">
                            At <strong>{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</strong>, our mission is to ensure that modern, hands-on, high-quality computer education is accessible to every young learner in our region. We believe that true technical mastery comes from real-world practice, not just theoretical concepts.
                        </p>
                        <p class="text-secondary">
                            Whether you are learning fundamental office tools, mastering complex financial accounting with Tally Prime and GST, practicing high-speed bilingual typing for competitive exams, or exploring advanced software applications, our dedicated lab infrastructure and personalized faculty mentorship are designed to guide you step-by-step.
                        </p>
                        <p class="text-secondary">
                            I invite every ambitious student to join us, hone their digital capabilities, and build a bright, self-reliant future.
                        </p>
                        <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="d-block text-dark">Director & Academic Head</strong>
                                <small class="text-muted">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</small>
                            </div>
                            <span class="badge bg-primary-subtle text-primary px-3 py-2 fw-bold">Sheikhpura Campus</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('layouts.app')

@section('title', 'Terms and Conditions | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center text-md-start">
            <h1>Terms & Conditions</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Terms & Conditions</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="max-w-900 mx-auto">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <h3 class="fw-bold text-primary mb-3">Institutional Terms of Service</h3>
                    <p class="text-muted">Effective as of: {{ date('F d, Y') }}</p>
                    <hr>

                    <h5 class="fw-bold text-dark mt-4">1. Enrolment & Admissions</h5>
                    <p class="text-secondary">
                        Admissions to various diploma, certificate, and vocational computer courses at <strong>{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</strong> are subject to fulfilling academic prerequisites and timely submission of valid identity and educational documents.
                    </p>

                    <h5 class="fw-bold text-dark mt-4">2. Lab Conduct & Attendance</h5>
                    <p class="text-secondary">
                        Students must maintain regular attendance and follow laboratory safety and equipment protocols. Any deliberate mishandling or damaging of computer workstations, peripheral devices, or network equipment will incur disciplinary action.
                    </p>

                    <h5 class="fw-bold text-dark mt-4">3. Examinations & Certification</h5>
                    <p class="text-secondary">
                        Eligibility for periodical assessments and final diploma examinations requires satisfactory completion of hands-on practical assignments and clearance of dues. Marksheets and Certificates are issued upon successful evaluation and are verifiable online.
                    </p>

                    <h5 class="fw-bold text-dark mt-4">4. Intellectual Property</h5>
                    <p class="text-secondary">
                        All logos, training materials, syllabi, software assets, and course documentation of <strong>{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</strong> remain the exclusive intellectual property of the institute.
                    </p>

                    <h5 class="fw-bold text-dark mt-4">5. Jurisdiction</h5>
                    <p class="text-secondary">
                        All administrative inquiries, student agreements, and related matters are governed by the laws applicable in Bihar, under the exclusive jurisdiction of the competent courts in Sheikhpura, Bihar.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

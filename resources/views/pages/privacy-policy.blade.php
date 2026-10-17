@extends('layouts.app')

@section('title', 'Privacy Policy | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center text-md-start">
            <h1>Privacy Policy</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Privacy Policy</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="max-w-900 mx-auto">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <h3 class="fw-bold text-primary mb-3">Privacy Statement</h3>
                    <p class="text-muted">Last updated: {{ date('F d, Y') }}</p>
                    <hr>

                    <h5 class="fw-bold text-dark mt-4">1. Information We Collect</h5>
                    <p class="text-secondary">
                        <strong>{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</strong> collects student and franchise applicant information including full name, guardian name, email, phone number, physical address, photographs, and educational qualifications strictly for student admission, fee administration, exam scheduling, and certificate issuance.
                    </p>

                    <h5 class="fw-bold text-dark mt-4">2. Use of Information</h5>
                    <p class="text-secondary">
                        The gathered information is utilized solely to:
                    </p>
                    <ul class="text-secondary small">
                        <li>Register students into authorized computer training courses, batches, and academic evaluations.</li>
                        <li>Dispatch admission confirmation acknowledgements, fee payment receipts, and schedule updates.</li>
                        <li>Enable authorized anti-fraud online certificate and marksheet verification through unique serial identifiers.</li>
                        <li>Maintain accurate academic records and fee accounting.</li>
                    </ul>

                    <h5 class="fw-bold text-dark mt-4">3. Data Protection & Security</h5>
                    <p class="text-secondary">
                        We deploy industry-standard SSL encryption, secure server access controls, and hashed authentication credentials. Student contact details are kept confidential and are not published on open public portals.
                    </p>

                    <h5 class="fw-bold text-dark mt-4">4. Third-Party Sharing</h5>
                    <p class="text-secondary">
                        We do not sell, rent, or lease personal student or partner information to commercial marketing third parties. Data is processed only for authorized administrative operations and authorized payment gateway transactions.
                    </p>

                    <h5 class="fw-bold text-dark mt-4">5. Contact Our Campus Desk</h5>
                    <p class="text-secondary">
                        For any queries regarding your student records or privacy preferences, contact us at: <br>
                        <strong>Phone / Helpline:</strong> {{ $instituteSettings['contact_phone'] ?? '9162384660' }} | 
                        <strong>Address:</strong> {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

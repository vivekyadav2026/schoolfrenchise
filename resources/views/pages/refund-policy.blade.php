@extends('layouts.app')

@section('title', 'Refund and Cancellation Policy | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center text-md-start">
            <h1>Refund & Cancellation Policy</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Refund Policy</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="max-w-900 mx-auto">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <h3 class="fw-bold text-primary mb-3">Fee Refund & Payment Terms</h3>
                    <p class="text-muted">Last updated: {{ date('F d, Y') }}</p>
                    <hr>

                    <h5 class="fw-bold text-dark mt-4">1. Student Course Tuition Fees</h5>
                    <p class="text-secondary">
                        Admission registration fees and initial enrollment processing charges once deposited are non-refundable and non-transferable, as student seats and course allocations are provisioned immediately upon confirmation.
                    </p>
                    <p class="text-secondary">
                        Installment tuition fees paid for a course may be reviewed for a batch timing transfer or adjustment upon written request submitted to the administration office prior to the commencement of regular classes. Once a batch has commenced, no tuition fee refunds can be issued.
                    </p>

                    <h5 class="fw-bold text-dark mt-4">2. Online Payment Reversals & Duplicate Deductions</h5>
                    <p class="text-secondary">
                        In the event of a technical issue where amount is debited from a student's bank account or UPI multiple times for a single installment, the duplicate transaction will be reconciled with the payment gateway and refunded to the original payment source within 5 to 7 business working days.
                    </p>

                    <h5 class="fw-bold text-dark mt-4">3. Grievance Redressal</h5>
                    <p class="text-secondary">
                        For any billing or transaction inquiries, contact our campus desk at <strong>{{ $instituteSettings['contact_phone'] ?? '9162384660' }}</strong> or visit {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }} at {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

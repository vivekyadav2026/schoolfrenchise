@extends('layouts.app')

@section('title', 'Contact Us | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center text-md-start">
            <h1>Contact Us & Campus Helpdesk</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row g-5">
                <!-- Left: Contact Details -->
                <div class="col-lg-5">
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill uppercase mb-2">Get In Touch</span>
                    <h2 class="fw-extrabold display-6 mb-4">We are Here to Help You</h2>
                    <p class="text-muted">Have queries regarding student admission, batch timings, fee structures, or digital certificate verification? Connect directly with our campus desk.</p>

                    <div class="d-flex gap-3 mb-4">
                        <div class="rounded-circle bg-primary text-white p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0;">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Campus Address</h6>
                            <p class="text-muted small mb-0">{{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }}</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <div class="rounded-circle bg-warning text-dark p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0;">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Mobile Helpline</h6>
                            <p class="text-muted small mb-0"><a href="tel:{{ $instituteSettings['contact_phone'] ?? '9162384660' }}" class="text-decoration-none text-dark fw-bold">{{ $instituteSettings['contact_phone'] ?? '9162384660' }}</a> (Mon - Sat, 08:00 AM - 07:00 PM)</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <div class="rounded-circle bg-success text-white p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0;">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Direct WhatsApp Support</h6>
                            <p class="text-muted small mb-0"><a href="https://wa.me/91{{ $instituteSettings['whatsapp_number'] ?? '9162384660' }}?text=Hello%20Tech%20Era%20Computer" target="_blank" class="text-decoration-none text-success fw-bold">+91 {{ $instituteSettings['whatsapp_number'] ?? '9162384660' }}</a></p>
                        </div>
                    </div>
                </div>

                <!-- Right: Contact Form -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5">
                        <h4 class="fw-bold text-primary mb-3"><i class="fa-solid fa-paper-plane me-2"></i>Send Us a Message</h4>
                        <p class="text-muted small mb-4">Fill in your information and our national counseling desk will reply promptly.</p>

                        <form action="{{ route('contact.submit') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Your Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter your full name" value="{{ old('name') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="mobile" class="form-control" placeholder="10-digit mobile number" value="{{ old('mobile') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" placeholder="name@example.com" value="{{ old('email') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">State</label>
                                    <select name="state_id" class="form-select dce-state-select" data-district-target="contact_district_id">
                                        <option value="">Select State</option>
                                        @foreach($states as $st)
                                            <option value="{{ $st->id }}" {{ old('state_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">District</label>
                                    <select name="district_id" id="contact_district_id" class="form-select">
                                        <option value="">Select District</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Subject <span class="text-danger">*</span></label>
                                    <input type="text" name="subject" class="form-control" placeholder="Course inquiry, Franchise, etc." value="{{ old('subject') }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Message / Details <span class="text-danger">*</span></label>
                                    <textarea name="message" class="form-control" rows="4" placeholder="Write your query in detail..." required>{{ old('message') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-dce-primary w-100 py-3 fw-bold fs-6">
                                        <i class="fa-solid fa-paper-plane me-2"></i> Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

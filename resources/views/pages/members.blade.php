@extends('layouts.app')

@section('title', 'Corporate Members | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
    <div class="dce-page-banner">
        <div class="container">
            <h1>Corporate Board & Advisory Members</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Corporate Members</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill uppercase mb-2">Governance</span>
                <h2 class="fw-extrabold display-6">Board of Directors & Senior Management</h2>
                <p class="text-muted">Experienced educators, technocrats, and social administrators steering our mission forward.</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100">
                        <div class="rounded-circle mx-auto mb-3 bg-primary text-white d-flex align-items-center justify-content-center" style="width:100px; height:100px; font-size: 2.5rem;">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Er. Rajesh Kumar</h5>
                        <small class="text-primary fw-bold mb-2 d-block">Managing Director</small>
                        <p class="text-muted small mb-0">Senior IT Consultant & Pioneer of grassroots vocational education networks in North India.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100">
                        <div class="rounded-circle mx-auto mb-3 bg-info text-white d-flex align-items-center justify-content-center" style="width:100px; height:100px; font-size: 2.5rem;">
                            <i class="fa-solid fa-user-doctor"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Dr. S. K. Pathak</h5>
                        <small class="text-info fw-bold mb-2 d-block">Director - Academic Affairs</small>
                        <p class="text-muted small mb-0">Former University Dean overseeing standardized curriculum, examinations, and board grading.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100">
                        <div class="rounded-circle mx-auto mb-3 bg-success text-white d-flex align-items-center justify-content-center" style="width:100px; height:100px; font-size: 2.5rem;">
                            <i class="fa-solid fa-user-gear"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Smt. Vandana Singh</h5>
                        <small class="text-success fw-bold mb-2 d-block">Head - Franchise Relations</small>
                        <p class="text-muted small mb-0">Directing affiliate onboarding, compliance auditing, and centre growth enablement.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100">
                        <div class="rounded-circle mx-auto mb-3 bg-warning text-dark d-flex align-items-center justify-content-center" style="width:100px; height:100px; font-size: 2.5rem;">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Sri Amit Verma</h5>
                        <small class="text-dark fw-bold mb-2 d-block">Controller of Examinations</small>
                        <p class="text-muted small mb-0">Overseeing national digital examination schedules, marks approvals, and anti-counterfeit QR security.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

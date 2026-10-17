@extends('layouts.admin')

@section('title', 'Website Content & Configuration Settings')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.cms.index') }}">CMS</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Site Content</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-sliders text-primary me-2"></i>Website Content & Page Settings
            </h2>
            <p class="text-muted small mb-0">Configure institute metrics, contact helplines, chairman message excerpt, and social links.</p>
        </div>
        <div>
            <a href="{{ route('admin.cms.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> CMS Hub
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-2"></i>Please correct the following errors:</h6>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.cms.content.update') }}" method="POST">
        @csrf

        <div class="row g-4">
            <!-- Left Column: Institute Stats & Hero -->
            <div class="col-lg-6">
                <!-- Homepage Statistics Counters -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-chart-pie me-2 text-primary"></i>Homepage Institute Counters</h6>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">Live counters rendered in the public statistics counter section.</p>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="total_franchises_count" class="form-label small fw-semibold">Authorized Study Centres</label>
                                <input type="text" class="form-control font-monospace" id="total_franchises_count" name="total_franchises_count" value="{{ old('total_franchises_count', $settings['total_franchises_count'] ?? '12,042+') }}">
                            </div>

                            <div class="col-sm-6">
                                <label for="total_states_count" class="form-label small fw-semibold">States & UTs Covered</label>
                                <input type="text" class="form-control font-monospace" id="total_states_count" name="total_states_count" value="{{ old('total_states_count', $settings['total_states_count'] ?? '28+') }}">
                            </div>

                            <div class="col-sm-6">
                                <label for="total_cities_count" class="form-label small fw-semibold">Cities & Districts</label>
                                <input type="text" class="form-control font-monospace" id="total_cities_count" name="total_cities_count" value="{{ old('total_cities_count', $settings['total_cities_count'] ?? '763+') }}">
                            </div>

                            <div class="col-sm-6">
                                <label for="total_courses_count" class="form-label small fw-semibold">Job-Oriented Courses</label>
                                <input type="text" class="form-control font-monospace" id="total_courses_count" name="total_courses_count" value="{{ old('total_courses_count', $settings['total_courses_count'] ?? '364+') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hero Section Taglines -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-bullhorn me-2 text-primary"></i>Homepage Hero & Mission Text</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="hero_title" class="form-label small fw-semibold">Hero Heading</label>
                            <input type="text" class="form-control" id="hero_title" name="hero_title" value="{{ old('hero_title', $settings['hero_title'] ?? 'Empowering India Through Quality Computer & Skill Education') }}">
                        </div>

                        <div class="mb-3">
                            <label for="hero_subtitle" class="form-label small fw-semibold">Hero Subtitle</label>
                            <textarea class="form-control" id="hero_subtitle" name="hero_subtitle" rows="2">{{ old('hero_subtitle', $settings['hero_subtitle'] ?? 'India\'s fastest-growing autonomous vocational training organization providing high-quality computer education and job-oriented diplomas.') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="chairman_message_quote" class="form-label small fw-semibold">Chairman's Message Excerpt</label>
                            <textarea class="form-control" id="chairman_message_quote" name="chairman_message_quote" rows="3">{{ old('chairman_message_quote', $settings['chairman_message_quote'] ?? 'Our mission is to take digital literacy and software competence to every village, town, and district across India.') }}</textarea>
                        </div>

                        <div>
                            <label for="about_mission_summary" class="form-label small fw-semibold">Mission Statement Brief</label>
                            <textarea class="form-control" id="about_mission_summary" name="about_mission_summary" rows="2">{{ old('about_mission_summary', $settings['about_mission_summary'] ?? 'Providing affordable, practical, and industry-aligned IT education for all youth.') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Contact & Socials -->
            <div class="col-lg-6">
                <!-- Public Helplines & Office Info -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-headset me-2 text-primary"></i>Institute Contacts & Helpline</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label for="site_helpline" class="form-label small fw-semibold">Toll-Free / Phone Helpline</label>
                                <input type="text" class="form-control" id="site_helpline" name="site_helpline" value="{{ old('site_helpline', $settings['site_helpline'] ?? '+91 98765 43210') }}">
                            </div>

                            <div class="col-sm-6">
                                <label for="site_whatsapp" class="form-label small fw-semibold">Official WhatsApp Number</label>
                                <input type="text" class="form-control" id="site_whatsapp" name="site_whatsapp" value="{{ old('site_whatsapp', $settings['site_whatsapp'] ?? '9162384660') }}">
                            </div>

                            <div class="col-sm-6">
                                <label for="site_email" class="form-label small fw-semibold">General Enquiry Email</label>
                                <input type="email" class="form-control" id="site_email" name="site_email" value="{{ old('site_email', $settings['site_email'] ?? '') }}" placeholder="Enter email if available">
                            </div>

                            <div class="col-sm-6">
                                <label for="site_office_hours" class="form-label small fw-semibold">Office Working Hours</label>
                                <input type="text" class="form-control" id="site_office_hours" name="site_office_hours" value="{{ old('site_office_hours', $settings['site_office_hours'] ?? 'Mon - Sat: 9:00 AM - 6:00 PM') }}">
                            </div>
                        </div>

                        <div>
                            <label for="site_address" class="form-label small fw-semibold">Corporate Head Office Address</label>
                            <textarea class="form-control" id="site_address" name="site_address" rows="3">{{ old('site_address', $settings['site_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Social Media Channel URLs -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-share-nodes me-2 text-primary"></i>Official Social Media Profiles</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="social_facebook" class="form-label small fw-semibold"><i class="fa-brands fa-facebook text-primary me-1"></i>Facebook Page</label>
                            <input type="url" class="form-control" id="social_facebook" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}" placeholder="https://facebook.com/...">
                        </div>

                        <div class="mb-3">
                            <label for="social_instagram" class="form-label small fw-semibold"><i class="fa-brands fa-instagram text-danger me-1"></i>Instagram Profile</label>
                            <input type="url" class="form-control" id="social_instagram" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}" placeholder="https://instagram.com/...">
                        </div>

                        <div class="mb-3">
                            <label for="social_youtube" class="form-label small fw-semibold"><i class="fa-brands fa-youtube text-danger me-1"></i>YouTube Channel</label>
                            <input type="url" class="form-control" id="social_youtube" name="social_youtube" value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}" placeholder="https://youtube.com/...">
                        </div>

                        <div>
                            <label for="social_twitter" class="form-label small fw-semibold"><i class="fa-brands fa-x-twitter text-dark me-1"></i>X (Twitter)</label>
                            <input type="url" class="form-control" id="social_twitter" name="social_twitter" value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}" placeholder="https://x.com/...">
                        </div>
                    </div>
                </div>

                <!-- Submit Bar -->
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <span class="text-muted small"><i class="fa-solid fa-shield-halved me-1"></i>Changes update immediately on the public website.</span>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fa-solid fa-check me-1"></i> Save CMS Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

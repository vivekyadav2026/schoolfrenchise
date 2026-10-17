@extends('layouts.app')

@section('title', 'Search Authorized Centres | ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))
@section('meta_description', 'Search and locate authorized study centres of ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER') . '.')

@section('content')
    <div class="dce-page-banner">
        <div class="container text-center text-md-start">
            <h1>Authorized Centre Locator</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb dce-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Franchise</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Search Centre</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <!-- Search & Filters -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light">
                <form action="{{ route('franchise.search') }}" method="GET" class="row g-3 align-items-center">
                    <div class="col-lg-4">
                        <label class="form-label small fw-bold text-muted">Select State</label>
                        <select name="state_id" class="form-select dce-state-select" data-district-target="centre_search_dist">
                            <option value="">All States</option>
                            @foreach($states as $st)
                                <option value="{{ $st->id }}" {{ request('state_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label small fw-bold text-muted">Select District</label>
                        <select name="district_id" id="centre_search_dist" class="form-select">
                            <option value="">All Districts</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}" {{ request('district_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label small fw-bold text-muted">Search Keyword / Centre Code</label>
                        <input type="text" name="keyword" class="form-control" placeholder="e.g. TEC-1001 or City" value="{{ request('keyword') }}">
                    </div>
                    <div class="col-lg-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-dce-primary w-100 py-2" title="Search Centres">
                            <i class="fa-solid fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Centres Listing Grid -->
            @if($centres->isEmpty())
                <div class="text-center py-5">
                    <div class="text-muted fs-1 mb-3"><i class="fa-solid fa-building-circle-xmark"></i></div>
                    <h4 class="fw-bold text-dark">No Authorized Centre Found</h4>
                    <p class="text-muted">No centre matches your search criteria. You can apply to start a new centre in this location!</p>
                    <a href="{{ route('franchise.apply') }}" class="btn btn-dce-accent mt-2">
                        <i class="fa-solid fa-plus me-1"></i> Apply for Franchise in This Area
                    </a>
                </div>
            @else
                <div class="row g-4">
                    @foreach($centres as $centre)
                        <div class="col-lg-4 col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-4 border-top border-primary border-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-primary fw-bold px-3 py-1">Code: {{ $centre->centre_code }}</span>
                                    <span class="badge bg-success-subtle text-success fw-bold"><i class="fa-solid fa-circle-check me-1"></i> Active</span>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">{{ $centre->centre_name }}</h5>
                                <div class="small text-muted mb-3">
                                    <div><i class="fa-solid fa-user-tie text-primary me-2"></i><strong>Director:</strong> {{ $centre->director_name }}</div>
                                    <div class="mt-1"><i class="fa-solid fa-location-dot text-danger me-2"></i>{{ $centre->address }}, {{ $centre->city }} - {{ $centre->pincode }}</div>
                                    <div class="mt-1"><i class="fa-solid fa-map text-info me-2"></i>{{ $centre->district->name ?? '' }}, {{ $centre->state->name ?? '' }}</div>
                                    <div class="mt-1"><i class="fa-solid fa-phone text-warning me-2"></i>{{ $centre->mobile }}</div>
                                    <div class="mt-1"><i class="fa-solid fa-envelope text-info me-2"></i>{{ $centre->email }}</div>
                                </div>
                                <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><i class="fa-solid fa-desktop me-1 text-primary"></i> {{ $centre->lab_capacity }} Systems</small>
                                    <a href="tel:{{ $centre->mobile }}" class="btn btn-sm btn-outline-primary fw-bold">
                                        <i class="fa-solid fa-phone me-1"></i> Call Centre
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 d-flex justify-content-center">
                    {{ $centres->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection

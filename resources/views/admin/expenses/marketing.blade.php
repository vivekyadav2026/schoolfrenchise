@extends('layouts.admin')

@section('title', 'Marketing Campaigns & Ad Expenses')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.expenses.index') }}">Expenses</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Marketing</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-bullhorn text-primary me-2"></i>Marketing & Promotional Campaigns
            </h2>
            <p class="text-muted small mb-0">Track digital ad spending, lead generation cost (CPL), and franchise promotional ROI.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Operating Expenses
            </a>
            <a href="{{ route('admin.expenses.marketing.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-plus-circle me-1"></i> Add Marketing Campaign
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Campaigns</div>
                    <div class="h4 fw-bold text-dark mb-0">{{ number_format($stats['total_campaigns']) }}</div>
                    <div class="text-muted small mt-1">Logged initiatives</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Total Budget</div>
                    <div class="h4 fw-bold text-info mb-0">₹{{ number_format($stats['total_budget'], 2) }}</div>
                    <div class="text-muted small mt-1">Allocated funds</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Total Amount Spent</div>
                    <div class="h4 fw-bold text-danger mb-0">₹{{ number_format($stats['total_spent'], 2) }}</div>
                    <div class="text-muted small mt-1">Actual disbursements</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Total Leads</div>
                    <div class="h4 fw-bold text-warning mb-0">{{ number_format($stats['total_leads']) }}</div>
                    <div class="text-muted small mt-1">{{ number_format($stats['total_conversions']) }} enrolled</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Cost Per Lead (CPL)</div>
                    <div class="h4 fw-bold text-success mb-0">₹{{ number_format($stats['cpl'], 2) }}</div>
                    <div class="text-muted small mt-1">Average acquisition cost</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.expenses.marketing') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search campaign name, platform..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="platform" class="form-select form-select-sm">
                        <option value="">All Platforms</option>
                        <option value="google_ads" {{ request('platform') === 'google_ads' ? 'selected' : '' }}>Google Search & Display</option>
                        <option value="facebook_meta" {{ request('platform') === 'facebook_meta' ? 'selected' : '' }}>Meta (Facebook / Instagram)</option>
                        <option value="youtube" {{ request('platform') === 'youtube' ? 'selected' : '' }}>YouTube Video</option>
                        <option value="newspaper" {{ request('platform') === 'newspaper' ? 'selected' : '' }}>Newspaper & Print Ads</option>
                        <option value="pamphlet" {{ request('platform') === 'pamphlet' ? 'selected' : '' }}>Door-to-Door Pamphlets</option>
                        <option value="seminar" {{ request('platform') === 'seminar' ? 'selected' : '' }}>School Seminars & Workshops</option>
                        <option value="other" {{ request('platform') === 'other' ? 'selected' : '' }}>Other Media</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="centre_id" class="form-select form-select-sm">
                        <option value="">All Centres</option>
                        @foreach($centres as $cnt)
                            <option value="{{ $cnt->id }}" {{ request('centre_id') == $cnt->id ? 'selected' : '' }}>
                                {{ $cnt->centre_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'platform', 'centre_id']))
                        <a href="{{ route('admin.expenses.marketing') }}" class="btn btn-sm btn-light border" title="Clear Filters">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Campaigns Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0 text-dark">
                Campaign Ledger <span class="badge bg-light text-dark fw-normal border ms-1">{{ $campaigns->total() }}</span>
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3">Campaign Particulars</th>
                        <th>Platform</th>
                        <th>Target Branch</th>
                        <th class="text-end">Budget</th>
                        <th class="text-end">Amount Spent</th>
                        <th class="text-center">Leads</th>
                        <th class="text-center">Conversions</th>
                        <th class="text-end">CPL</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $cmp)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">{{ $cmp->campaign_name }}</div>
                                <div class="small text-muted">
                                    {{ $cmp->start_date?->format('d M, Y') }}
                                    @if($cmp->end_date)
                                        to {{ $cmp->end_date->format('d M, Y') }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ ucfirst(str_replace('_', ' ', $cmp->platform)) }}
                                </span>
                            </td>
                            <td>
                                @if($cmp->centre)
                                    <div class="fw-semibold text-dark">{{ $cmp->centre->centre_name }}</div>
                                    <div class="small text-muted font-monospace">{{ $cmp->centre->centre_code }}</div>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border">National Campaign</span>
                                @endif
                            </td>
                            <td class="text-end font-monospace text-muted">
                                ₹{{ number_format($cmp->budget, 2) }}
                            </td>
                            <td class="text-end font-monospace fw-bold text-danger">
                                ₹{{ number_format($cmp->amount_spent, 2) }}
                            </td>
                            <td class="text-center font-monospace">
                                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $cmp->leads_generated }}</span>
                            </td>
                            <td class="text-center font-monospace">
                                <span class="badge bg-success bg-opacity-10 text-success">{{ $cmp->conversions_count }}</span>
                            </td>
                            <td class="text-end font-monospace fw-semibold text-dark">
                                @if($cmp->leads_generated > 0)
                                    ₹{{ number_format($cmp->amount_spent / $cmp->leads_generated, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.expenses.marketing.edit', $cmp) }}" class="btn btn-light border text-dark" title="Edit Campaign">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.expenses.marketing.destroy', $cmp) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this marketing campaign record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light border text-danger" title="Delete Campaign">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-bullhorn fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h6>No Marketing Campaigns Found</h6>
                                    <p class="small text-muted mb-3">Record Google Ads, Facebook promotions, pamphlets, or seminars.</p>
                                    <a href="{{ route('admin.expenses.marketing.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fa-solid fa-plus-circle me-1"></i> Add Campaign
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($campaigns->hasPages())
            <div class="card-footer bg-white py-3 border-0">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Downloads & Resources Catalog')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Downloads & Resources</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-box-archive text-primary me-2"></i>Downloads & Orders Management
            </h2>
            <p class="text-muted small mb-0">Publish software tools, official prospectuses, forms, drivers, and fulfill franchise kit orders.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.downloads.orders') }}" class="btn btn-outline-info shadow-sm">
                <i class="fa-solid fa-cart-shopping me-1"></i> Orders Directory ({{ $stats['total_orders'] }})
            </a>
            <a href="{{ route('admin.downloads.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload New Resource
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Total Catalog Items</span>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 text-primary">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-dark mb-0">{{ number_format($stats['total_downloads']) }}</h3>
                    <div class="text-muted small mt-1">{{ $stats['active_downloads'] }} active for download</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Total Download Hits</span>
                        <div class="rounded-circle bg-success bg-opacity-10 p-2 text-success">
                            <i class="fa-solid fa-download"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-success mb-0">{{ number_format($stats['total_download_hits']) }}</h3>
                    <div class="text-muted small mt-1">Files downloaded by centres/students</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Total Orders Logged</span>
                        <div class="rounded-circle bg-info bg-opacity-10 p-2 text-info">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-info mb-0">{{ number_format($stats['total_orders']) }}</h3>
                    <div class="text-muted small mt-1">Kits, materials & resources</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Completed Revenue</span>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-2 text-warning">
                            <i class="fa-solid fa-indian-rupee-sign"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-warning mb-0">₹{{ number_format($stats['collected_order_revenue'], 2) }}</h3>
                    <div class="text-muted small mt-1">Verified paid orders</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.downloads.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search title, version, description..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="category" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        <option value="software" {{ request('category') === 'software' ? 'selected' : '' }}>Software & Tools</option>
                        <option value="prospectus" {{ request('category') === 'prospectus' ? 'selected' : '' }}>Prospectus & Brochures</option>
                        <option value="form" {{ request('category') === 'form' ? 'selected' : '' }}>Forms & Affidavits</option>
                        <option value="template" {{ request('category') === 'template' ? 'selected' : '' }}>Templates & Branding</option>
                        <option value="driver" {{ request('category') === 'driver' ? 'selected' : '' }}>Device Drivers</option>
                        <option value="study_note" {{ request('category') === 'study_note' ? 'selected' : '' }}>Study Notes</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Published (Active)</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Disabled (Hidden)</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'category', 'status']))
                        <a href="{{ route('admin.downloads.index') }}" class="btn btn-sm btn-light border" title="Clear Filters">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Downloads Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0 text-dark">
                Downloadable Catalog <span class="badge bg-light text-dark fw-normal border ms-1">{{ $downloads->total() }}</span>
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3">Resource Details</th>
                        <th>Category</th>
                        <th>Version</th>
                        <th>File Size</th>
                        <th class="text-center">Access / Fee</th>
                        <th class="text-center">Download Hits</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($downloads as $item)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">{{ $item->title }}</div>
                                @if($item->description)
                                    <div class="small text-muted text-truncate" style="max-width: 320px;">{{ $item->description }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ ucfirst(str_replace('_', ' ', $item->category)) }}
                                </span>
                            </td>
                            <td>
                                <span class="font-monospace small text-muted">{{ $item->version ?? 'v1.0' }}</span>
                            </td>
                            <td>
                                <span class="font-monospace small text-muted">{{ $item->file_size ?? 'N/A' }}</span>
                            </td>
                            <td class="text-center">
                                @if($item->is_free)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Free</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle font-monospace">
                                        ₹{{ number_format($item->price, 2) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary font-monospace">
                                    <i class="fa-solid fa-download me-1"></i>{{ $item->downloads_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($item->status)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Published</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border">Hidden</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.downloads.download-file', $item) }}" class="btn btn-light border text-primary" title="Download Resource">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                    <a href="{{ route('admin.downloads.edit', $item) }}" class="btn btn-light border text-dark" title="Edit Resource">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.downloads.toggle-status', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-light border text-warning" title="Toggle Publish Status">
                                            <i class="fa-solid {{ $item->status ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.downloads.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this downloadable resource?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light border text-danger" title="Delete Resource">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-box-open fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h6>No Download Resources Found</h6>
                                    <p class="small text-muted mb-3">Upload your first institutional form, prospectus, or software utility.</p>
                                    <a href="{{ route('admin.downloads.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Resource
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($downloads->hasPages())
            <div class="card-footer bg-white py-3 border-0">
                {{ $downloads->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

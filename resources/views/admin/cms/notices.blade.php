@extends('layouts.admin')

@section('title', 'Announcements & Marquee Notices')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.cms.index') }}">CMS</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Notices</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-scroll text-primary me-2"></i>Announcements & Marquee Notices
            </h2>
            <p class="text-muted small mb-0">Publish official circulars, exam notifications, and breaking ticker alerts.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.cms.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> CMS Hub
            </a>
            <a href="{{ route('admin.cms.notices.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-plus-circle me-1"></i> Post New Notice
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.cms.notices') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search notice title, content..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <select name="audience" class="form-select form-select-sm">
                        <option value="">All Audiences</option>
                        <option value="all" {{ request('audience') == 'all' ? 'selected' : '' }}>General / All</option>
                        <option value="centres" {{ request('audience') == 'centres' ? 'selected' : '' }}>Franchises / Centres</option>
                        <option value="students" {{ request('audience') == 'students' ? 'selected' : '' }}>Enrolled Students</option>
                        <option value="public" {{ request('audience') == 'public' ? 'selected' : '' }}>Public Visitors</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="is_marquee" class="form-select form-select-sm">
                        <option value="">All Displays</option>
                        <option value="1" {{ request('is_marquee') === '1' ? 'selected' : '' }}>Marquee Ticker Only</option>
                        <option value="0" {{ request('is_marquee') === '0' ? 'selected' : '' }}>Standard Notices</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active / Visible</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Draft / Inactive</option>
                    </select>
                </div>

                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Filter"><i class="fa-solid fa-filter"></i></button>
                    @if(request()->hasAny(['search', 'audience', 'is_marquee', 'status']))
                        <a href="{{ route('admin.cms.notices') }}" class="btn btn-sm btn-light border" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Notices Table Card -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Title & Summary</th>
                        <th>Target Audience</th>
                        <th>Marquee Ticker</th>
                        <th>Published Date</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notices as $notice)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">{{ $notice->title }}</div>
                                @if($notice->content)
                                    <div class="small text-muted">{{ Str::limit(strip_tags($notice->content), 70) }}</div>
                                @endif
                                @if($notice->attachment_path)
                                    <div class="mt-1">
                                        <a href="{{ asset($notice->attachment_path) }}" target="_blank" class="badge bg-light text-primary border text-decoration-none">
                                            <i class="fa-solid fa-file-pdf me-1 text-danger"></i> Scanned Document
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary text-uppercase fw-semibold">
                                    {{ $notice->target_audience }}
                                </span>
                            </td>
                            <td>
                                @if($notice->is_marquee)
                                    <span class="badge bg-danger"><i class="fa-solid fa-bolt me-1"></i>Marquee</span>
                                @else
                                    <span class="badge bg-light text-muted border">Standard</span>
                                @endif
                            </td>
                            <td class="font-monospace small text-muted">
                                {{ $notice->published_at ? \Carbon\Carbon::parse($notice->published_at)->format('d M, Y') : '-' }}
                            </td>
                            <td>
                                <form action="{{ route('admin.cms.notices.toggle-status', $notice) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="badge border-0 {{ $notice->status ? 'bg-success' : 'bg-secondary' }}" style="cursor: pointer;">
                                        {{ $notice->status ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.cms.notices.edit', $notice) }}" class="btn btn-light border text-dark" title="Edit Notice">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.cms.notices.destroy', $notice) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this notice?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light border text-danger" title="Delete Notice">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-scroll fa-3x text-muted opacity-25 mb-3 d-block"></i>
                                No notices or announcements found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($notices->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $notices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

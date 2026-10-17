@extends('layouts.admin')

@section('title', 'Public Enquiries & Lead Management')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.cms.index') }}">CMS</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Enquiries</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-envelope-open-text text-primary me-2"></i>Public Enquiries & Demo Requests
            </h2>
            <p class="text-muted small mb-0">Follow up with prospective students, franchisees, and general website inquiries.</p>
        </div>
        <div>
            <a href="{{ route('admin.cms.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> CMS Hub
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.cms.enquiries') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search name, phone, email, subject..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All Enquiry Types</option>
                        <option value="demo_request" {{ request('type') == 'demo_request' ? 'selected' : '' }}>Live Demo Request</option>
                        <option value="admission_enquiry" {{ request('type') == 'admission_enquiry' ? 'selected' : '' }}>Admission Enquiry</option>
                        <option value="general_contact" {{ request('type') == 'general_contact' ? 'selected' : '' }}>General Contact Form</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New / Unread</option>
                        <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted / Enrolled</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Filter"><i class="fa-solid fa-filter"></i></button>
                    @if(request()->hasAny(['search', 'type', 'status']))
                        <a href="{{ route('admin.cms.enquiries') }}" class="btn btn-sm btn-light border" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Enquiries Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Sender & Contacts</th>
                        <th>Subject & Message Teaser</th>
                        <th>Enquiry Type</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Received On</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enquiries as $item)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">{{ $item->name }}</div>
                                <div class="small font-monospace text-primary">
                                    <i class="fa-solid fa-phone me-1"></i>{{ $item->mobile }}
                                </div>
                                @if($item->email)
                                    <div class="small text-muted text-truncate" style="max-width: 180px;">
                                        <i class="fa-regular fa-envelope me-1"></i>{{ $item->email }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ Str::limit($item->subject, 35) }}</div>
                                <div class="small text-muted">{{ Str::limit($item->message, 50) }}</div>
                            </td>
                            <td>
                                @if($item->type == 'demo_request')
                                    <span class="badge bg-primary bg-opacity-10 text-primary">Demo Request</span>
                                @elseif($item->type == 'admission_enquiry')
                                    <span class="badge bg-warning bg-opacity-10 text-warning">Admission</span>
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info">General Contact</span>
                                @endif
                            </td>
                            <td>
                                <div class="small text-dark">{{ $item->district ? $item->district->name : '-' }}</div>
                                <div class="small text-muted">{{ $item->state ? $item->state->name : '' }}</div>
                            </td>
                            <td>
                                @if($item->status == 'new')
                                    <span class="badge bg-danger">New</span>
                                @elseif($item->status == 'contacted')
                                    <span class="badge bg-info">Contacted</span>
                                @elseif($item->status == 'converted')
                                    <span class="badge bg-success">Converted</span>
                                @else
                                    <span class="badge bg-secondary">Closed</span>
                                @endif
                            </td>
                            <td class="font-monospace small text-muted">
                                {{ $item->created_at->format('d M, Y') }}
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $item->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.cms.enquiries.show', $item) }}" class="btn btn-light border text-dark" title="View Enquiry Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.cms.enquiries.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this enquiry record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light border text-danger" title="Delete Record">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-inbox fa-3x text-muted opacity-25 mb-3 d-block"></i>
                                No public enquiries or leads found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($enquiries->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $enquiries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

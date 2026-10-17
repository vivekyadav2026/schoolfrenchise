@extends('layouts.admin')

@section('title', 'Notification Center & Broadcasts')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-bell text-primary me-2"></i>Notification Center & Messaging
            </h2>
            <p class="text-muted small mb-0">Broadcast instant alerts, fee notices, and academic reminders across students and franchise centres.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-secondary shadow-sm">
                    <i class="fa-solid fa-envelope-open me-1"></i> Mark All as Read
                </button>
            </form>
            <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-paper-plane me-1"></i> Compose Broadcast
            </a>
        </div>
    </div>

    <!-- KPI Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Total Dispatched</div>
                            <div class="h3 fw-bold text-dark mb-0">{{ number_format($stats['total_notifications']) }}</div>
                            <div class="text-muted small mt-1">Lifetime messages</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                            <i class="fa-solid fa-bell fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Unread Alerts</div>
                            <div class="h3 fw-bold text-danger mb-0">{{ number_format($stats['unread_count']) }}</div>
                            <div class="text-muted small mt-1">Awaiting recipient review</div>
                        </div>
                        <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                            <i class="fa-solid fa-envelope fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Urgent Alerts</div>
                            <div class="h3 fw-bold text-warning mb-0">{{ number_format($stats['alert_count']) }}</div>
                            <div class="text-muted small mt-1">High priority notices</div>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                            <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Today's Broadcasts</div>
                            <div class="h3 fw-bold text-success mb-0">{{ number_format($stats['today_dispatched']) }}</div>
                            <div class="text-muted small mt-1">Sent in last 24h</div>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                            <i class="fa-solid fa-paper-plane fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.notifications.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search notification title, message, recipient..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>General Info</option>
                        <option value="alert" {{ request('type') == 'alert' ? 'selected' : '' }}>Urgent Alert</option>
                        <option value="fee" {{ request('type') == 'fee' ? 'selected' : '' }}>Fee / Payment</option>
                        <option value="exam" {{ request('type') == 'exam' ? 'selected' : '' }}>Examination</option>
                        <option value="certificate" {{ request('type') == 'certificate' ? 'selected' : '' }}>Certificate</option>
                        <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>System Broadcast</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread Only</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="role" class="form-select form-select-sm">
                        <option value="">All Recipient Roles</option>
                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Students</option>
                        <option value="centre" {{ request('role') == 'centre' ? 'selected' : '' }}>Centre Directors</option>
                        <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff Members</option>
                        <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admins</option>
                    </select>
                </div>

                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Filter"><i class="fa-solid fa-filter"></i></button>
                    @if(request()->hasAny(['search', 'type', 'status', 'role']))
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-light border" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Notifications Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Type</th>
                        <th>Notification Particulars</th>
                        <th>Recipient Account</th>
                        <th>Dispatched By</th>
                        <th>Sent On</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $item)
                        <tr class="{{ is_null($item->read_at) ? 'table-light' : '' }}">
                            <td class="ps-3">
                                @if($item->type == 'alert')
                                    <span class="badge bg-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i>Alert</span>
                                @elseif($item->type == 'fee')
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-indian-rupee-sign me-1"></i>Fee</span>
                                @elseif($item->type == 'exam')
                                    <span class="badge bg-info"><i class="fa-solid fa-pen-nib me-1"></i>Exam</span>
                                @elseif($item->type == 'certificate')
                                    <span class="badge bg-success"><i class="fa-solid fa-certificate me-1"></i>Certificate</span>
                                @elseif($item->type == 'system')
                                    <span class="badge bg-dark"><i class="fa-solid fa-server me-1"></i>System</span>
                                @else
                                    <span class="badge bg-primary"><i class="fa-solid fa-circle-info me-1"></i>Info</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->title }}</div>
                                <div class="small text-muted" style="max-width: 380px;">{{ Str::limit($item->message, 90) }}</div>
                                @if($item->action_url)
                                    <div class="mt-1">
                                        <a href="{{ $item->action_url }}" target="_blank" class="small text-primary text-decoration-none">
                                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Target Link
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($item->user)
                                    <div class="fw-semibold text-dark">{{ $item->user->name }}</div>
                                    <div class="small text-muted font-monospace">{{ $item->user->email }}</div>
                                    <span class="badge bg-light text-muted border text-uppercase" style="font-size: 0.7rem;">{{ $item->user->role }}</span>
                                @else
                                    <span class="text-muted small">User Removed</span>
                                @endif
                            </td>
                            <td>
                                <div class="small text-dark fw-semibold">{{ $item->sender ? $item->sender->name : 'System Automated' }}</div>
                            </td>
                            <td class="font-monospace small text-muted">
                                {{ $item->created_at->format('d M, Y') }}
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $item->created_at->format('h:i A') }}</div>
                            </td>
                            <td>
                                @if($item->read_at)
                                    <span class="badge bg-light text-muted border"><i class="fa-solid fa-check-double text-success me-1"></i>Read</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Unread</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    @if(is_null($item->read_at))
                                        <form action="{{ route('admin.notifications.read', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-light border text-primary" title="Mark as Read">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.notifications.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this notification log?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light border text-danger" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-bell-slash fa-3x text-muted opacity-25 mb-3 d-block"></i>
                                No notifications dispatched yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($notifications->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

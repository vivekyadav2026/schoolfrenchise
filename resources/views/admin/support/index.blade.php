@extends('layouts.admin')

@section('title', 'Help & Support Helpdesk')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Help & Support</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-headset text-primary me-2"></i>Help & Support Central Helpdesk
            </h2>
            <p class="text-muted small mb-0">Track and resolve institutional enquiries, technical support tickets, and exam queries from franchise centres and students.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.support.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-plus-circle me-1"></i> Create Support Ticket
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
                    <div class="text-muted small fw-bold text-uppercase">Total Inward</div>
                    <div class="h4 fw-bold text-dark mb-0">{{ number_format($stats['total_tickets']) }}</div>
                    <div class="text-muted small mt-1">All tickets logged</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Open / Pending</div>
                    <div class="h4 fw-bold text-warning mb-0">{{ number_format($stats['open_tickets']) }}</div>
                    <div class="text-muted small mt-1">Awaiting response</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">In Progress</div>
                    <div class="h4 fw-bold text-info mb-0">{{ number_format($stats['in_progress_tickets']) }}</div>
                    <div class="text-muted small mt-1">Being addressed</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Resolved / Closed</div>
                    <div class="h4 fw-bold text-success mb-0">{{ number_format($stats['resolved_tickets']) }}</div>
                    <div class="text-muted small mt-1">Satisfactorily concluded</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Urgent Escalations</div>
                    <div class="h4 fw-bold text-danger mb-0">{{ number_format($stats['urgent_tickets']) }}</div>
                    <div class="text-muted small mt-1">High-priority attention</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.support.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search ticket #, subject..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="answered" {{ request('status') === 'answered' ? 'selected' : '' }}>Answered</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="priority" class="form-select form-select-sm">
                        <option value="">All Priorities</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>

                <div class="col-md-3">
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
                    @if(request()->anyFilled(['search', 'status', 'priority', 'category', 'centre_id']))
                        <a href="{{ route('admin.support.index') }}" class="btn btn-sm btn-light border" title="Clear Filters">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0 text-dark">
                Support Tickets Directory <span class="badge bg-light text-dark fw-normal border ms-1">{{ $tickets->total() }}</span>
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3">Ticket ID</th>
                        <th>Subject & Category</th>
                        <th>Requester / Branch</th>
                        <th class="text-center">Priority</th>
                        <th>Assigned Staff</th>
                        <th>Last Activity</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $tck)
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route('admin.support.show', $tck) }}" class="font-monospace fw-bold text-primary text-decoration-none">
                                    {{ $tck->ticket_no }}
                                </a>
                                <div class="small text-muted">{{ $tck->created_at?->format('d M, Y') }}</div>
                            </td>
                            <td>
                                <a href="{{ route('admin.support.show', $tck) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $tck->subject }}
                                </a>
                                <div>
                                    <span class="badge bg-light text-dark border small">
                                        {{ ucfirst(str_replace('_', ' ', $tck->category)) }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if($tck->centre)
                                    <div class="fw-semibold text-dark">{{ $tck->centre->centre_name }}</div>
                                    <div class="small text-muted font-monospace">{{ $tck->centre->centre_code }}</div>
                                @elseif($tck->user)
                                    <div class="fw-semibold text-dark">{{ $tck->user->name }}</div>
                                    <div class="small text-muted">{{ $tck->user->email }}</div>
                                @else
                                    <div class="text-muted small">Central Board System</div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($tck->priority === 'urgent')
                                    <span class="badge bg-danger text-white">Urgent</span>
                                @elseif($tck->priority === 'high')
                                    <span class="badge bg-warning text-dark">High</span>
                                @elseif($tck->priority === 'medium')
                                    <span class="badge bg-info text-dark">Medium</span>
                                @else
                                    <span class="badge bg-secondary">Low</span>
                                @endif
                            </td>
                            <td>
                                @if($tck->assignee)
                                    <div class="fw-semibold text-dark small"><i class="fa-solid fa-user-gear me-1 text-primary"></i>{{ $tck->assignee->name }}</div>
                                @else
                                    <span class="text-muted small fst-italic">Unassigned</span>
                                @endif
                            </td>
                            <td class="small">
                                {{ $tck->last_reply_at?->diffForHumans() ?? $tck->created_at?->diffForHumans() }}
                            </td>
                            <td class="text-center">
                                @if($tck->status === 'open')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Open</span>
                                @elseif($tck->status === 'in_progress')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">In Progress</span>
                                @elseif($tck->status === 'answered')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Answered</span>
                                @elseif($tck->status === 'resolved')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Resolved</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">Closed</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('admin.support.show', $tck) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-comments me-1"></i> Thread
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-headset fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h6>No Support Tickets Found</h6>
                                    <p class="small text-muted mb-3">All franchise queries and student issues are currently resolved.</p>
                                    <a href="{{ route('admin.support.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fa-solid fa-plus-circle me-1"></i> Create Ticket
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="card-footer bg-white py-3 border-0">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

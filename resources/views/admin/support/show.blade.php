@extends('layouts.admin')

@section('title', 'Ticket #' . $ticket->ticket_no . ': ' . $ticket->subject)

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.support.index') }}">Support</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $ticket->ticket_no }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h2 class="h4 fw-bold mb-0 text-dark">{{ $ticket->subject }}</h2>
                <span class="badge bg-primary font-monospace">{{ $ticket->ticket_no }}</span>
                @if($ticket->status === 'open')
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Open</span>
                @elseif($ticket->status === 'in_progress')
                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">In Progress</span>
                @elseif($ticket->status === 'waiting_for_student')
                    <span class="badge bg-warning text-dark border px-2 py-1"><i class="fa-solid fa-hourglass-half me-1"></i>Waiting for Student</span>
                @elseif($ticket->status === 'answered')
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Answered</span>
                @elseif($ticket->status === 'resolved')
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Resolved</span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">Closed</span>
                @endif
            </div>
        </div>
        <div>
            <a href="{{ route('admin.support.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Helpdesk
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

    <div class="row g-4">
        <!-- Main Conversation Thread -->
        <div class="col-lg-8">
            <!-- Original Ticket Description Card -->
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-light py-3 border-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                            {{ strtoupper(substr($ticket->user?->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark">{{ $ticket->user?->name ?? 'Requester' }}</div>
                            <div class="small text-muted">{{ $ticket->created_at?->format('d M, Y h:i A') }} (Original Query)</div>
                        </div>
                    </div>
                    <span class="badge bg-light text-dark border">{{ ucfirst($ticket->category) }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="text-dark" style="white-space: pre-line;">{{ $ticket->description }}</div>
                </div>
            </div>

            <!-- Thread Replies -->
            @foreach($ticket->replies->sortBy('created_at') as $reply)
                <div class="card border-0 shadow-sm rounded-3 mb-3 {{ $reply->is_staff_reply ? 'bg-white border-start border-4 border-primary' : 'bg-light' }}">
                    <div class="card-header {{ $reply->is_staff_reply ? 'bg-white' : 'bg-light' }} py-2 border-0 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle {{ $reply->is_staff_reply ? 'bg-primary text-white' : 'bg-secondary text-white' }} d-flex align-items-center justify-content-center fw-bold small" style="width: 30px; height: 30px;">
                                {{ strtoupper(substr($reply->user?->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <span class="fw-bold text-dark small">{{ $reply->user?->name ?? 'User' }}</span>
                                @if($reply->is_staff_reply)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1" style="font-size: 10px;">Support Staff</span>
                                @endif
                                <span class="text-muted small ms-2" style="font-size: 11px;">{{ $reply->created_at?->format('d M, Y h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3 pt-1">
                        <div class="text-dark small" style="white-space: pre-line;">{{ $reply->message }}</div>
                        @if($reply->attachment_path)
                            <div class="mt-2 pt-2 border-top">
                                <a href="{{ asset('storage/' . $reply->attachment_path) }}" target="_blank" class="btn btn-sm btn-light border text-primary">
                                    <i class="fa-solid fa-paperclip me-1"></i> View Attachment
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Reply Box -->
            @if($ticket->status !== 'closed')
                <div class="card border-0 shadow-sm rounded-3 bg-white mt-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-reply text-primary me-2"></i>Post Official Reply
                        </h6>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <form method="POST" action="{{ route('admin.support.reply', $ticket) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4" required placeholder="Type your official administrative reply or instructions here..."></textarea>
                                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="row align-items-center g-3">
                                <div class="col-md-6">
                                    <input type="file" name="attachment" class="form-control form-control-sm">
                                    <div class="form-text small">Optional screenshot / document up to 5MB.</div>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="form-check form-check-inline me-3">
                                        <input class="form-check-input" type="checkbox" name="mark_resolved" value="1" id="markResolvedCheck">
                                        <label class="form-check-label small fw-semibold" for="markResolvedCheck">
                                            Mark ticket as Resolved
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm px-4">
                                        <i class="fa-solid fa-paper-plane me-1"></i> Send Reply
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-secondary border-0 p-3 mt-4 text-center">
                    <i class="fa-solid fa-lock me-1"></i> This support ticket has been closed. Reopen the ticket to post further replies.
                </div>
            @endif
        </div>

        <!-- Sidebar Actions & Details -->
        <div class="col-lg-4">
            <!-- Ticket Info Card -->
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold text-dark mb-0">Ticket Details</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="mb-2">
                        <span class="text-muted small fw-bold d-block">Status</span>
                        @if($ticket->status === 'open')
                            <span class="badge bg-warning-subtle text-warning border px-2 py-1">Open</span>
                        @elseif($ticket->status === 'in_progress')
                            <span class="badge bg-info-subtle text-info border px-2 py-1">In Progress</span>
                        @elseif($ticket->status === 'answered')
                            <span class="badge bg-primary-subtle text-primary border px-2 py-1">Answered</span>
                        @elseif($ticket->status === 'resolved')
                            <span class="badge bg-success-subtle text-success border px-2 py-1">Resolved</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">Closed</span>
                        @endif
                    </div>

                    <div class="mb-2">
                        <span class="text-muted small fw-bold d-block">Priority</span>
                        @if($ticket->priority === 'urgent')
                            <span class="badge bg-danger">Urgent</span>
                        @elseif($ticket->priority === 'high')
                            <span class="badge bg-warning text-dark">High</span>
                        @elseif($ticket->priority === 'medium')
                            <span class="badge bg-info text-dark">Medium</span>
                        @else
                            <span class="badge bg-secondary">Low</span>
                        @endif
                    </div>

                    <div class="mb-2">
                        <span class="text-muted small fw-bold d-block">Category</span>
                        <div class="small fw-semibold text-dark">{{ ucfirst(str_replace('_', ' ', $ticket->category)) }}</div>
                    </div>

                    <div class="mb-2">
                        <span class="text-muted small fw-bold d-block">Submitted By</span>
                        <div class="small fw-semibold text-dark">{{ $ticket->user?->name ?? 'Requester' }}</div>
                        <div class="small text-muted">{{ $ticket->user?->email }}</div>
                    </div>

                    @if($ticket->centre)
                        <div class="mb-2">
                            <span class="text-muted small fw-bold d-block">Affiliated Branch</span>
                            <div class="small fw-semibold text-dark">{{ $ticket->centre->centre_name }}</div>
                            <div class="small text-primary font-monospace">{{ $ticket->centre->centre_code }}</div>
                        </div>
                    @endif

                    <div class="mb-2">
                        <span class="text-muted small fw-bold d-block">Assigned Staff</span>
                        <div class="small fw-semibold text-dark">
                            <i class="fa-solid fa-user-gear text-primary me-1"></i>
                            {{ $ticket->assignee?->name ?? 'Unassigned' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staff Assignment Form -->
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold text-dark mb-0">Assign Staff</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <form method="POST" action="{{ route('admin.support.assign', $ticket) }}">
                        @csrf
                        <div class="mb-2">
                            <select name="assigned_to" class="form-select form-select-sm" required>
                                <option value="">-- Select Staff Officer --</option>
                                @foreach($staffMembers as $stf)
                                    <option value="{{ $stf->id }}" {{ $ticket->assigned_to == $stf->id ? 'selected' : '' }}>
                                        {{ $stf->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                            <i class="fa-solid fa-user-check me-1"></i> Update Assignee
                        </button>
                    </form>
                </div>
            </div>

            <!-- Status Changer -->
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold text-dark mb-0">Modify Status</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <form method="POST" action="{{ route('admin.support.status', $ticket) }}">
                        @csrf
                        <div class="mb-2">
                            <select name="status" class="form-select form-select-sm">
                                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="waiting_for_student" {{ $ticket->status === 'waiting_for_student' ? 'selected' : '' }}>Waiting for Student</option>
                                <option value="answered" {{ $ticket->status === 'answered' ? 'selected' : '' }}>Answered</option>
                                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-sm btn-dark w-100">
                            <i class="fa-solid fa-check me-1"></i> Set Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Status Transition & Ticket History -->
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-timeline text-primary me-2"></i>Status History</h6>
                    <span class="badge bg-light text-muted border small">Audit Trail</span>
                </div>
                <div class="card-body p-3 pt-0">
                    <div class="small">
                        <div class="d-flex align-items-start gap-2 mb-2 pb-2 border-bottom">
                            <i class="fa-solid fa-circle-dot text-primary mt-1" style="font-size: 10px;"></i>
                            <div>
                                <div class="fw-semibold text-dark">Ticket Initiated</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $ticket->created_at?->format('d M, Y h:i A') }}</div>
                            </div>
                        </div>

                        @php
                            $auditLogs = \App\Models\AuditLog::where('auditable_type', 'App\Models\SupportTicket')
                                ->where('auditable_id', $ticket->id)
                                ->latest()
                                ->take(5)
                                ->get();
                        @endphp

                        @foreach($auditLogs as $log)
                        <div class="d-flex align-items-start gap-2 mb-2 pb-2 border-bottom">
                            <i class="fa-solid fa-circle-check text-success mt-1" style="font-size: 10px;"></i>
                            <div>
                                <div class="fw-semibold text-dark">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</div>
                                @if(isset($log->new_values['status']))
                                    <span class="badge bg-light text-dark border" style="font-size: 10px;">Status: {{ ucfirst(str_replace('_', ' ', $log->new_values['status'])) }}</span>
                                @endif
                                <div class="text-muted" style="font-size: 11px;">{{ $log->created_at?->format('d M, Y h:i A') }}</div>
                            </div>
                        </div>
                        @endforeach

                        @if($ticket->last_reply_at)
                        <div class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-info mt-1" style="font-size: 10px;"></i>
                            <div>
                                <div class="fw-semibold text-dark">Last Activity</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $ticket->last_reply_at->format('d M, Y h:i A') }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Archive Ticket -->
            <form action="{{ route('admin.support.destroy', $ticket) }}" method="POST" onsubmit="return confirm('Archive this support ticket?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                    <i class="fa-solid fa-trash me-1"></i> Archive Ticket
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

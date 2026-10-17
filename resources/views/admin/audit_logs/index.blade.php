@extends('layouts.admin')

@section('title', 'Audit Logs & Security Trail - TECH ERA COMPUTER')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Audit Logs & Security Trail</h2>
            <p class="text-muted small mb-0">Track, monitor, and investigate sensitive administrative actions, record alterations, and financial modifications.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.audit-logs.export', request()->query()) }}" class="btn btn-success shadow-sm">
                <i class="fa-solid fa-file-csv me-1"></i> Export Audit Logs CSV
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Total System Logs</div>
                    <div class="h4 fw-bold text-dark mb-0">{{ number_format($stats['total_logs']) }}</div>
                    <div class="text-muted small mt-1">Lifetime recorded events</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Today's Activity</div>
                    <div class="h4 fw-bold text-info mb-0">{{ number_format($stats['today_logs']) }}</div>
                    <div class="text-muted small mt-1">Actions executed today</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Critical / High-Risk</div>
                    <div class="h4 fw-bold text-danger mb-0">{{ number_format($stats['critical_logs']) }}</div>
                    <div class="text-muted small mt-1">Deletions, refunds & revocations</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Active Operators</div>
                    <div class="h4 fw-bold text-success mb-0">{{ number_format($stats['unique_users']) }}</div>
                    <div class="text-muted small mt-1">Distinct administrative actors</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search action, IP, user name..." value="{{ request('search') }}">
                </div>

                <div class="col-md-2">
                    <select name="action" class="form-select form-select-sm">
                        <option value="">All Action Types</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ $act }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">All Operators</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="auditable_type" class="form-select form-select-sm">
                        <option value="">All Target Entities</option>
                        @foreach($models as $m)
                            <option value="{{ $m }}" {{ request('auditable_type') == $m ? 'selected' : '' }}>{{ class_basename($m) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-1">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" title="Date From">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" title="Date To">
                </div>

                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Apply Filter"><i class="fa-solid fa-filter"></i></button>
                    @if(request()->hasAny(['search', 'action', 'user_id', 'auditable_type', 'date_from', 'date_to']))
                        <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-light border" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Log ID & Time</th>
                        <th>Action Type</th>
                        <th>Target Resource</th>
                        <th>Performed By</th>
                        <th>Client IP Address</th>
                        <th class="text-end pe-3">Inspection</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="ps-3 font-monospace small">
                                <div class="fw-bold text-dark">#LOG-{{ str_pad($log->id, 6, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    {{ $log->created_at ? $log->created_at->format('d M, Y H:i:s') : '-' }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $act = strtolower($log->action);
                                    $badgeClass = 'bg-secondary';
                                    if (str_contains($act, 'delete') || str_contains($act, 'revoke') || str_contains($act, 'refund') || str_contains($act, 'freeze')) {
                                        $badgeClass = 'bg-danger';
                                    } elseif (str_contains($act, 'create') || str_contains($act, 'store') || str_contains($act, 'approve')) {
                                        $badgeClass = 'bg-success';
                                    } elseif (str_contains($act, 'update') || str_contains($act, 'edit')) {
                                        $badgeClass = 'bg-primary';
                                    } elseif (str_contains($act, 'status') || str_contains($act, 'toggle')) {
                                        $badgeClass = 'bg-warning text-dark';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }} font-monospace">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>
                                @if($log->auditable_type)
                                    <div class="small fw-semibold text-dark">{{ class_basename($log->auditable_type) }}</div>
                                    @if($log->auditable_id)
                                        <div class="small text-muted font-monospace">ID: #{{ $log->auditable_id }}</div>
                                    @endif
                                @else
                                    <span class="text-muted small">System Global</span>
                                @endif
                            </td>
                            <td>
                                @if($log->user)
                                    <div class="fw-semibold text-dark">{{ $log->user->name }}</div>
                                    <div class="small text-muted font-monospace">{{ $log->user->email }}</div>
                                @else
                                    <span class="badge bg-light text-muted border">System Automated</span>
                                @endif
                            </td>
                            <td class="font-monospace small text-muted">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('admin.audit-logs.show', $log) }}" class="btn btn-sm btn-outline-primary" title="Inspect Record">
                                    <i class="fa-solid fa-magnifying-glass me-1"></i> Inspect
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-shield-halved fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-bold">No audit trail records found.</div>
                                <div class="small">Try adjusting search parameters or date filters.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

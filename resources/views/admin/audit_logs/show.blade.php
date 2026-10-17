@extends('layouts.admin')

@section('title', 'Audit Log Inspection - #LOG-' . $auditLog->id . ' - TECH ERA COMPUTER')

@section('content')
<div class="container-fluid px-0">
    <!-- Top Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h2 class="h3 fw-bold text-dark mb-0">Audit Record #LOG-{{ str_pad($auditLog->id, 6, '0', STR_PAD_LEFT) }}</h2>
                @php
                    $act = strtolower($auditLog->action);
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
                <span class="badge {{ $badgeClass }} fs-6 font-monospace">{{ $auditLog->action }}</span>
            </div>
            <p class="text-muted small mb-0">Captured at {{ $auditLog->created_at ? $auditLog->created_at->format('d M, Y \a\t H:i:s T') : '-' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Audit Trail
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Metadata & Operator Information -->
        <div class="col-lg-5">
            <!-- Event Particulars -->
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-shield-halved me-2 text-primary"></i> Execution Particulars
                    </h5>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Unique Log ID:</span>
                            <span class="font-monospace fw-bold text-dark">#LOG-{{ str_pad($auditLog->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Action Trigger:</span>
                            <span class="font-monospace fw-bold text-primary">{{ $auditLog->action }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Client IP Address:</span>
                            <span class="font-monospace text-dark">{{ $auditLog->ip_address ?? '127.0.0.1' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Timestamp:</span>
                            <span class="text-dark">{{ $auditLog->created_at ? $auditLog->created_at->format('d M, Y H:i:s') : '-' }}</span>
                        </li>
                    </ul>

                    @if($auditLog->user_agent)
                        <div class="mt-3 pt-3 border-top">
                            <label class="small text-muted fw-bold mb-1">Client User Agent:</label>
                            <div class="p-2 bg-light rounded border font-monospace text-break" style="font-size: 0.75rem;">
                                {{ $auditLog->user_agent }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Operator Account -->
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-user-gear me-2 text-primary"></i> Responsible Actor
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($auditLog->user)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 48px; height: 48px;">
                                {{ strtoupper(substr($auditLog->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ $auditLog->user->name }}</div>
                                <div class="small text-muted font-monospace">{{ $auditLog->user->email }}</div>
                                <div class="mt-1">
                                    <span class="badge bg-secondary small text-capitalize">{{ $auditLog->user->role }}</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.show', $auditLog->user) }}" class="btn btn-sm btn-outline-primary w-100">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Operator Dossier
                        </a>
                    @else
                        <div class="p-3 bg-light rounded border text-muted small">
                            <i class="fa-solid fa-robot me-1"></i> Executed by automated system daemon or guest process.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Target Resource -->
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-database me-2 text-primary"></i> Target Resource Entity
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($auditLog->auditable_type)
                        <div class="p-3 bg-light rounded border">
                            <div class="small text-muted mb-1">Target Model:</div>
                            <div class="fw-bold text-dark font-monospace mb-2">{{ class_basename($auditLog->auditable_type) }}</div>
                            <div class="small text-muted mb-1">Primary Key ID:</div>
                            <div class="fw-bold text-dark font-monospace">#{{ $auditLog->auditable_id ?? 'N/A' }}</div>
                        </div>
                    @else
                        <div class="text-muted small">
                            Global administrative system operation (not scoped to an individual Eloquent entity).
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: State Payload & Diff Inspector -->
        <div class="col-lg-7">
            <!-- Changes Differential Card -->
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-code-compare me-2 text-primary"></i> State Changes & Payload Differential
                    </h5>
                    <p class="text-muted small mb-0">Comparison of attributes before and after the administrative operation.</p>
                </div>
                <div class="card-body p-4">
                    @if($auditLog->old_values && $auditLog->new_values)
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="fw-bold text-danger small mb-2">
                                    <i class="fa-solid fa-minus me-1"></i> Prior State (Old Values)
                                </div>
                                <pre class="p-3 bg-light rounded border font-monospace text-dark small" style="max-height: 400px; overflow-y: auto;">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-success small mb-2">
                                    <i class="fa-solid fa-plus me-1"></i> Modified State (New Values)
                                </div>
                                <pre class="p-3 bg-light rounded border font-monospace text-dark small" style="max-height: 400px; overflow-y: auto;">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </div>
                        </div>
                    @elseif($auditLog->new_values)
                        <div>
                            <div class="fw-bold text-success small mb-2">
                                <i class="fa-solid fa-plus me-1"></i> Created / Dispatched State (New Values)
                            </div>
                            <pre class="p-3 bg-light rounded border font-monospace text-dark small" style="max-height: 450px; overflow-y: auto;">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    @elseif($auditLog->old_values)
                        <div>
                            <div class="fw-bold text-danger small mb-2">
                                <i class="fa-solid fa-trash-can me-1"></i> Purged State (Old Values Prior to Deletion)
                            </div>
                            <pre class="p-3 bg-light rounded border font-monospace text-dark small" style="max-height: 450px; overflow-y: auto;">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    @else
                        <div class="p-4 bg-light rounded border text-center text-muted">
                            <i class="fa-solid fa-circle-info fa-2x mb-2 text-secondary opacity-50"></i>
                            <div class="fw-semibold">No tabular payload parameters recorded.</div>
                            <div class="small">Action executed as a stateful event trigger.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

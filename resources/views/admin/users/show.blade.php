@extends('layouts.admin')

@section('title', 'User Dossier - ' . $user->name . ' - TECH ERA COMPUTER')

@section('content')
<div class="container-fluid px-0">
    <!-- Top Header -->
    <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 64px; height: 64px; min-width: 64px;">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h3 class="fw-bold text-dark mb-0">{{ $user->name }}</h3>
                            @if($user->is_active)
                                <span class="badge bg-success-subtle text-success border border-success">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger">Deactivated</span>
                            @endif

                            @if($user->id === auth()->id())
                                <span class="badge bg-secondary-subtle text-secondary border">Your Account</span>
                            @endif
                        </div>
                        <div class="text-muted small font-monospace">
                            <i class="fa-solid fa-envelope me-1"></i> {{ $user->email }}
                            @if($user->mobile)
                                <span class="mx-2">•</span>
                                <i class="fa-solid fa-phone me-1"></i> {{ $user->mobile }}
                            @endif
                            <span class="mx-2">•</span>
                            <i class="fa-solid fa-calendar me-1"></i> Member since {{ $user->created_at ? $user->created_at->format('M Y') : '-' }}
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary shadow-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Users Hub
                    </a>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary shadow-sm">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Profile
                    </a>
                    <button type="button" class="btn btn-outline-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                        <i class="fa-solid fa-key me-1"></i> Reset Password
                    </button>
                    @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-{{ $user->is_active ? 'secondary' : 'success' }} shadow-sm">
                                <i class="fa-solid fa-{{ $user->is_active ? 'user-slash' : 'user-check' }} me-1"></i>
                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        <button type="button" class="btn btn-outline-danger shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                            <i class="fa-solid fa-trash-can me-1"></i> Delete
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Layout -->
    <div class="row g-4">
        <!-- Left Column: Particulars & Affiliation -->
        <div class="col-lg-5">
            <!-- Account Summary Card -->
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-id-card me-2 text-primary"></i> Account Particulars
                    </h5>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Account System ID:</span>
                            <span class="font-monospace fw-bold text-dark">#USR-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Primary Role:</span>
                            <span>
                                @if($user->role === 'super_admin')
                                    <span class="badge bg-danger">Super Administrator</span>
                                @elseif($user->role === 'admin')
                                    <span class="badge bg-primary">Administrator</span>
                                @elseif($user->role === 'centre')
                                    <span class="badge bg-info text-dark">Centre Director</span>
                                @elseif($user->role === 'staff')
                                    <span class="badge bg-warning text-dark">Staff / Faculty</span>
                                @else
                                    <span class="badge bg-secondary">Student</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Login Email:</span>
                            <span class="font-monospace text-dark">{{ $user->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Contact Mobile:</span>
                            <span class="font-monospace text-dark">{{ $user->mobile ?? 'Not Recorded' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Registration Timestamp:</span>
                            <span class="text-dark">{{ $user->created_at ? $user->created_at->format('d M, Y h:i A') : '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Last Updated:</span>
                            <span class="text-dark">{{ $user->updated_at ? $user->updated_at->format('d M, Y h:i A') : '-' }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Centre Affiliation Card -->
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-building-columns me-2 text-primary"></i> Franchise Affiliation
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($user->assignedCentre)
                        <div class="p-3 bg-light rounded border">
                            <div class="h6 fw-bold text-dark mb-1">{{ $user->assignedCentre->centre_name }}</div>
                            <div class="small text-muted font-monospace mb-2">Code: {{ $user->assignedCentre->centre_code }}</div>
                            <div class="small text-muted mb-2">
                                <i class="fa-solid fa-location-dot me-1 text-danger"></i>
                                {{ $user->assignedCentre->city }}, {{ $user->assignedCentre->state ? $user->assignedCentre->state->name : 'India' }}
                            </div>
                            <a href="{{ route('admin.centres.show', $user->assignedCentre) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Centre Dossier
                            </a>
                        </div>
                    @elseif($user->role === 'super_admin' || $user->role === 'admin')
                        <div class="alert alert-info border-0 mb-0 small">
                            <i class="fa-solid fa-shield-halved me-1"></i> Corporate Headquarters: This administrative account holds unrestricted access across all nationwide branches.
                        </div>
                    @else
                        <div class="text-muted small">No specific franchise centre is associated with this account.</div>
                    @endif
                </div>
            </div>

            <!-- Student Profile Linkage -->
            @if($user->student)
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-graduation-cap me-2 text-primary"></i> Linked Student Dossier
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="p-3 bg-light rounded border">
                            <div class="h6 fw-bold text-dark mb-1">{{ $user->student->full_name }}</div>
                            <div class="small text-muted font-monospace mb-1">Roll No: {{ $user->student->roll_no }}</div>
                            <div class="small text-muted font-monospace mb-3">Reg No: {{ $user->student->registration_no }}</div>
                            <a href="{{ route('admin.students.show', $user->student) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Student Dossier
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: RBAC Permissions & Audit Log -->
        <div class="col-lg-7">
            <!-- Roles & Permissions Matrix -->
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-shield-halved me-2 text-primary"></i> Roles & Permissions Matrix
                    </h5>
                    <span class="badge bg-light text-dark border">{{ $user->roles->count() }} Roles Attached</span>
                </div>
                <div class="card-body p-4">
                    <!-- Assigned Roles -->
                    <div class="mb-3">
                        <label class="small text-muted fw-bold mb-2">Assigned Security Roles:</label>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse($user->roles as $r)
                                <span class="badge bg-primary px-3 py-2 fs-6">
                                    <i class="fa-solid fa-user-shield me-1"></i> {{ $r->display_name }}
                                    <span class="font-monospace small opacity-75">({{ $r->name }})</span>
                                </span>
                            @empty
                                <span class="badge bg-secondary px-3 py-2 fs-6">{{ ucfirst($user->role) }}</span>
                            @endforelse
                        </div>
                    </div>

                    <!-- Consolidated Permissions -->
                    <div>
                        <label class="small text-muted fw-bold mb-2">Effective Capabilities & Access:</label>
                        @if($user->isSuperAdmin())
                            <div class="alert alert-success border-0 small mb-0">
                                <div class="fw-bold mb-1"><i class="fa-solid fa-crown text-warning me-1"></i> Full System Super Administrator Privileges</div>
                                <div>This account possesses absolute, bypass authority across all system modules, financial ledgers, audit logs, and settings.</div>
                            </div>
                        @else
                            @php
                                $allPerms = collect();
                                foreach($user->roles as $r) {
                                    $allPerms = $allPerms->merge($r->permissions);
                                }
                                $groupedPerms = $allPerms->unique('id')->groupBy('module');
                            @endphp

                            @if($groupedPerms->isNotEmpty())
                                <div class="row g-2">
                                    @foreach($groupedPerms as $mod => $perms)
                                        <div class="col-md-6">
                                            <div class="p-2 bg-light rounded border h-100">
                                                <div class="fw-bold text-dark small text-uppercase mb-1">
                                                    <i class="fa-solid fa-folder-open me-1 text-secondary"></i> {{ $mod }}
                                                </div>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($perms as $p)
                                                        <span class="badge bg-white text-dark border small">{{ $p->display_name }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-muted small">Standard role-based access for {{ ucfirst($user->role) }}.</div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Administrative Audit Trail -->
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i> Activity & Security Audit Trail
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-2">Timestamp</th>
                                    <th>Action Type</th>
                                    <th>Performed By</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($auditLogs as $log)
                                    <tr>
                                        <td class="ps-2 font-monospace small text-muted">
                                            {{ $log->created_at ? $log->created_at->format('d M, Y H:i') : '-' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-monospace small">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="small">
                                            {{ $log->user ? $log->user->name : 'System' }}
                                        </td>
                                        <td class="font-monospace small text-muted">
                                            {{ $log->ip_address ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">
                                            No recent administrative audit records captured for this account.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fa-solid fa-key me-2 text-warning"></i> Reset User Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">
                @csrf
                <div class="modal-body py-3">
                    <p class="text-muted small mb-3">
                        Set a new password for <strong>{{ $user->name }}</strong> ({{ $user->email }}). The user can use this password on their next login attempt.
                    </p>
                    <div class="mb-3">
                        <label for="modal_password" class="form-label small fw-bold">New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="modal_password" class="form-control" placeholder="Minimum 8 characters" required>
                    </div>
                    <div class="mb-2">
                        <label for="modal_password_confirmation" class="form-label small fw-bold">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="modal_password_confirmation" class="form-control" placeholder="Repeat new password" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-check me-1"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
@if($user->id !== auth()->id())
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Delete User Account
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <p class="mb-2">Are you sure you want to permanently delete this user account?</p>
                    <div class="p-2 bg-light rounded border mb-2">
                        <div class="fw-bold">{{ $user->name }}</div>
                        <div class="small text-muted font-monospace">{{ $user->email }}</div>
                        <div class="small text-muted">Role: <span class="badge bg-secondary">{{ $user->role }}</span></div>
                    </div>
                    <p class="small text-danger mb-0">This action permanently purges the user credentials and detached roles.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Confirm Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

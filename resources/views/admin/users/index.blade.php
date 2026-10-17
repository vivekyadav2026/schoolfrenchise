@extends('layouts.admin')

@section('title', 'User & Access Management - TECH ERA COMPUTER')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">User & Access Management</h2>
            <p class="text-muted small mb-0">Manage system administrators, centre franchise directors, faculty staff, and student logins.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-user-plus me-1"></i> Add New User
            </a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Total Users</div>
                    <div class="h4 fw-bold text-dark mb-0">{{ number_format($stats['total_users']) }}</div>
                    <div class="text-muted small mt-1">All accounts across roles</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Administrators</div>
                    <div class="h4 fw-bold text-danger mb-0">{{ number_format($stats['total_admins']) }}</div>
                    <div class="text-muted small mt-1">Super Admin & Admin officers</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Centres & Staff</div>
                    <div class="h4 fw-bold text-info mb-0">{{ number_format($stats['total_centres'] + $stats['total_staff']) }}</div>
                    <div class="text-muted small mt-1">{{ $stats['total_centres'] }} Directors | {{ $stats['total_staff'] }} Faculty</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Active Accounts</div>
                    <div class="h4 fw-bold text-success mb-0">{{ number_format($stats['active_users']) }}</div>
                    <div class="text-muted small mt-1">{{ $stats['inactive_users'] }} deactivated</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, email, phone..." value="{{ request('search') }}">
                </div>

                <div class="col-md-2">
                    <select name="role" class="form-select form-select-sm">
                        <option value="">All Roles</option>
                        <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="centre" {{ request('role') == 'centre' ? 'selected' : '' }}>Centre / Franchise</option>
                        <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff / Faculty</option>
                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="centre_id" class="form-select form-select-sm">
                        <option value="">All Study Centres</option>
                        @foreach($centres as $cnt)
                            <option value="{{ $cnt->id }}" {{ request('centre_id') == $cnt->id ? 'selected' : '' }}>{{ $cnt->centre_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Deactivated</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Apply Filter"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                    @if(request()->hasAny(['search', 'role', 'centre_id', 'status']))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light border" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">User Particulars</th>
                        <th>Role & Access</th>
                        <th>Centre Branch</th>
                        <th>Contact Mobile</th>
                        <th>Status</th>
                        <th>Created On</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold fs-6" style="width: 38px; height: 38px; min-width: 38px;">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">
                                            <a href="{{ route('admin.users.show', $u) }}" class="text-dark text-decoration-none hover-primary">
                                                {{ $u->name }}
                                            </a>
                                            @if($u->id === auth()->id())
                                                <span class="badge bg-secondary-subtle text-secondary border ms-1" style="font-size: 0.65rem;">You</span>
                                            @endif
                                        </div>
                                        <div class="small text-muted font-monospace">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($u->role === 'super_admin')
                                    <span class="badge bg-danger">Super Admin</span>
                                @elseif($u->role === 'admin')
                                    <span class="badge bg-primary">Admin</span>
                                @elseif($u->role === 'centre')
                                    <span class="badge bg-info text-dark">Centre Director</span>
                                @elseif($u->role === 'staff')
                                    <span class="badge bg-warning text-dark">Staff / Faculty</span>
                                @else
                                    <span class="badge bg-secondary">Student</span>
                                @endif

                                @if($u->roles->count() > 1)
                                    <span class="badge bg-light text-muted border ms-1">+{{ $u->roles->count() - 1 }} role</span>
                                @endif
                            </td>
                            <td>
                                @if($u->assignedCentre)
                                    <div class="small fw-semibold text-dark">{{ $u->assignedCentre->centre_name }}</div>
                                    <div class="small text-muted font-monospace">{{ $u->assignedCentre->centre_code }}</div>
                                @elseif($u->role === 'super_admin' || $u->role === 'admin')
                                    <span class="text-muted small fst-italic">Headquarters (All)</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="font-monospace small">
                                {{ $u->mobile ?? '-' }}
                            </td>
                            <td>
                                @if($u->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger">Deactivated</span>
                                @endif
                            </td>
                            <td class="font-monospace small text-muted">
                                {{ $u->created_at ? $u->created_at->format('d M, Y') : '-' }}
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.users.show', $u) }}" class="btn btn-outline-secondary" title="View Dossier">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-outline-primary" title="Edit User">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    @if($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.toggle-status', $u) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-{{ $u->is_active ? 'warning' : 'success' }}" title="{{ $u->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fa-solid fa-{{ $u->is_active ? 'user-slash' : 'user-check' }}"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $u->id }}" title="Delete User">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endif
                                </div>

                                <!-- Delete Confirmation Modal -->
                                @if($u->id !== auth()->id())
                                    <div class="modal fade text-start" id="deleteUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
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
                                                        <div class="fw-bold">{{ $u->name }}</div>
                                                        <div class="small text-muted font-monospace">{{ $u->email }}</div>
                                                        <div class="small text-muted">Role: <span class="badge bg-secondary">{{ $u->role }}</span></div>
                                                    </div>
                                                    <p class="small text-danger mb-0">This action cannot be undone.</p>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Confirm Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-users fa-3x mb-3 text-secondary opacity-50"></i>
                                <div class="fw-bold">No user accounts found.</div>
                                <div class="small">Try adjusting search parameters or create a new user.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Edit User - ' . $user->name . ' - TECH ERA COMPUTER')

@section('content')
<div class="container-fluid px-0" style="max-width: 900px;">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Edit User Account: {{ $user->name }}</h2>
            <p class="text-muted small mb-0">Update profile particulars, security roles, and franchise branch scoping.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-eye me-1"></i> View Dossier
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-light border shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> All Users
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <!-- Section 1: Basic Information -->
                <div class="border-bottom pb-3 mb-4">
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-user me-2 text-primary"></i> 1. Personal & Contact Details
                    </h5>
                    <p class="text-muted small mb-0">Essential identity and communication information.</p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label small fw-bold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="mobile" class="form-label small fw-bold">Mobile Number</label>
                        <input type="text" name="mobile" id="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $user->mobile) }}">
                        @error('mobile')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 d-flex align-items-center pt-md-4">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            <label class="form-check-label fw-semibold text-dark small" for="is_active">
                                Active Account (Allow Login)
                            </label>
                            @if($user->id === auth()->id())
                                <input type="hidden" name="is_active" value="1">
                                <div class="form-text small text-muted">You cannot deactivate your own active session.</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Section 2: Role & Centre Scoping -->
                <div class="border-bottom pb-3 mb-4">
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-shield-halved me-2 text-primary"></i> 2. Role & Institutional Affiliation
                    </h5>
                    <p class="text-muted small mb-0">Define privileges, access scope, and assigned franchise branch.</p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="role" class="form-label small fw-bold">Primary Access Role <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required onchange="toggleCentreField(this.value)">
                            <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>Super Administrator</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="centre" {{ old('role', $user->role) == 'centre' ? 'selected' : '' }}>Centre / Franchise Director</option>
                            <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Faculty / Branch Staff</option>
                            <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6" id="centre_wrapper">
                        <label for="centre_id" class="form-label small fw-bold">Assigned Study Centre</label>
                        <select name="centre_id" id="centre_id" class="form-select @error('centre_id') is-invalid @enderror">
                            <option value="">-- Select Study Centre --</option>
                            @foreach($centres as $c)
                                <option value="{{ $c->id }}" {{ old('centre_id', $user->centre_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->centre_name }} ({{ $c->centre_code }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small">Mandatory for Centre Directors and Branch Staff accounts.</div>
                        @error('centre_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($roles->isNotEmpty())
                        @php
                            $userRoleIds = $user->roles->pluck('id')->toArray();
                        @endphp
                        <div class="col-12">
                            <label class="form-label small fw-bold mb-2">Additional RBAC Roles (Optional)</label>
                            <div class="d-flex flex-wrap gap-3 p-3 bg-light rounded border">
                                @foreach($roles as $r)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="role_ids[]" value="{{ $r->id }}" id="role_{{ $r->id }}" {{ is_array(old('role_ids', $userRoleIds)) && in_array($r->id, old('role_ids', $userRoleIds)) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="role_{{ $r->id }}">
                                            <strong>{{ $r->display_name }}</strong> <span class="text-muted font-monospace">({{ $r->name }})</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Section 3: Credentials (Optional for Edit) -->
                <div class="border-bottom pb-3 mb-4">
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-key me-2 text-primary"></i> 3. Change Password (Optional)
                    </h5>
                    <p class="text-muted small mb-0">Leave fields blank if you do not wish to modify the existing password.</p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="password" class="form-label small fw-bold">New Password</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Leave empty to retain current password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label small fw-bold">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Repeat new password">
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-light border px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-check me-1"></i> Update Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleCentreField(role) {
    const centreWrapper = document.getElementById('centre_wrapper');
    if (role === 'centre' || role === 'staff') {
        centreWrapper.style.display = 'block';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    toggleCentreField(document.getElementById('role').value);
});
</script>
@endpush
@endsection

@extends('layouts.admin')

@section('title', 'Add New User - TECH ERA COMPUTER')

@section('content')
<div class="container-fluid px-0" style="max-width: 900px;">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Provision New User Account</h2>
            <p class="text-muted small mb-0">Create administrative, franchise centre director, faculty staff, or student accounts.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Users
        </a>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

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
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Ramesh Kumar" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="e.g. staff@example.com" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="mobile" class="form-label small fw-bold">Mobile Number</label>
                        <input type="text" name="mobile" id="mobile" class="form-control @error('mobile') is-invalid @enderror" placeholder="e.g. 9876543210" value="{{ old('mobile') }}">
                        @error('mobile')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 d-flex align-items-center pt-md-4">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-dark small" for="is_active">
                                Active Account (Allow immediate login)
                            </label>
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
                            <option value="">-- Select Primary Role --</option>
                            <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Administrator</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="centre" {{ old('role') == 'centre' ? 'selected' : '' }}>Centre / Franchise Director</option>
                            <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Faculty / Branch Staff</option>
                            <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
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
                                <option value="{{ $c->id }}" {{ old('centre_id') == $c->id ? 'selected' : '' }}>
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
                        <div class="col-12">
                            <label class="form-label small fw-bold mb-2">Additional RBAC Roles (Optional)</label>
                            <div class="d-flex flex-wrap gap-3 p-3 bg-light rounded border">
                                @foreach($roles as $r)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="role_ids[]" value="{{ $r->id }}" id="role_{{ $r->id }}" {{ is_array(old('role_ids')) && in_array($r->id, old('role_ids')) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="role_{{ $r->id }}">
                                            <strong>{{ $r->display_name }}</strong> <span class="text-muted font-monospace">({{ $r->name }})</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Section 3: Credentials -->
                <div class="border-bottom pb-3 mb-4">
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-key me-2 text-primary"></i> 3. Security Credentials
                    </h5>
                    <p class="text-muted small mb-0">Initial authentication password for this user.</p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="password" class="form-label small fw-bold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimum 8 characters" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label small fw-bold">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Repeat password exactly" required>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light border px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-check me-1"></i> Provision Account
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

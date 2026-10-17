@extends('layouts.admin')

@section('title', 'Register New Student - TECH ERA COMPUTER ERP')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}" class="text-decoration-none">Students</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add New Student</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Register New Student</h3>
        <p class="text-muted small mb-0">Create a computerized student profile, generate unique roll & registration number, and create portal access.</p>
    </div>
    <div>
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Student List
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.students.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- Centre & Academic Assignment -->
        <div class="col-12">
            <div class="card dce-table-card">
                <div class="card-header bg-light">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-building-columns me-2"></i>Study Centre Assignment</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Allocated Study Centre <span class="text-danger">*</span></label>
                            <select name="centre_id" class="form-select @error('centre_id') is-invalid @enderror" required>
                                <option value="">-- Select Authorised Centre --</option>
                                @foreach($centres as $c)
                                    <option value="{{ $c->id }}" {{ old('centre_id') == $c->id ? 'selected' : '' }}>
                                        [{{ $c->centre_code }}] {{ $c->centre_name }} - {{ $c->city }}, {{ $c->state?->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('centre_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text small">Student Roll and Registration numbers will be auto-generated based on the chosen centre code.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Highest Prior Qualification</label>
                            <input type="text" name="highest_qualification" class="form-control @error('highest_qualification') is-invalid @enderror" 
                                   placeholder="e.g. 10th / 12th / BCA / B.Sc / Graduate" value="{{ old('highest_qualification') }}">
                            @error('highest_qualification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Details -->
        <div class="col-lg-7">
            <div class="card dce-table-card h-100">
                <div class="card-header bg-light">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-user me-2"></i>Personal Particulars</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Full Name (as per Matriculation Certificate) <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" 
                                   placeholder="e.g. Amit Kumar Sharma" value="{{ old('full_name') }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Father's / Guardian's Name <span class="text-danger">*</span></label>
                            <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror" 
                                   placeholder="e.g. Ramesh Kumar" value="{{ old('father_name') }}" required>
                            @error('father_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mother's Name</label>
                            <input type="text" name="mother_name" class="form-control @error('mother_name') is-invalid @enderror" 
                                   placeholder="e.g. Sunita Devi" value="{{ old('mother_name') }}">
                            @error('mother_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" 
                                   value="{{ old('dob') }}" required>
                            @error('dob')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="">-- Select Gender --</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mobile Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">+91</span>
                                <input type="tel" name="mobile" maxlength="10" class="form-control @error('mobile') is-invalid @enderror" 
                                       placeholder="10 digit mobile" value="{{ old('mobile') }}" required>
                            </div>
                            <div class="form-text small">This mobile number is used as the initial student portal login password.</div>
                            @error('mobile')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alternate Mobile / WhatsApp</label>
                            <input type="tel" name="alternate_mobile" maxlength="20" class="form-control @error('alternate_mobile') is-invalid @enderror" 
                                   placeholder="Optional alternate contact" value="{{ old('alternate_mobile') }}">
                            @error('alternate_mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   placeholder="e.g. student@gmail.com" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address & Identification Documents -->
        <div class="col-lg-5">
            <div class="card dce-table-card mb-4">
                <div class="card-header bg-light">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-map-location-dot me-2"></i>Residential Address</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                            <select name="state_id" id="stateSelect" class="form-select @error('state_id') is-invalid @enderror" required>
                                <option value="">-- Select State --</option>
                                @foreach($states as $s)
                                    <option value="{{ $s->id }}" {{ old('state_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('state_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">District <span class="text-danger">*</span></label>
                            <select name="district_id" id="districtSelect" class="form-select @error('district_id') is-invalid @enderror" required>
                                <option value="">-- Select District (Select State first) --</option>
                            </select>
                            @error('district_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-7">
                            <label class="form-label fw-semibold">City / Town / Village <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" 
                                   value="{{ old('city') }}" placeholder="City name" required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-semibold">PIN Code <span class="text-danger">*</span></label>
                            <input type="text" name="pincode" maxlength="6" class="form-control @error('pincode') is-invalid @enderror" 
                                   value="{{ old('pincode') }}" placeholder="6-digit PIN" required>
                            @error('pincode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Full Postal Address <span class="text-danger">*</span></label>
                            <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror" 
                                      placeholder="House / Street / Landmark" required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- KYC & Photo Uploads -->
            <div class="card dce-table-card">
                <div class="card-header bg-light">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-camera me-2"></i>Photo & Documents</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Passport Photo (JPEG/PNG, Max 2MB)</label>
                        <input type="file" name="photo" accept="image/*" class="form-control @error('photo') is-invalid @enderror">
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Student Signature (JPEG/PNG, Max 2MB)</label>
                        <input type="file" name="signature" accept="image/*" class="form-control @error('signature') is-invalid @enderror">
                        @error('signature')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label fw-semibold">ID Proof / Aadhaar / Marksheet (PDF/Image, Max 5MB)</label>
                        <input type="file" name="id_proof" accept=".pdf,image/*" class="form-control @error('id_proof') is-invalid @enderror">
                        @error('id_proof')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Controls -->
        <div class="col-12 text-end">
            <hr class="my-3">
            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary px-4 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                <i class="fa-solid fa-check me-2"></i>Save & Register Student
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stateSelect = document.getElementById('stateSelect');
    const districtSelect = document.getElementById('districtSelect');
    const oldDistrictId = "{{ old('district_id') }}";

    function loadDistricts(stateId, selectedId = null) {
        if (!stateId) {
            districtSelect.innerHTML = '<option value="">-- Select District (Select State first) --</option>';
            return;
        }
        districtSelect.innerHTML = '<option value="">Loading districts...</option>';
        fetch('/api/districts/' + stateId)
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">-- Select District --</option>';
                data.forEach(d => {
                    const sel = (selectedId && selectedId == d.id) ? 'selected' : '';
                    options += `<option value="${d.id}" ${sel}>${d.name}</option>`;
                });
                districtSelect.innerHTML = options;
            })
            .catch(err => {
                console.error('Error fetching districts:', err);
                districtSelect.innerHTML = '<option value="">Error loading districts</option>';
            });
    }

    if (stateSelect.value) {
        loadDistricts(stateSelect.value, oldDistrictId);
    }

    stateSelect.addEventListener('change', function() {
        loadDistricts(this.value);
    });
});
</script>
@endpush
@endsection

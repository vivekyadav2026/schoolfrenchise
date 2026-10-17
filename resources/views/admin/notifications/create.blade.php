@extends('layouts.admin')

@section('title', 'Compose Broadcast Notification')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.notifications.index') }}">Notifications</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Compose Broadcast</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-paper-plane text-primary me-2"></i>Compose & Dispatch Broadcast
            </h2>
            <p class="text-muted small mb-0">Send instant in-app alerts and notifications to students, franchise centres, or specific users.</p>
        </div>
        <div>
            <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Notifications
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-2"></i>Please correct the following errors:</h6>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-bullhorn me-2 text-primary"></i>Broadcast Particulars</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.notifications.store') }}" method="POST">
                        @csrf

                        <!-- Target Audience -->
                        <div class="mb-4">
                            <label class="form-label small fw-semibold">Target Audience Group <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-sm-6 col-md-4">
                                    <div class="form-check border rounded p-2 ps-4">
                                        <input class="form-check-input" type="radio" name="target_group" id="target_all" value="all" {{ old('target_group', 'all') == 'all' ? 'checked' : '' }} onchange="toggleTargetFields()">
                                        <label class="form-check-label small fw-semibold" for="target_all">
                                            <i class="fa-solid fa-users text-primary me-1"></i> All Users
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="form-check border rounded p-2 ps-4">
                                        <input class="form-check-input" type="radio" name="target_group" id="target_centres" value="centres" {{ old('target_group') == 'centres' ? 'checked' : '' }} onchange="toggleTargetFields()">
                                        <label class="form-check-label small fw-semibold" for="target_centres">
                                            <i class="fa-solid fa-building-columns text-info me-1"></i> All Centres / Staff
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="form-check border rounded p-2 ps-4">
                                        <input class="form-check-input" type="radio" name="target_group" id="target_students" value="students" {{ old('target_group') == 'students' ? 'checked' : '' }} onchange="toggleTargetFields()">
                                        <label class="form-check-label small fw-semibold" for="target_students">
                                            <i class="fa-solid fa-user-graduate text-success me-1"></i> All Students
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <div class="form-check border rounded p-2 ps-4">
                                        <input class="form-check-input" type="radio" name="target_group" id="target_specific_centre" value="specific_centre" {{ old('target_group') == 'specific_centre' ? 'checked' : '' }} onchange="toggleTargetFields()">
                                        <label class="form-check-label small fw-semibold" for="target_specific_centre">
                                            <i class="fa-solid fa-school text-warning me-1"></i> Specific Study Centre
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <div class="form-check border rounded p-2 ps-4">
                                        <input class="form-check-input" type="radio" name="target_group" id="target_specific_user" value="specific_user" {{ old('target_group') == 'specific_user' ? 'checked' : '' }} onchange="toggleTargetFields()">
                                        <label class="form-check-label small fw-semibold" for="target_specific_user">
                                            <i class="fa-solid fa-user text-secondary me-1"></i> Specific Individual User
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Centre Selector (Conditional) -->
                        <div class="mb-3" id="centre_selector" style="display: {{ old('target_group') == 'specific_centre' ? 'block' : 'none' }};">
                            <label for="centre_id" class="form-label small fw-semibold">Select Franchise Centre <span class="text-danger">*</span></label>
                            <select class="form-select @error('centre_id') is-invalid @enderror" id="centre_id" name="centre_id">
                                <option value="">-- Choose Study Centre --</option>
                                @foreach($centres as $c)
                                    <option value="{{ $c->id }}" {{ old('centre_id') == $c->id ? 'selected' : '' }}>
                                        [{{ $c->centre_code }}] {{ $c->centre_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('centre_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- User Selector (Conditional) -->
                        <div class="mb-3" id="user_selector" style="display: {{ old('target_group') == 'specific_user' ? 'block' : 'none' }};">
                            <label for="user_id" class="form-label small fw-semibold">Select Recipient User <span class="text-danger">*</span></label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                                <option value="">-- Choose User Account --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->email }}) [{{ strtoupper($u->role) }}]
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label for="title" class="form-label small fw-semibold">Notification Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. Important: Online Exam Server Maintenance Tonight" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="type" class="form-label small fw-semibold">Category Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>General Information</option>
                                    <option value="alert" {{ old('type') == 'alert' ? 'selected' : '' }}>Urgent Alert</option>
                                    <option value="fee" {{ old('type') == 'fee' ? 'selected' : '' }}>Fee & Dues Reminder</option>
                                    <option value="exam" {{ old('type') == 'exam' ? 'selected' : '' }}>Examination Notice</option>
                                    <option value="certificate" {{ old('type') == 'certificate' ? 'selected' : '' }}>Certificate Dispatch</option>
                                    <option value="system" {{ old('type') == 'system' ? 'selected' : '' }}>System Update</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label small fw-semibold">Message Body <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="4" placeholder="Enter clear, concise broadcast text..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="action_url" class="form-label small fw-semibold">Action URL / Portal Link (Optional)</label>
                            <input type="text" class="form-control @error('action_url') is-invalid @enderror" id="action_url" name="action_url" value="{{ old('action_url') }}" placeholder="e.g. /student/exams or /centre/admissions">
                            <div class="form-text small">Users clicking the notification will be navigated directly to this destination.</div>
                            @error('action_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Integration Channels -->
                        <div class="card bg-light border-0 rounded-3 mb-4 p-3">
                            <h6 class="fw-bold text-dark small mb-2"><i class="fa-solid fa-tower-broadcast me-2 text-primary"></i>Multi-Channel Dispatch Options</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="send_email" name="send_email" value="1" {{ old('send_email') ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="send_email">Queue Email Notification</label>
                                    </div>
                                    <div class="text-muted small ps-4">Send copy to registered email inbox.</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="send_whatsapp" name="send_whatsapp" value="1" {{ old('send_whatsapp') ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="send_whatsapp">WhatsApp Gateway Sync</label>
                                    </div>
                                    <div class="text-muted small ps-4">Trigger automated WhatsApp alert to mobile number.</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="fa-solid fa-paper-plane me-1"></i> Send Broadcast
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleTargetFields() {
    var group = document.querySelector('input[name="target_group"]:checked').value;
    var centreBox = document.getElementById('centre_selector');
    var userBox = document.getElementById('user_selector');

    if (group === 'specific_centre') {
        centreBox.style.display = 'block';
        userBox.style.display = 'none';
    } else if (group === 'specific_user') {
        centreBox.style.display = 'none';
        userBox.style.display = 'block';
    } else {
        centreBox.style.display = 'none';
        userBox.style.display = 'none';
    }
}
</script>
@endsection

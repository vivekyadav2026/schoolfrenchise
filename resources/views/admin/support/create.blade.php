@extends('layouts.admin')

@section('title', 'Create Support Ticket')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.support.index') }}">Support</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-plus-circle text-primary me-2"></i>Create New Support Ticket
            </h2>
            <p class="text-muted small mb-0">Log a new support query for a franchise branch, student issue, or central escalation.</p>
        </div>
        <div>
            <a href="{{ route('admin.support.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Helpdesk
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Please correct the following errors:</h6>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.support.store') }}" enctype="multipart/form-data" class="card border-0 shadow-sm rounded-3 bg-white">
                @csrf
                <div class="card-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Related Centre / Franchise (Optional)</label>
                            <select name="centre_id" class="form-select @error('centre_id') is-invalid @enderror">
                                <option value="">-- Central / General Enquiry --</option>
                                @foreach($centres as $cnt)
                                    <option value="{{ $cnt->id }}" {{ old('centre_id') == $cnt->id ? 'selected' : '' }}>
                                        {{ $cnt->centre_name }} ({{ $cnt->centre_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('centre_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Assign Staff Officer</label>
                            <select name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror">
                                <option value="">-- Leave Unassigned (Queue) --</option>
                                @foreach($staffMembers as $stf)
                                    <option value="{{ $stf->id }}" {{ old('assigned_to') == $stf->id ? 'selected' : '' }}>
                                        {{ $stf->name }} ({{ ucfirst($stf->role) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Technical / Portal Issue</option>
                                <option value="academic" {{ old('category') === 'academic' ? 'selected' : '' }}>Academic & Syllabus Query</option>
                                <option value="billing" {{ old('category') === 'billing' ? 'selected' : '' }}>Billing & Wallet Dispute</option>
                                <option value="exam" {{ old('category') === 'exam' ? 'selected' : '' }}>Exam & Marks Verification</option>
                                <option value="certificate" {{ old('category') === 'certificate' ? 'selected' : '' }}>Certificate Dispatch / Correction</option>
                                <option value="franchise" {{ old('category') === 'franchise' ? 'selected' : '' }}>Franchise Partnership / Renewal</option>
                                <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>General Query</option>
                            </select>
                            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Priority Level <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium Priority</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High Priority</option>
                                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent Escalation</option>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low Priority</option>
                            </select>
                            @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Subject / Query Summary <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}" required placeholder="e.g. Discrepancy in Student Marksheet Roll #20260101">
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Detailed Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5" required placeholder="Provide complete facts, student details, or error messages...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Attach File / Screenshot (Optional)</label>
                        <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
                        <div class="form-text small">PDF, PNG, JPG, WEBP up to 5MB.</div>
                        @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.support.index') }}" class="btn btn-light border px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fa-solid fa-paper-plane me-1"></i> Submit Ticket
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

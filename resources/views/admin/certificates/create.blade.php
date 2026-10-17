@extends('layouts.admin')

@section('title', 'Issue Certificate & Marksheet')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a;">
                <i class="fa-solid fa-stamp me-2" style="color: #0891b2;"></i>Issue Official Certificate & Marksheet
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.certificates.index') }}">Certificates</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Issue Certificate</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Certificates
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-file-shield me-2 text-primary"></i>Issuance Credentials Particulars
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.certificates.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="exam_result_id" class="form-label fw-semibold">Passed Candidate Evaluation <span class="text-danger">*</span></label>
                                <select class="form-select @error('exam_result_id') is-invalid @enderror" id="exam_result_id" name="exam_result_id" required>
                                    <option value="">Select Candidate with Passed Result</option>
                                    @foreach($eligibleResults as $res)
                                        <option value="{{ $res->id }}"
                                            data-student="{{ $res->student->full_name }}"
                                            data-roll="{{ $res->student->roll_no }}"
                                            data-reg="{{ $res->student->registration_no }}"
                                            data-course="{{ $res->course->course_name }}"
                                            data-centre="{{ $res->centre->centre_name ?? 'Head Office' }}"
                                            data-pct="{{ $res->percentage }}"
                                            data-grade="{{ $res->grade }}"
                                            {{ (old('exam_result_id') == $res->id || ($selectedResult && $selectedResult->id == $res->id)) ? 'selected' : '' }}>
                                            {{ $res->student->full_name }} (Roll: {{ $res->student->roll_no }}) - {{ $res->course->course_code }} - {{ $res->percentage }}% (Grade {{ $res->grade }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('exam_result_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Selected Candidate Synopsis -->
                            <div class="col-md-12">
                                <div class="card bg-light border-0 p-3 rounded-2" id="candidatePreviewCard">
                                    <div class="fw-bold text-dark fs-6" id="previewStudent">Candidate: None Selected</div>
                                    <div class="small text-muted mt-1" id="previewRollReg">Roll No: - | Reg No: -</div>
                                    <div class="small text-muted" id="previewCentre">Centre: -</div>
                                    <div class="small text-primary fw-semibold mt-1" id="previewCourse">Course: -</div>
                                    <div class="small fw-bold text-success mt-1" id="previewScore">Performance: -</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="certificate_no" class="form-label fw-semibold">Certificate Serial Number</label>
                                <input type="text" class="form-control @error('certificate_no') is-invalid @enderror font-monospace" id="certificate_no" name="certificate_no" value="{{ old('certificate_no') }}" placeholder="Leave blank to auto-generate (DCE/CERT/YYYY/XXXX)">
                                <div class="form-text small">Unique tamper-proof board registration identifier.</div>
                                @error('certificate_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="marksheet_no" class="form-label fw-semibold">Marksheet Serial Number</label>
                                <input type="text" class="form-control @error('marksheet_no') is-invalid @enderror font-monospace" id="marksheet_no" name="marksheet_no" value="{{ old('marksheet_no') }}" placeholder="Leave blank to auto-generate (DCE/MS/YYYY/XXXX)">
                                <div class="form-text small">Unique marks statement registration serial.</div>
                                @error('marksheet_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="issue_date" class="form-label fw-semibold">Official Issuance Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('issue_date') is-invalid @enderror" id="issue_date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                                @error('issue_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.certificates.index') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4" style="background-color: #0891b2; border-color: #0891b2;">
                                <i class="fa-solid fa-stamp me-1"></i> Issue Certificate & Marksheet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('exam_result_id');
    const previewStudent = document.getElementById('previewStudent');
    const previewRollReg = document.getElementById('previewRollReg');
    const previewCentre = document.getElementById('previewCentre');
    const previewCourse = document.getElementById('previewCourse');
    const previewScore = document.getElementById('previewScore');

    function updatePreview() {
        const opt = select.options[select.selectedIndex];
        if (!opt || !opt.value) {
            previewStudent.textContent = 'Candidate: None Selected';
            previewRollReg.textContent = 'Roll No: - | Reg No: -';
            previewCentre.textContent = 'Centre: -';
            previewCourse.textContent = 'Course: -';
            previewScore.textContent = 'Performance: -';
            return;
        }

        previewStudent.textContent = 'Candidate: ' + (opt.dataset.student || 'Unknown');
        previewRollReg.textContent = `Roll No: ${opt.dataset.roll || 'N/A'} | Reg No: ${opt.dataset.reg || 'N/A'}`;
        previewCentre.textContent = 'Study Centre: ' + (opt.dataset.centre || 'Main Centre');
        previewCourse.textContent = 'Course Curriculum: ' + (opt.dataset.course || 'Unassigned');
        previewScore.textContent = `Performance: ${opt.dataset.pct}% (Grade: ${opt.dataset.grade})`;
    }

    select.addEventListener('change', updatePreview);
    if (select.value) {
        updatePreview();
    }
});
</script>
@endpush
@endsection

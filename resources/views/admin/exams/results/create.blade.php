@extends('layouts.admin')

@section('title', 'Enter Student Marks')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a;">
                <i class="fa-solid fa-marker me-2" style="color: #0891b2;"></i>Student Examination Marks Entry
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.exams.index') }}">Examinations</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.exams.results') }}">Results</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Enter Marks</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.exams.results') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Results
        </a>
    </div>

    <form method="POST" action="{{ route('admin.exams.results.store') }}" id="marksEntryForm">
        @csrf

        <div class="row g-4">
            <!-- Candidate & Exam Selection Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-user-graduate me-2 text-primary"></i>Candidate & Exam
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <label for="student_id" class="form-label fw-semibold">Select Student <span class="text-danger">*</span></label>
                            <select class="form-select @error('student_id') is-invalid @enderror" id="student_id" name="student_id" required>
                                <option value="">Select Enrolled Student</option>
                                @foreach($students as $st)
                                    @php
                                        $adm = $st->admissions->first();
                                        $courseId = $adm ? $adm->course_id : '';
                                        $courseName = $adm && $adm->course ? $adm->course->course_name : '';
                                    @endphp
                                    <option value="{{ $st->id }}" 
                                        data-course-id="{{ $courseId }}" 
                                        data-course-name="{{ $courseName }}" 
                                        data-centre="{{ $st->centre->centre_name ?? 'Head Office' }}"
                                        data-roll="{{ $st->roll_no }}"
                                        data-reg="{{ $st->registration_no }}"
                                        {{ (old('student_id') == $st->id || ($selectedStudent && $selectedStudent->id == $st->id)) ? 'selected' : '' }}>
                                        {{ $st->full_name }} (Roll: {{ $st->roll_no ?? 'Pending' }}) - {{ $courseName }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="exam_id" class="form-label fw-semibold">Exam Cycle (Optional)</label>
                            <select class="form-select @error('exam_id') is-invalid @enderror" id="exam_id" name="exam_id">
                                <option value="">General Term Assessment</option>
                                @foreach($exams as $ex)
                                    <option value="{{ $ex->id }}" 
                                        data-course-id="{{ $ex->course_id }}"
                                        {{ (old('exam_id') == $ex->id || ($selectedExam && $selectedExam->id == $ex->id)) ? 'selected' : '' }}>
                                        {{ $ex->exam_title }} ({{ $ex->exam_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('exam_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <input type="hidden" name="course_id" id="course_id" value="{{ old('course_id', $selectedExam->course_id ?? ($selectedStudent && $selectedStudent->admissions->first() ? $selectedStudent->admissions->first()->course_id : '')) }}">

                        <!-- Selected Candidate Preview -->
                        <div class="card bg-light border-0 p-3 mt-3 rounded-2" id="studentPreviewCard">
                            <div class="fw-bold text-dark mb-1" id="previewName">Candidate: None Selected</div>
                            <div class="small text-muted mb-1" id="previewRollReg">Roll: - | Reg: -</div>
                            <div class="small text-muted mb-1" id="previewCentre">Centre: -</div>
                            <div class="small text-primary fw-semibold" id="previewCourse">Course: -</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subject Marks Grid -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-list-check me-2 text-primary"></i>Curriculum Paper Marks Breakdown
                        </h6>
                        <span class="badge bg-light text-secondary border" id="subjectsCountBadge">0 Subjects</span>
                    </div>
                    <div class="card-body p-3">
                        <div id="noSubjectsAlert" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-book-open fs-2 mb-2 opacity-50"></i>
                            <h6>Select a Student or Exam</h6>
                            <p class="small mb-0">The subject papers corresponding to the student's enrolled course will appear here automatically.</p>
                        </div>

                        <div id="subjectsContainer" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light text-muted small text-uppercase">
                                        <tr>
                                            <th>Subject Paper</th>
                                            <th style="width: 130px;">Theory</th>
                                            <th style="width: 130px;">Practical</th>
                                            <th style="width: 130px;">Viva</th>
                                            <th style="width: 110px;">Total</th>
                                            <th style="width: 90px;">Pass/Fail</th>
                                        </tr>
                                    </thead>
                                    <tbody id="subjectsTableBody">
                                        <!-- Dynamically generated rows -->
                                    </tbody>
                                    <tfoot class="table-light fw-bold">
                                        <tr>
                                            <td>Aggregate Total</td>
                                            <td colspan="3" class="text-end text-muted small">Max Marks: <span id="aggregateMax">0</span></td>
                                            <td><span id="aggregateObtained" class="text-primary fs-6">0.00</span></td>
                                            <td>
                                                <span id="aggregateStatus" class="badge bg-secondary">Pending</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Percentage: <span id="aggregatePercentage" class="text-dark fs-6">0.00%</span></td>
                                            <td colspan="3" class="text-end">Final Grade: <span id="aggregateGrade" class="badge bg-primary fs-6">F</span></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('admin.exams.results') }}" class="btn btn-light px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4" style="background-color: #0891b2; border-color: #0891b2;">
                                    <i class="fa-solid fa-check me-1"></i> Save & Publish Result
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const studentSelect = document.getElementById('student_id');
    const examSelect = document.getElementById('exam_id');
    const courseIdInput = document.getElementById('course_id');
    const previewName = document.getElementById('previewName');
    const previewRollReg = document.getElementById('previewRollReg');
    const previewCentre = document.getElementById('previewCentre');
    const previewCourse = document.getElementById('previewCourse');
    const noSubjectsAlert = document.getElementById('noSubjectsAlert');
    const subjectsContainer = document.getElementById('subjectsContainer');
    const subjectsTableBody = document.getElementById('subjectsTableBody');
    const subjectsCountBadge = document.getElementById('subjectsCountBadge');

    function calculateGrade(percentage) {
        if (percentage >= 85) return 'A+';
        if (percentage >= 70) return 'A';
        if (percentage >= 60) return 'B';
        if (percentage >= 50) return 'C';
        if (percentage >= 40) return 'D';
        return 'F';
    }

    function recalculateTotals() {
        let totalObtained = 0;
        let totalMax = 0;
        let allPassed = true;

        const rows = subjectsTableBody.querySelectorAll('tr');
        rows.forEach(row => {
            const th = parseFloat(row.querySelector('.theory-input')?.value || 0);
            const pr = parseFloat(row.querySelector('.practical-input')?.value || 0);
            const va = parseFloat(row.querySelector('.viva-input')?.value || 0);
            const maxMarks = parseFloat(row.dataset.maxMarks || 100);
            const minPass = parseFloat(row.dataset.minPass || 40);

            const rowTot = th + pr + va;
            row.querySelector('.row-total').textContent = rowTot.toFixed(2);

            const isPass = rowTot >= minPass;
            if (!isPass) allPassed = false;

            const badge = row.querySelector('.row-status-badge');
            if (badge) {
                badge.className = isPass ? 'badge bg-success-subtle text-success' : 'badge bg-danger-subtle text-danger';
                badge.textContent = isPass ? 'Pass' : 'Fail';
            }

            totalObtained += rowTot;
            totalMax += maxMarks;
        });

        document.getElementById('aggregateMax').textContent = totalMax.toFixed(2);
        document.getElementById('aggregateObtained').textContent = totalObtained.toFixed(2);

        const pct = totalMax > 0 ? (totalObtained / totalMax) * 100 : 0;
        document.getElementById('aggregatePercentage').textContent = pct.toFixed(2) + '%';
        const grade = calculateGrade(pct);
        document.getElementById('aggregateGrade').textContent = grade;

        const aggStatusBadge = document.getElementById('aggregateStatus');
        if (allPassed && pct >= 40) {
            aggStatusBadge.className = 'badge bg-success';
            aggStatusBadge.textContent = 'PASSED';
        } else {
            aggStatusBadge.className = 'badge bg-danger';
            aggStatusBadge.textContent = 'FAILED';
        }
    }

    function loadCourseSubjects(courseId) {
        if (!courseId) {
            noSubjectsAlert.style.display = 'block';
            subjectsContainer.style.display = 'none';
            subjectsCountBadge.textContent = '0 Subjects';
            return;
        }

        courseIdInput.value = courseId;

        fetch(`/admin/exams/course-subjects/${courseId}`)
            .then(res => res.json())
            .then(subjects => {
                subjectsTableBody.innerHTML = '';
                if (!subjects || subjects.length === 0) {
                    noSubjectsAlert.style.display = 'block';
                    noSubjectsAlert.innerHTML = '<i class="fa-solid fa-circle-exclamation fs-2 text-warning mb-2"></i><h6>No subjects configured</h6><p class="small text-muted">This course does not have subject papers configured in the curriculum.</p>';
                    subjectsContainer.style.display = 'none';
                    subjectsCountBadge.textContent = '0 Subjects';
                    return;
                }

                noSubjectsAlert.style.display = 'none';
                subjectsContainer.style.display = 'block';
                subjectsCountBadge.textContent = subjects.length + ' Subjects';

                subjects.forEach((sub, idx) => {
                    const tr = document.createElement('tr');
                    tr.dataset.maxMarks = sub.total_max || 100;
                    tr.dataset.minPass = sub.min_pass_marks || 40;

                    tr.innerHTML = `
                        <td>
                            <input type="hidden" name="subjects[${idx}][id]" value="${sub.id}">
                            <div class="fw-bold text-dark">${sub.subject_name}</div>
                            <div class="small text-muted font-monospace">${sub.subject_code} | Pass: ${sub.min_pass_marks || 40} / ${sub.total_max || 100}</div>
                        </td>
                        <td>
                            <input type="number" step="0.5" min="0" max="${sub.theory_max || 100}" name="subjects[${idx}][theory_obtained]" class="form-control form-control-sm theory-input" placeholder="Max ${sub.theory_max || 70}" value="0">
                        </td>
                        <td>
                            <input type="number" step="0.5" min="0" max="${sub.practical_max || 100}" name="subjects[${idx}][practical_obtained]" class="form-control form-control-sm practical-input" placeholder="Max ${sub.practical_max || 20}" value="0">
                        </td>
                        <td>
                            <input type="number" step="0.5" min="0" max="${sub.viva_max || 100}" name="subjects[${idx}][viva_obtained]" class="form-control form-control-sm viva-input" placeholder="Max ${sub.viva_max || 10}" value="0">
                        </td>
                        <td>
                            <span class="fw-bold text-dark row-total">0.00</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary row-status-badge">Pending</span>
                        </td>
                    `;
                    subjectsTableBody.appendChild(tr);
                });

                // Attach listeners
                subjectsTableBody.querySelectorAll('input').forEach(input => {
                    input.addEventListener('input', recalculateTotals);
                });

                recalculateTotals();
            })
            .catch(err => {
                console.error(err);
            });
    }

    studentSelect.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (!opt || !opt.value) {
            previewName.textContent = 'Candidate: None Selected';
            previewRollReg.textContent = 'Roll: - | Reg: -';
            previewCentre.textContent = 'Centre: -';
            previewCourse.textContent = 'Course: -';
            loadCourseSubjects(null);
            return;
        }

        previewName.textContent = 'Candidate: ' + opt.text.split(' (Roll:')[0];
        previewRollReg.textContent = `Roll: ${opt.dataset.roll || 'N/A'} | Reg: ${opt.dataset.reg || 'N/A'}`;
        previewCentre.textContent = 'Centre: ' + (opt.dataset.centre || 'Head Office');
        previewCourse.textContent = 'Course: ' + (opt.dataset.courseName || 'Unassigned');

        if (opt.dataset.courseId) {
            loadCourseSubjects(opt.dataset.courseId);
        }
    });

    examSelect.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (opt && opt.dataset.courseId && !studentSelect.value) {
            loadCourseSubjects(opt.dataset.courseId);
        }
    });

    // Trigger on page load if student or exam pre-selected
    if (studentSelect.value) {
        studentSelect.dispatchEvent(new Event('change'));
    } else if (examSelect.value) {
        examSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection

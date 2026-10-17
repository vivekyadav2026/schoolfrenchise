@extends('layouts.admin')

@section('title', 'Exam Result Dossier - ' . ($result->student->full_name ?? 'Student'))

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 d-print-none">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0" style="color: #0f172a;">Computerized Marksheet & Result Dossier</h4>
                @if($result->result_status === 'passed')
                    <span class="badge bg-success fs-6"><i class="fa-solid fa-circle-check me-1"></i> Passed</span>
                @elseif($result->result_status === 'failed')
                    <span class="badge bg-danger fs-6"><i class="fa-solid fa-circle-xmark me-1"></i> Failed</span>
                @else
                    <span class="badge bg-warning text-dark fs-6"><i class="fa-solid fa-pause me-1"></i> Withheld</span>
                @endif
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.exams.index') }}">Examinations</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.exams.results') }}">Results</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $result->student->roll_no ?? 'Result #' . $result->id }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="fa-solid fa-print me-1"></i> Print Marksheet
            </button>
            <a href="{{ route('admin.exams.results') }}" class="btn btn-outline-dark">
                <i class="fa-solid fa-arrow-left me-1"></i> All Results
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Marksheet Document -->
        <div class="col-lg-8">
            <div class="card border shadow-sm rounded-3 p-4 bg-white" id="printableMarksheet">
                <!-- Institution Header -->
                <div class="text-center border-bottom pb-3 mb-4">
                    <div class="d-flex justify-content-center align-items-center gap-2 mb-1">
                        @if(!empty($instituteSettings['logo_url']))
                            <img src="{{ $instituteSettings['logo_url'] }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" style="height: 40px; object-fit: contain;">
                        @else
                            <i class="fa-solid fa-graduation-cap fs-2 text-primary"></i>
                        @endif
                        <h4 class="fw-bold mb-0 text-dark font-monospace text-uppercase" style="letter-spacing: 1px;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h4>
                    </div>
                    <div class="text-muted small">{{ $instituteSettings['site_tagline'] ?? 'Building Digital Skills for the Future' }}</div>
                    <div class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 mt-2 text-uppercase fw-bold">
                        Official Statement of Marks
                    </div>
                </div>

                <!-- Student & Course Particulars Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted small fw-semibold" style="width: 140px;">Candidate Name:</td>
                                <td class="fw-bold text-dark">{{ $result->student->full_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small fw-semibold">Father's Name:</td>
                                <td class="fw-medium text-dark">{{ $result->student->father_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small fw-semibold">Roll Number:</td>
                                <td class="fw-bold text-primary font-monospace">{{ $result->student->roll_no ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small fw-semibold">Registration No:</td>
                                <td class="font-monospace text-dark">{{ $result->student->registration_no ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted small fw-semibold" style="width: 140px;">Course Curriculum:</td>
                                <td class="fw-bold text-dark">{{ $result->course->course_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small fw-semibold">Course Code:</td>
                                <td class="badge bg-light text-secondary border font-monospace">{{ $result->course->course_code ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small fw-semibold">Study Centre:</td>
                                <td class="text-dark">{{ $result->centre->centre_name ?? 'Main Head Office Centre' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small fw-semibold">Exam Session:</td>
                                <td class="text-dark">{{ $result->exam->session_name ?? 'Regular Cycle' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Subject-wise Marks Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle text-center mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="text-start ps-3" style="width: 35%;">Subject Paper</th>
                                <th>Max Marks</th>
                                <th>Theory</th>
                                <th>Practical</th>
                                <th>Viva</th>
                                <th>Total Obtained</th>
                                <th>Grade</th>
                                <th>Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($result->subjectResults as $subRes)
                                <tr>
                                    <td class="text-start ps-3">
                                        <div class="fw-semibold text-dark">{{ $subRes->subject->subject_name ?? 'Module Paper' }}</div>
                                        <small class="text-muted font-monospace">{{ $subRes->subject->subject_code ?? '' }}</small>
                                    </td>
                                    <td class="fw-semibold text-secondary">{{ number_format($subRes->max_marks, 0) }}</td>
                                    <td>{{ number_format($subRes->theory_obtained, 1) }}</td>
                                    <td>{{ number_format($subRes->practical_obtained, 1) }}</td>
                                    <td>{{ number_format($subRes->viva_obtained, 1) }}</td>
                                    <td class="fw-bold text-dark">{{ number_format($subRes->total_obtained, 1) }}</td>
                                    <td><span class="badge bg-light text-dark border font-monospace">{{ $subRes->grade }}</span></td>
                                    <td>
                                        @if($subRes->is_passed)
                                            <span class="text-success fw-bold small"><i class="fa-solid fa-check me-1"></i>Pass</span>
                                        @else
                                            <span class="text-danger fw-bold small"><i class="fa-solid fa-xmark me-1"></i>Fail</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-muted py-3">No individual subject entries found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td class="text-start ps-3">Aggregate Total</td>
                                <td>{{ number_format($result->total_max_marks, 0) }}</td>
                                <td colspan="3" class="text-muted small">Combined Score</td>
                                <td class="text-primary fs-6">{{ number_format($result->total_marks_obtained, 2) }}</td>
                                <td><span class="badge bg-secondary font-monospace">{{ $result->grade }}</span></td>
                                <td>
                                    @if($result->result_status === 'passed')
                                        <span class="badge bg-success">PASSED</span>
                                    @elseif($result->result_status === 'failed')
                                        <span class="badge bg-danger">FAILED</span>
                                    @else
                                        <span class="badge bg-warning text-dark">WITHHELD</span>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Aggregate Statistics Grid -->
                <div class="row g-3 p-3 bg-light rounded-3 mb-4">
                    <div class="col-sm-4 text-center">
                        <div class="text-muted small text-uppercase fw-semibold">Percentage Score</div>
                        <div class="fs-4 fw-bold text-primary">{{ number_format($result->percentage, 2) }}%</div>
                    </div>
                    <div class="col-sm-4 text-center border-start border-end">
                        <div class="text-muted small text-uppercase fw-semibold">Overall Grade</div>
                        <div class="fs-4 fw-bold text-dark font-monospace">{{ $result->grade }}</div>
                    </div>
                    <div class="col-sm-4 text-center">
                        <div class="text-muted small text-uppercase fw-semibold">Final Status</div>
                        <div class="fs-4 fw-bold {{ $result->result_status === 'passed' ? 'text-success' : ($result->result_status === 'failed' ? 'text-danger' : 'text-warning') }} text-uppercase">
                            {{ $result->result_status }}
                        </div>
                    </div>
                </div>

                <!-- Signatures & Authority -->
                <div class="row text-center mt-5 pt-3">
                    <div class="col-4">
                        <div class="border-top pt-2 small text-muted">Controller of Examination</div>
                    </div>
                    <div class="col-4">
                        <div class="border-top pt-2 small text-muted">Centre Director / In-Charge</div>
                    </div>
                    <div class="col-4">
                        <div class="border-top pt-2 small text-muted">Chairman / Executive Director</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions & Verification Meta -->
        <div class="col-lg-4 d-print-none">
            <!-- Approval & Status Management Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-stamp me-2 text-primary"></i>Board Result Status
                    </h6>
                </div>
                <div class="card-body p-3">
                    <form method="POST" action="{{ route('admin.exams.results.status', $result) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="result_status" class="form-label fw-semibold">Change Official Status</label>
                            <select name="result_status" id="result_status" class="form-select">
                                <option value="passed" {{ $result->result_status === 'passed' ? 'selected' : '' }}>Approved - PASSED</option>
                                <option value="failed" {{ $result->result_status === 'failed' ? 'selected' : '' }}>Evaluated - FAILED</option>
                                <option value="withheld" {{ $result->result_status === 'withheld' ? 'selected' : '' }}>Pending - WITHHELD</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" style="background-color: #0891b2; border-color: #0891b2;">
                            <i class="fa-solid fa-check-double me-1"></i> Update Result Status
                        </button>
                    </form>

                    <div class="border-top mt-3 pt-3 small text-muted">
                        <div>Approved by: <strong>{{ $result->approver->name ?? 'System Admin' }}</strong></div>
                        <div>Date: <strong>{{ $result->approved_at ? $result->approved_at->format('d M, Y h:i A') : 'Pending Approval' }}</strong></div>
                    </div>
                </div>
            </div>

            <!-- Student Profile Summary -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-address-card me-2 text-primary"></i>Student Quick Profile
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary fw-bold fs-4" style="width: 50px; height: 50px;">
                            {{ substr($result->student->first_name ?? 'S', 0, 1) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark">{{ $result->student->full_name ?? 'N/A' }}</div>
                            <small class="text-muted font-monospace">{{ $result->student->roll_no ?? 'N/A' }}</small>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.students.show', $result->student_id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-user me-1"></i> View Full Student Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

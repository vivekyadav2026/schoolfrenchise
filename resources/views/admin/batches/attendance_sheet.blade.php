@extends('layouts.admin')

@section('title', 'Daily Attendance Sheet - ' . $batch->batch_name . ' - TECH ERA COMPUTER')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.batches.index') }}" class="text-decoration-none">Batches</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.batches.show', $batch->id) }}" class="text-decoration-none">{{ $batch->batch_name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Attendance Sheet</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Daily Attendance Sheet</h3>
        <p class="text-muted small mb-0">Record daily classroom attendance for <strong class="text-primary">{{ $batch->batch_name }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.batches.show', $batch->id) }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Batch
        </a>
    </div>
</div>

<!-- Date Selector Card -->
<div class="card dce-table-card mb-4 border-top border-4 border-success">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.batches.attendance.sheet', $batch->id) }}" class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-dark font-monospace">[{{ $batch->centre?->centre_code }}]</span>
                    <span class="fw-bold text-dark">{{ $batch->batch_name }}</span>
                    <span class="text-muted small">({{ \Carbon\Carbon::parse($batch->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($batch->end_time)->format('h:i A') }})</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-calendar-day text-success"></i></span>
                    <input type="date" name="date" class="form-control" value="{{ $attendanceDate }}" max="{{ date('Y-m-d') }}">
                    <button type="submit" class="btn btn-outline-success">Change Date</button>
                </div>
            </div>

            <div class="col-md-3 text-md-end">
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-success" id="btnAllPresent">
                        <i class="fa-solid fa-check me-1"></i> All Present
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="btnAllAbsent">
                        <i class="fa-solid fa-xmark me-1"></i> All Absent
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Attendance Entry Form -->
<form method="POST" action="{{ route('admin.batches.attendance.mark', $batch->id) }}">
    @csrf
    <input type="hidden" name="attendance_date" value="{{ $attendanceDate }}">

    <div class="card dce-table-card">
        <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-clipboard-user text-primary me-2"></i>Class Roll Call — {{ \Carbon\Carbon::parse($attendanceDate)->format('d F Y (l)') }}
                <span class="badge bg-secondary-subtle text-secondary ms-2">{{ $batch->admissions->count() }} Students</span>
            </h6>
            @if($existingRecords->isNotEmpty())
                <span class="badge bg-success-subtle text-success">
                    <i class="fa-solid fa-check-double me-1"></i> Attendance previously saved for this date (updates will overwrite)
                </span>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead>
                        <tr>
                            <th style="width: 140px;">Roll No</th>
                            <th>Student Name</th>
                            <th style="width: 320px;">Attendance Status</th>
                            <th style="width: 250px;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batch->admissions as $adm)
                        @php
                            $st = $adm->student;
                            $existing = $existingRecords->get($st->id);
                            $currentStatus = $existing ? $existing->status : 'present';
                            $currentRemark = $existing ? $existing->remarks : '';
                        @endphp
                        <tr>
                            <td>
                                <div class="font-monospace fw-bold text-primary">{{ $st->roll_no }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $st->full_name }}</div>
                                <div class="text-muted small">Mobile: {{ $st->mobile }}</div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm w-100" role="group">
                                    <input type="radio" class="btn-check status-present" name="attendance[{{ $st->id }}]" id="att_present_{{ $st->id }}" value="present" {{ $currentStatus === 'present' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success" for="att_present_{{ $st->id }}">Present</label>

                                    <input type="radio" class="btn-check status-absent" name="attendance[{{ $st->id }}]" id="att_absent_{{ $st->id }}" value="absent" {{ $currentStatus === 'absent' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-danger" for="att_absent_{{ $st->id }}">Absent</label>

                                    <input type="radio" class="btn-check" name="attendance[{{ $st->id }}]" id="att_late_{{ $st->id }}" value="late" {{ $currentStatus === 'late' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-warning" for="att_late_{{ $st->id }}">Late</label>

                                    <input type="radio" class="btn-check" name="attendance[{{ $st->id }}]" id="att_leave_{{ $st->id }}" value="leave" {{ $currentStatus === 'leave' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-info" for="att_leave_{{ $st->id }}">Leave</label>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="remarks[{{ $st->id }}]" class="form-control form-control-sm" placeholder="Optional notes..." value="{{ $currentRemark }}">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-user-slash fa-2x mb-3 text-secondary"></i>
                                <p class="mb-0">No students are currently allocated to this batch.</p>
                                <a href="{{ route('admin.admissions.create') }}" class="btn btn-sm btn-primary mt-2">Enroll Students into Batch</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($batch->admissions->isNotEmpty())
        <div class="card-footer bg-white text-end py-3">
            <a href="{{ route('admin.batches.show', $batch->id) }}" class="btn btn-outline-secondary px-4 me-2">Cancel</a>
            <button type="submit" class="btn btn-success px-5 shadow-sm fw-semibold">
                <i class="fa-solid fa-floppy-disk me-2"></i>Save Daily Attendance
            </button>
        </div>
        @endif
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnAllPresent = document.getElementById('btnAllPresent');
    const btnAllAbsent = document.getElementById('btnAllAbsent');

    if (btnAllPresent) {
        btnAllPresent.addEventListener('click', function() {
            document.querySelectorAll('.status-present').forEach(radio => radio.checked = true);
        });
    }

    if (btnAllAbsent) {
        btnAllAbsent.addEventListener('click', function() {
            document.querySelectorAll('.status-absent').forEach(radio => radio.checked = true);
        });
    }
});
</script>
@endpush
@endsection

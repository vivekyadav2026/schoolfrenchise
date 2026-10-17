@extends('layouts.admin')

@section('title', 'Configure Instalment Schedule - ' . ($admission->student?->roll_no ?? $admission->id) . ' - TECH ERA COMPUTER')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.admissions.index') }}" class="text-decoration-none">Admissions</a></li>
                <li class="breadcrumb-item active" aria-current="page">Custom Instalments</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Configure Student Instalment Schedule</h3>
        <p class="text-muted small mb-0">Set custom installment terms, due dates, concession amounts, and approved adjustments for individual students.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.admissions.print', $admission->id) }}" target="_blank" class="btn btn-outline-primary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-print me-1"></i> View Admission Form
        </a>
        <a href="{{ route('admin.fees.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Fees
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Student Information Card -->
        <div class="card dce-table-card border-0 shadow-sm p-4 mb-4" style="background: linear-gradient(135deg, #eff6ff 0%, #f5f3ff 100%);">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <span class="badge bg-primary font-monospace px-3 py-1 mb-1">ROLL NO: {{ $admission->student?->roll_no ?? 'Unassigned' }}</span>
                    <h4 class="fw-bold text-dark mb-1">{{ $admission->student?->full_name }}</h4>
                    <div class="text-muted small">
                        Course: <strong class="text-primary">{{ $admission->course?->course_name }}</strong> &bull;
                        Centre: <strong>{{ $admission->centre?->centre_name }}</strong> &bull;
                        Session: {{ $admission->student?->academic_session ?? date('Y') }}
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="bg-white p-3 rounded-3 shadow-sm border text-center">
                        <div class="text-muted small">Current Total Paid</div>
                        <h4 class="fw-bold text-success mb-0">₹{{ number_format($admission->total_paid, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Schedule Form -->
        <div class="card dce-table-card border-0 shadow-sm p-4">
            <form method="POST" action="{{ route('admin.fees.schedule.store', $admission->id) }}" id="scheduleForm">
                @csrf

                <!-- Pricing & Concession Row -->
                <div class="row g-3 mb-4 p-3 bg-light rounded-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Standard Course Fee (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="course_fee" id="courseFeeInput" class="form-control fw-bold" 
                               value="{{ old('course_fee', $admission->course_fee) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Concession / Discount (₹)</label>
                        <input type="number" step="0.01" name="concession_amount" id="concessionInput" class="form-control text-danger fw-bold" 
                               value="{{ old('concession_amount', $admission->discount_amount) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Net Payable Fee (₹)</label>
                        <input type="text" id="netFeeDisplay" class="form-control bg-white fw-bold text-primary" readonly 
                               value="₹{{ number_format($admission->net_fee, 2) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Approved Concession / Adjustment Reason</label>
                        <input type="text" name="adjustment_reason" class="form-control" 
                               placeholder="e.g. Merit scholarship approved by Central Committee / Sibling discount" 
                               value="{{ old('adjustment_reason') }}">
                    </div>
                </div>

                <!-- Dynamic Installments Section -->
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-calendar-days text-primary me-2"></i>Instalment Milestones & Due Dates
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="addInstallmentBtn">
                        <i class="fa-solid fa-plus me-1"></i> Add Instalment
                    </button>
                </div>

                <div id="installmentsContainer">
                    @php
                        $existingFees = $admission->studentFees->isNotEmpty() ? $admission->studentFees : collect([
                            (object)[
                                'title' => 'Admission & 1st Term Fee',
                                'amount' => $admission->net_fee > 0 ? $admission->net_fee : $admission->course_fee,
                                'due_date' => $admission->admission_date ?? now(),
                            ]
                        ]);
                    @endphp

                    @foreach($existingFees as $idx => $fee)
                        <div class="row g-2 align-items-center mb-3 installment-row p-2 rounded-2 border">
                            <div class="col-md-4">
                                <label class="form-label small text-muted mb-1">Title</label>
                                <input type="text" name="installments[{{ $idx }}][title]" class="form-control form-control-sm" 
                                       value="{{ $fee->title ?? 'Instalment #' . ($idx + 1) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted mb-1">Amount (₹)</label>
                                <input type="number" step="0.01" name="installments[{{ $idx }}][amount]" class="form-control form-control-sm installment-amount" 
                                       value="{{ $fee->amount }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted mb-1">Due Date</label>
                                <input type="date" name="installments[{{ $idx }}][due_date]" class="form-control form-control-sm" 
                                       value="{{ $fee->due_date ? \Carbon\Carbon::parse($fee->due_date)->format('Y-m-d') : date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-1 text-end pt-3">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn" title="Remove row">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Reconciliation Strip -->
                <div class="p-3 rounded-3 mb-4 d-flex justify-content-between align-items-center" style="background-color: #f1f5f9;">
                    <div>
                        <span class="text-muted small">Total of Configured Instalments:</span>
                        <div class="fw-bold fs-5 text-dark" id="totalInstallmentsSum">₹0.00</div>
                    </div>
                    <div id="reconciliationNotice">
                        <!-- Dynamic check badge -->
                    </div>
                </div>

                <div class="text-end border-top pt-3">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm fw-semibold">
                        <i class="fa-solid fa-check-double me-1"></i> Save Custom Instalment Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = {{ count($existingFees) }};

    function recalculate() {
        const courseFee = parseFloat(document.getElementById('courseFeeInput').value) || 0;
        const concession = parseFloat(document.getElementById('concessionInput').value) || 0;
        const netFee = Math.max(0, courseFee - concession);

        document.getElementById('netFeeDisplay').value = '₹' + netFee.toFixed(2);

        let sum = 0;
        document.querySelectorAll('.installment-amount').forEach(inp => {
            sum += parseFloat(inp.value) || 0;
        });

        document.getElementById('totalInstallmentsSum').textContent = '₹' + sum.toFixed(2);

        const notice = document.getElementById('reconciliationNotice');
        if (Math.abs(sum - netFee) < 0.5) {
            notice.innerHTML = '<span class="badge bg-success-subtle text-success fs-6 px-3 py-2"><i class="fa-solid fa-circle-check me-1"></i> Schedule Balanced (₹' + sum.toFixed(2) + ' = Net Fee)</span>';
        } else {
            const diff = (netFee - sum).toFixed(2);
            notice.innerHTML = '<span class="badge bg-danger-subtle text-danger fs-6 px-3 py-2"><i class="fa-solid fa-triangle-exclamation me-1"></i> Difference of ₹' + Math.abs(diff) + ' (Must equal Net Fee)</span>';
        }
    }

    document.getElementById('courseFeeInput').addEventListener('input', recalculate);
    document.getElementById('concessionInput').addEventListener('input', recalculate);

    document.getElementById('installmentsContainer').addEventListener('input', function(e) {
        if (e.target.classList.contains('installment-amount')) {
            recalculate();
        }
    });

    document.getElementById('installmentsContainer').addEventListener('click', function(e) {
        if (e.target.closest('.remove-row-btn')) {
            const row = e.target.closest('.installment-row');
            if (document.querySelectorAll('.installment-row').length > 1) {
                row.remove();
                recalculate();
            } else {
                alert('At least one instalment must be present.');
            }
        }
    });

    document.getElementById('addInstallmentBtn').addEventListener('click', function() {
        rowIndex++;
        const nextDate = new Date();
        nextDate.setMonth(nextDate.getMonth() + (rowIndex - 1));
        const dateStr = nextDate.toISOString().split('T')[0];

        const rowHtml = `
            <div class="row g-2 align-items-center mb-3 installment-row p-2 rounded-2 border">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Title</label>
                    <input type="text" name="installments[${rowIndex}][title]" class="form-control form-control-sm" 
                           value="Instalment #${rowIndex}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Amount (₹)</label>
                    <input type="number" step="0.01" name="installments[${rowIndex}][amount]" class="form-control form-control-sm installment-amount" 
                           value="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Due Date</label>
                    <input type="date" name="installments[${rowIndex}][due_date]" class="form-control form-control-sm" 
                           value="${dateStr}" required>
                </div>
                <div class="col-md-1 text-end pt-3">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn" title="Remove row">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
        `;

        document.getElementById('installmentsContainer').insertAdjacentHTML('beforeend', rowHtml);
        recalculate();
    });

    recalculate();
});
</script>
@endpush

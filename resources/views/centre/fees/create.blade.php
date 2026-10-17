@extends('layouts.centre')

@section('title', 'Collect Student Fee - TECH ERA COMPUTER')

@section('content')
<div class="mb-4">
    <a href="{{ route('centre.fees.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill mb-2">
        <i class="fa-solid fa-arrow-left me-1"></i>Back to Collections Ledger
    </a>
    <h3 class="fw-bold text-dark mb-1">
        <i class="fa-solid fa-hand-holding-dollar text-success me-2"></i>Collect Student Fee Payment
    </h3>
    <p class="text-muted small mb-0">Record offline tuition and examination fee installment, update ledger, and generate computerized receipt.</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 rounded-4 shadow-sm mb-4">
            <div class="card-body p-4">
                <!-- Step 1: Select Student Admission -->
                <form method="GET" action="{{ route('centre.fees.create') }}" class="mb-4">
                    <label class="form-label fw-bold">Select Candidate with Pending Dues: <span class="text-danger">*</span></label>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-9">
                            <select name="admission_id" class="form-select" onchange="this.form.submit()" required>
                                <option value="">-- Choose Candidate --</option>
                                @foreach($students as $s)
                                @php $adm = $s->admissions->first(); @endphp
                                @if($adm)
                                @php $due = max(0, $adm->net_fee - $adm->total_paid); @endphp
                                <option value="{{ $adm->id }}" {{ ($selectedAdmission && $selectedAdmission->id == $adm->id) ? 'selected' : '' }}>
                                    {{ $s->roll_no }} - {{ $s->full_name }} ({{ $adm->course?->course_code }}) &mdash; Due: ₹{{ number_format($due, 2) }}
                                </option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 fw-semibold">Select</button>
                        </div>
                    </div>
                </form>

                @if($selectedAdmission)
                @php $outstanding = max(0, $selectedAdmission->net_fee - $selectedAdmission->total_paid); @endphp

                <!-- Student & Course Ledger Snapshot -->
                <div class="bg-light p-3 rounded-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <div>
                            <div class="fw-bold text-dark fs-6">{{ $selectedAdmission->student?->full_name }}</div>
                            <div class="small text-muted font-monospace">ROLL: {{ $selectedAdmission->student?->roll_no }} &bull; REG: {{ $selectedAdmission->student?->registration_no }}</div>
                        </div>
                        <span class="badge bg-primary font-monospace">{{ $selectedAdmission->course?->course_code }}</span>
                    </div>

                    <div class="row g-2 text-center pt-1">
                        <div class="col-4">
                            <div class="small text-muted">Total Net Fee</div>
                            <div class="fw-bold text-dark fs-6">₹{{ number_format($selectedAdmission->net_fee, 2) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Already Paid</div>
                            <div class="fw-bold text-success fs-6">₹{{ number_format($selectedAdmission->total_paid, 2) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Outstanding Due</div>
                            <div class="fw-bold text-danger fs-5">₹{{ number_format($outstanding, 2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <form method="POST" action="{{ route('centre.fees.store') }}">
                    @csrf
                    <input type="hidden" name="admission_id" value="{{ $selectedAdmission->id }}">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="1" max="{{ $outstanding }}" name="amount" class="form-control form-control-lg fw-bold text-success" value="{{ old('amount', $outstanding) }}" required>
                            <div class="form-text" style="font-size: 0.72rem;">Maximum collectable: ₹{{ number_format($outstanding, 2) }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Mode <span class="text-danger">*</span></label>
                            <select name="payment_mode" class="form-select form-select-lg" required>
                                <option value="cash" {{ old('payment_mode') == 'cash' ? 'selected' : '' }}>Cash (Direct Counter)</option>
                                <option value="upi" {{ old('payment_mode') == 'upi' ? 'selected' : '' }}>UPI / QR Code</option>
                                <option value="bank_transfer" {{ old('payment_mode') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer (NEFT/RTGS/IMPS)</option>
                                <option value="cheque" {{ old('payment_mode') == 'cheque' ? 'selected' : '' }}>Cheque / Demand Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Transaction Ref / Cheque No / UTR</label>
                            <input type="text" name="transaction_ref" class="form-control" value="{{ old('transaction_ref') }}" placeholder="e.g. UPI-93821038 or CHQ-001234">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Collector Notes / Remarks</label>
                            <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="e.g. Installment 1 / Examination fee">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow-sm">
                        <i class="fa-solid fa-receipt me-1"></i>Confirm & Generate Receipt
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.centre')

@section('title', 'Fee Collections & Receipts - TECH ERA COMPUTER')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-receipt text-success me-2"></i>Fee Collections & Receipts Ledger
        </h3>
        <p class="text-muted small mb-0">Record offline tuition and examination fee payments, issue computerized receipts, and review outstanding dues.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('centre.fees.create') }}" class="btn btn-success rounded-pill px-4 fw-semibold shadow-sm">
            <i class="fa-solid fa-hand-holding-dollar me-1"></i>Collect Student Fee
        </a>
        <a href="{{ route('centre.fees.dues') }}" class="btn btn-outline-danger rounded-pill px-3">
            <i class="fa-solid fa-file-invoice-dollar me-1"></i>Outstanding Dues Report
        </a>
    </div>
</div>

<!-- Stats Strip -->
<div class="row g-2 g-md-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card p-2.5 p-sm-3 bg-white border-start border-4 border-success h-100">
            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Total Collections</div>
            <div class="fs-6 fs-sm-5 fs-md-4 fw-bold text-success my-1">₹{{ number_format($stats['total_collected'], 2) }}</div>
            <span class="small text-muted d-none d-sm-inline" style="font-size: 0.72rem;">All-time receipts</span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card p-2.5 p-sm-3 bg-white border-start border-4 border-primary h-100">
            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Today's Collection</div>
            <div class="fs-6 fs-sm-5 fs-md-4 fw-bold text-primary my-1">₹{{ number_format($stats['today_collected'], 2) }}</div>
            <span class="small text-muted d-none d-sm-inline" style="font-size: 0.72rem;">{{ now()->format('d M, Y') }}</span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card p-2.5 p-sm-3 bg-white h-100">
            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Cash Transactions</div>
            <div class="fs-5 fs-sm-4 fw-bold text-dark my-1">{{ number_format($stats['cash_count']) }}</div>
            <span class="small text-muted d-none d-sm-inline" style="font-size: 0.72rem;">Counter receipts</span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card p-2.5 p-sm-3 bg-white h-100">
            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Digital / UPI</div>
            <div class="fs-5 fs-sm-4 fw-bold text-info my-1">{{ number_format($stats['online_count']) }}</div>
            <span class="small text-muted d-none d-sm-inline" style="font-size: 0.72rem;">UPI, Bank transfer</span>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('centre.fees.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search receipt no, student name, roll no..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="payment_mode" class="form-select">
                    <option value="">All Payment Modes</option>
                    <option value="cash" {{ request('payment_mode') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="upi" {{ request('payment_mode') == 'upi' ? 'selected' : '' }}>UPI / QR</option>
                    <option value="bank_transfer" {{ request('payment_mode') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="cheque" {{ request('payment_mode') == 'cheque' ? 'selected' : '' }}>Cheque / DD</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From date">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To date">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold">Filter</button>
                <a href="{{ route('centre.fees.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Payments Table -->
<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Receipt No</th>
                        <th>Student Name & Roll</th>
                        <th>Course</th>
                        <th>Payment Date</th>
                        <th>Mode & Ref</th>
                        <th>Amount</th>
                        <th class="text-end">Print Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>
                            <span class="badge bg-secondary-subtle text-dark font-monospace fw-bold">{{ $payment->receipt_no }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $payment->student?->full_name }}</div>
                            <div class="small text-muted font-monospace">{{ $payment->student?->roll_no }}</div>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary">{{ $payment->admission?->course?->course_code ?? 'Course' }}</span>
                            <div class="small text-muted text-truncate" style="max-width: 150px;">{{ $payment->admission?->course?->course_name }}</div>
                        </td>
                        <td>{{ $payment->payment_date ? $payment->payment_date->format('d M, Y') : 'N/A' }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ strtoupper($payment->payment_mode) }}</span>
                            @if($payment->transaction_ref)
                            <div class="small text-muted font-monospace">{{ $payment->transaction_ref }}</div>
                            @endif
                        </td>
                        <td class="fw-bold text-success fs-6">₹{{ number_format($payment->amount, 2) }}</td>
                        <td class="text-end">
                            @if($payment->paymentReceipt)
                            <a href="{{ route('centre.fees.receipt', $payment->paymentReceipt->id) }}" class="btn btn-sm btn-outline-success" target="_blank">
                                <i class="fa-solid fa-print me-1"></i>Official Receipt
                            </a>
                            @else
                            <span class="text-muted small">No receipt file</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-receipt fs-1 text-black-50 mb-3 d-block"></i>
                            <h5>No Fee Payments Recorded</h5>
                            <p class="small text-muted mb-3">Collect tuition fees from admitted students to issue computerized receipts.</p>
                            <a href="{{ route('centre.fees.create') }}" class="btn btn-success rounded-pill px-4">
                                <i class="fa-solid fa-hand-holding-dollar me-1"></i>Collect First Payment
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="p-3 border-top">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

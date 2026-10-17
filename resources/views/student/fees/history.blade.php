@extends('layouts.student')

@section('title', 'Payment History Ledger - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Payment History & Transaction Ledger
            </h4>
            <div class="text-muted small">Complete audit trail of all verified fee payment transactions and computerized receipts</div>
        </div>
        <div>
            <a href="{{ route('student.fees') }}" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Fees
            </a>
        </div>
    </div>

    <!-- Payments Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-receipt text-success me-2"></i> Transaction Ledger
            </h5>
            <span class="badge bg-light text-muted">{{ $payments->total() }} Transactions</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #64748b;">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Receipt Number</th>
                            <th>Payment Mode</th>
                            <th>Transaction Ref</th>
                            <th>Amount Paid</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td class="ps-4 small text-muted text-nowrap">
                                    {{ $payment->payment_date ? $payment->payment_date->format('d M, Y') : $payment->created_at->format('d M, Y') }}
                                </td>
                                <td>
                                    <span class="font-monospace fw-bold small text-primary">{{ $payment->receipt_no }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border small text-uppercase">
                                        {{ str_replace('_', ' ', $payment->payment_mode) }}
                                    </span>
                                </td>
                                <td class="font-monospace small text-muted">
                                    {{ $payment->transaction_ref ?? 'OFFLINE' }}
                                </td>
                                <td class="fw-bold text-success fs-6">
                                    ₹{{ number_format($payment->amount, 2) }}
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill small">
                                        <i class="fa-solid fa-circle-check me-1"></i> Verified
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('student.fees.receipt', $payment->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fa-solid fa-print me-1"></i> View Receipt
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-receipt fa-2x mb-2 text-secondary opacity-50"></i>
                                    <p class="mb-0">No payment transactions recorded on your ledger yet.</p>
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
</div>
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt - {{ $receipt->receipt_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
            padding: 25px;
        }

        .receipt-card {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border: 2px dashed #0891b2;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            position: relative;
        }

        .brand-title {
            color: #0891b2;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 130px;
            color: rgba(8, 145, 178, 0.04);
            pointer-events: none;
            z-index: 1;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .receipt-card {
                box-shadow: none;
                border: 2px solid #000;
                width: 100%;
                max-width: 100%;
            }
            .d-print-none {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="text-center mb-3 d-print-none">
    <button onclick="window.print()" class="btn btn-primary btn-lg shadow-sm" style="background-color: #0891b2; border-color: #0891b2;">
        <i class="fa-solid fa-print me-2"></i> Print Official Receipt
    </button>
</div>

<div class="receipt-card">
    <div class="watermark"><i class="fa-solid fa-receipt"></i></div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-1" style="height: 55px; width: auto; object-fit: contain;">
            <div>
                <h4 class="brand-title mb-0" style="color: #0284c7;">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</h4>
                <div class="small text-muted fw-semibold">{{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}</div>
                <div class="small text-muted">Campus: {{ $instituteSettings['corporate_address'] ?? 'Sasbahana, Sheikhpura, Bihar - 801105' }} | Helpline: {{ $instituteSettings['contact_phone'] ?? '9162384660' }}</div>
            </div>
        </div>
        <div class="text-end">
            <span class="badge bg-success px-3 py-2 text-uppercase fs-6">Fee Receipt</span>
            <div class="mt-2 small fw-bold text-dark font-monospace">{{ $receipt->receipt_no }}</div>
            <div class="small text-muted">Date: {{ $receipt->receipt_date ? $receipt->receipt_date->format('d/m/Y') : date('d/m/Y') }}</div>
        </div>
    </div>

    <!-- Student & Admission Particulars -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6">
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td class="text-muted small fw-semibold" style="width: 130px;">Student Name:</td>
                    <td class="fw-bold text-dark">{{ $receipt->student->full_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted small fw-semibold">Father's Name:</td>
                    <td class="text-dark">{{ $receipt->student->father_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted small fw-semibold">Roll Number:</td>
                    <td class="fw-bold text-primary font-monospace">{{ $receipt->student->roll_no ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted small fw-semibold">Registration No:</td>
                    <td class="font-monospace text-dark">{{ $receipt->student->registration_no ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
        <div class="col-sm-6">
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td class="text-muted small fw-semibold" style="width: 130px;">Course:</td>
                    <td class="fw-bold text-dark">{{ $receipt->admission->course->course_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted small fw-semibold">Study Centre:</td>
                    <td class="text-dark">{{ $receipt->centre->centre_name ?? 'Central Head Office' }}</td>
                </tr>
                <tr>
                    <td class="text-muted small fw-semibold">Payment Mode:</td>
                    <td class="badge bg-light text-dark border font-monospace text-uppercase">{{ $receipt->payment_mode }}</td>
                </tr>
                @if($receipt->payment && $receipt->payment->transaction_ref)
                    <tr>
                        <td class="text-muted small fw-semibold">Transaction Ref:</td>
                        <td class="font-monospace text-muted small">{{ $receipt->payment->transaction_ref }}</td>
                    </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- Payment Breakdown Box -->
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Description</th>
                    <th class="text-end" style="width: 180px;">Amount (INR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="fw-bold text-dark">Course Tuition / Installment Fee Payment</div>
                        <small class="text-muted">{{ $receipt->notes ?: 'Fee installment collected towards enrolled course package.' }}</small>
                    </td>
                    <td class="text-end fw-bold fs-5 text-success">
                        ₹{{ number_format($receipt->amount, 2) }}
                    </td>
                </tr>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td class="fw-bold">Total Amount Paid</td>
                    <td class="text-end fw-bold fs-5 text-primary">₹{{ number_format($receipt->amount, 2) }}</td>
                </tr>
                @if($receipt->admission)
                    @php
                        $balanceDue = max(0, (float) $receipt->admission->net_fee - (float) $receipt->admission->total_paid);
                    @endphp
                    <tr>
                        <td class="text-muted small">Total Paid to Date: ₹{{ number_format($receipt->admission->total_paid, 2) }} of ₹{{ number_format($receipt->admission->net_fee, 2) }}</td>
                        <td class="text-end text-danger fw-bold small">Remaining Due: ₹{{ number_format($balanceDue, 2) }}</td>
                    </tr>
                @endif
            </tfoot>
        </table>
    </div>

    <!-- Verification QR & Signatures -->
    <div class="row align-items-end mt-5 pt-3">
        <div class="col-4">
            <div class="text-center">
                <div class="border-top pt-2 small text-muted">Candidate / Depositor Signature</div>
            </div>
        </div>
        <div class="col-4 text-center">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=85x85&data={{ urlencode($receipt->qr_token ?? $receipt->receipt_no) }}" alt="QR" style="width: 75px; height: 75px;" class="border p-1 bg-white mb-1">
            <div class="text-muted font-monospace" style="font-size: 0.65rem;">Digital Token: {{ substr($receipt->qr_token ?? 'DCE-VAL', 0, 16) }}...</div>
        </div>
        <div class="col-4 text-center">
            <div class="border-top pt-2 small text-dark fw-bold">Authorized Cashier / Accountant</div>
            <div class="text-muted" style="font-size: 0.72rem;">Issued by: {{ $receipt->issuer->name ?? 'Accounts Officer' }}</div>
        </div>
    </div>
</div>

</body>
</html>

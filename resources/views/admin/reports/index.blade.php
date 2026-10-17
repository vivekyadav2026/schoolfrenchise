@extends('layouts.admin')

@section('title', 'Reports & Analytics Hub')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reports</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-chart-column text-primary me-2"></i>Institutional Reports & Business Intelligence
            </h2>
            <p class="text-muted small mb-0">Generate comprehensive audit reports, academic analytics, and financial statements across nationwide operations.</p>
        </div>
    </div>

    <!-- Executive KPI Grid -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Total Enrolled Students</div>
                    <div class="h3 fw-bold text-dark mb-0">{{ number_format($stats['total_students']) }}</div>
                    <div class="text-muted small mt-1">{{ number_format($stats['active_students']) }} active across {{ $stats['total_centres'] }} centres</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Total Fees Collected</div>
                    <div class="h3 fw-bold text-success mb-0">₹{{ number_format($stats['total_collected_fees'], 2) }}</div>
                    <div class="text-muted small mt-1">Cleared tuition revenues</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Outstanding Dues</div>
                    <div class="h3 fw-bold text-warning mb-0">₹{{ number_format($stats['total_dues'], 2) }}</div>
                    <div class="text-muted small mt-1">Pending fee installments</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="text-muted small fw-bold text-uppercase">Examination Pass Rate</div>
                    <div class="h3 fw-bold text-info mb-0">{{ $stats['pass_rate'] }}%</div>
                    <div class="text-muted small mt-1">{{ number_format($stats['total_results']) }} evaluated candidates</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Domain Report Modules Grid -->
    <div class="row g-4">
        <!-- 1. Students & Admissions -->
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                                <i class="fa-solid fa-user-graduate fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Student & Admissions</h5>
                                <div class="text-muted small">Demographics & Registrations</div>
                            </div>
                        </div>
                        <p class="text-muted small">Analyze student demographics, course demand distribution, gender ratios, admission trends, and centre-wise enrollments.</p>
                    </div>
                    <div class="d-flex gap-2 pt-3 border-top">
                        <a href="{{ route('admin.reports.students') }}" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="fa-solid fa-chart-pie me-1"></i> View Report
                        </a>
                        <a href="{{ route('admin.reports.students.export') }}" class="btn btn-outline-secondary btn-sm" title="Export CSV">
                            <i class="fa-solid fa-file-csv"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Fees & Financials -->
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-success bg-opacity-10 text-success p-3 rounded-3">
                                <i class="fa-solid fa-indian-rupee-sign fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Fees & Collections</h5>
                                <div class="text-muted small">Financial Ledgers & Dues</div>
                            </div>
                        </div>
                        <p class="text-muted small">Strict financial reconciliation distinguishing cleared payments, pending transactions, failed checkouts, refunds, and outstanding student dues.</p>
                    </div>
                    <div class="d-flex gap-2 pt-3 border-top">
                        <a href="{{ route('admin.reports.fees') }}" class="btn btn-success btn-sm flex-grow-1">
                            <i class="fa-solid fa-chart-line me-1"></i> View Report
                        </a>
                        <a href="{{ route('admin.reports.fees.export') }}" class="btn btn-outline-secondary btn-sm" title="Export CSV">
                            <i class="fa-solid fa-file-csv"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Attendance & Engagement -->
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-info bg-opacity-10 text-info p-3 rounded-3">
                                <i class="fa-solid fa-clipboard-user fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Attendance Analytics</h5>
                                <div class="text-muted small">Roll Calls & Class Attendance</div>
                            </div>
                        </div>
                        <p class="text-muted small">Batch-wise daily attendance percentages, regularity rates, punctuality tracking, and absenteeism reports across study centres.</p>
                    </div>
                    <div class="d-flex gap-2 pt-3 border-top">
                        <a href="{{ route('admin.reports.attendance') }}" class="btn btn-info text-white btn-sm flex-grow-1">
                            <i class="fa-solid fa-chart-bar me-1"></i> View Report
                        </a>
                        <a href="{{ route('admin.reports.attendance.export') }}" class="btn btn-outline-secondary btn-sm" title="Export CSV">
                            <i class="fa-solid fa-file-csv"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Exams & Academic Results -->
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3">
                                <i class="fa-solid fa-award fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Exams & Academic Results</h5>
                                <div class="text-muted small">Pass Ratios & Marks Distribution</div>
                            </div>
                        </div>
                        <p class="text-muted small">Monitor institutional assessment results, course-wise average scores, top performers, grade distributions, and failure rates.</p>
                    </div>
                    <div class="d-flex gap-2 pt-3 border-top">
                        <a href="{{ route('admin.reports.exams') }}" class="btn btn-warning text-dark btn-sm flex-grow-1">
                            <i class="fa-solid fa-square-poll-vertical me-1"></i> View Report
                        </a>
                        <a href="{{ route('admin.reports.exams.export') }}" class="btn btn-outline-secondary btn-sm" title="Export CSV">
                            <i class="fa-solid fa-file-csv"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Operating Expenses & Marketing -->
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-3">
                                <i class="fa-solid fa-receipt fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Expenses & Outflow</h5>
                                <div class="text-muted small">Operating Costs & Marketing ROI</div>
                            </div>
                        </div>
                        <p class="text-muted small">Track facility rent, hardware, utilities, salaries, and digital marketing acquisition spends with Cost Per Lead (CPL) benchmarks.</p>
                    </div>
                    <div class="d-flex gap-2 pt-3 border-top">
                        <a href="{{ route('admin.reports.expenses') }}" class="btn btn-danger btn-sm flex-grow-1">
                            <i class="fa-solid fa-file-invoice-dollar me-1"></i> View Report
                        </a>
                        <a href="{{ route('admin.reports.expenses.export') }}" class="btn btn-outline-secondary btn-sm" title="Export CSV">
                            <i class="fa-solid fa-file-csv"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Financial P&L & Net Cashflow -->
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-success bg-opacity-10 text-success p-3 rounded-3">
                                <i class="fa-solid fa-chart-line fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Financial P&L & Net Cashflow</h5>
                                <div class="text-muted small">Gross, Refunds, Expenses & Net</div>
                            </div>
                        </div>
                        <p class="text-muted small">Executive net margin analysis separating gross fee collections, approved refunds, operating disbursements, and marketing spends with date-wise cashflow.</p>
                    </div>
                    <div class="d-flex gap-2 pt-3 border-top">
                        <a href="{{ route('admin.reports.financial') }}" class="btn btn-success btn-sm flex-grow-1">
                            <i class="fa-solid fa-calculator me-1"></i> View Statement
                        </a>
                        <a href="{{ route('admin.reports.financial.export') }}" class="btn btn-outline-secondary btn-sm" title="Export CSV">
                            <i class="fa-solid fa-file-csv"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Franchise Network Dossier -->
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-secondary bg-opacity-10 text-secondary p-3 rounded-3">
                                <i class="fa-solid fa-building-columns fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Franchise Network</h5>
                                <div class="text-muted small">Branch Operations & Wallets</div>
                            </div>
                        </div>
                        <p class="text-muted small">Review franchise centre health, student intake capacities, circulating wallet balances, and state-wise expansion rosters.</p>
                    </div>
                    <div class="d-flex gap-2 pt-3 border-top">
                        <a href="{{ route('admin.centres.index') }}" class="btn btn-secondary btn-sm flex-grow-1">
                            <i class="fa-solid fa-network-wired me-1"></i> Franchise Directory
                        </a>
                        <a href="{{ route('admin.wallet.index') }}" class="btn btn-outline-secondary btn-sm" title="Wallet History">
                            <i class="fa-solid fa-wallet"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

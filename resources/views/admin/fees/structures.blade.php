@extends('layouts.admin')

@section('title', 'Course Fee Structures - TECH ERA COMPUTER')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb dce-breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.fees.index') }}" class="text-decoration-none">Fees</a></li>
                <li class="breadcrumb-item active" aria-current="page">Fee Structures</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 text-dark">Course Fee Structures & Plans</h3>
        <p class="text-muted small mb-0">Configure standard fee packages, installment plans, and component breakdowns by course.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.fees.structures.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Add Fee Structure
        </a>
        <a href="{{ route('admin.fees.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-receipt me-1"></i> Payment Ledger
        </a>
    </div>
</div>

<div class="card dce-table-card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-sliders text-primary me-2"></i>Configured Fee Packages
        </h6>
        <span class="badge bg-light text-muted">Total {{ $structures->total() }} Plans</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Plan Name</th>
                        <th>Applicable Course</th>
                        <th>Target Centre</th>
                        <th class="text-end">Total Fee</th>
                        <th class="text-center">Instalments</th>
                        <th>Components</th>
                        <th>Status</th>
                        <th class="pe-3 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($structures as $fs)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">{{ $fs->name }}</div>
                                @if($fs->terms)
                                    <div class="text-muted small">{{ Str::limit($fs->terms, 40) }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold text-primary">{{ $fs->course?->course_name }}</span>
                                <span class="badge bg-light text-muted ms-1 font-monospace">{{ $fs->course?->course_code }}</span>
                            </td>
                            <td>
                                @if($fs->centre)
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $fs->centre->centre_name }}</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary">All Centres (Global)</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-dark">₹{{ number_format($fs->total_fee, 2) }}</td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info px-3 py-1 font-monospace">
                                    {{ $fs->installments_count }} {{ Str::plural('Term', $fs->installments_count) }}
                                </span>
                            </td>
                            <td class="small text-muted">
                                @if($fs->admission_fee > 0) Adm: ₹{{ number_format($fs->admission_fee) }} &bull; @endif
                                @if($fs->tuition_fee > 0) Tuit: ₹{{ number_format($fs->tuition_fee) }} &bull; @endif
                                @if($fs->exam_fee > 0) Exam: ₹{{ number_format($fs->exam_fee) }} @endif
                            </td>
                            <td>
                                @if($fs->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="pe-3 text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.fees.structures.edit', $fs->id) }}" class="btn btn-outline-secondary" title="Edit Structure">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.fees.structures.destroy', $fs->id) }}" class="d-inline" data-confirm="Are you sure you want to delete this fee structure?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-file-invoice-dollar fa-2x mb-2 text-secondary opacity-50"></i>
                                <div>No custom fee structures configured yet. Courses currently use standard curriculum pricing.</div>
                                <a href="{{ route('admin.fees.structures.create') }}" class="btn btn-primary btn-sm rounded-pill mt-2">
                                    <i class="fa-solid fa-plus me-1"></i> Create First Fee Plan
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($structures->hasPages())
            <div class="p-3 border-top">
                {{ $structures->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

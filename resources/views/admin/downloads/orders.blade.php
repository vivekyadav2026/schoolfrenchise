@extends('layouts.admin')

@section('title', 'Kit & Resource Orders')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.downloads.index') }}">Downloads</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Orders</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-cart-shopping text-info me-2"></i>Resource & Franchise Orders
            </h2>
            <p class="text-muted small mb-0">Track orders for student welcome kits, certificates, examination stationery, and paid digital downloads.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.downloads.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-boxes-stacked me-1"></i> Downloads Catalog
            </a>
            <a href="{{ route('admin.downloads.orders.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-plus-circle me-1"></i> Log New Order
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Strict Financial KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Completed Revenue</span>
                        <div class="rounded-circle bg-success bg-opacity-10 p-2 text-success">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-success mb-0">₹{{ number_format($stats['completed_revenue'], 2) }}</h3>
                    <div class="text-muted small mt-1">Legitimate collected funds</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Pending Orders</span>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-2 text-warning">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-warning mb-0">₹{{ number_format($stats['pending_amount'], 2) }}</h3>
                    <div class="text-muted small mt-1">Awaiting payment verification</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Failed / Cancelled</span>
                        <div class="rounded-circle bg-danger bg-opacity-10 p-2 text-danger">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-danger mb-0">₹{{ number_format($stats['failed_amount'], 2) }}</h3>
                    <div class="text-muted small mt-1">Unprocessed checkout orders</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-secondary">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Refunded Amount</span>
                        <div class="rounded-circle bg-secondary bg-opacity-10 p-2 text-secondary">
                            <i class="fa-solid fa-arrow-rotate-left"></i>
                        </div>
                    </div>
                    <h3 class="h4 fw-bold text-secondary mb-0">₹{{ number_format($stats['refunded_amount'], 2) }}</h3>
                    <div class="text-muted small mt-1">Reversed ledger transactions</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.downloads.orders') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search order no, item, transaction ref..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="item_type" class="form-select form-select-sm">
                        <option value="">All Item Types</option>
                        <option value="kit" {{ request('item_type') === 'kit' ? 'selected' : '' }}>Student Welcome Kits</option>
                        <option value="certificates" {{ request('item_type') === 'certificates' ? 'selected' : '' }}>Certificate Paper Blankets</option>
                        <option value="stationery" {{ request('item_type') === 'stationery' ? 'selected' : '' }}>Institute Stationery</option>
                        <option value="download" {{ request('item_type') === 'download' ? 'selected' : '' }}>Digital Downloads</option>
                        <option value="other" {{ request('item_type') === 'other' ? 'selected' : '' }}>Other Supplies</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="payment_status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="completed" {{ request('payment_status') === 'completed' ? 'selected' : '' }}>Completed (Paid)</option>
                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'item_type', 'payment_status']))
                        <a href="{{ route('admin.downloads.orders') }}" class="btn btn-sm btn-light border" title="Clear Filters">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0 text-dark">
                Orders Roster <span class="badge bg-light text-dark fw-normal border ms-1">{{ $orders->total() }}</span>
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-3">Order No</th>
                        <th>Centre / Customer</th>
                        <th>Item Details</th>
                        <th>Type</th>
                        <th class="text-end">Amount</th>
                        <th>Payment Mode</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $ord)
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route('admin.downloads.orders.show', $ord) }}" class="font-monospace fw-bold text-primary text-decoration-none">
                                    {{ $ord->order_no }}
                                </a>
                                <div class="small text-muted">{{ $ord->created_at?->format('d M, Y') }}</div>
                            </td>
                            <td>
                                @if($ord->centre)
                                    <div class="fw-semibold text-dark">{{ $ord->centre->centre_name }}</div>
                                    <div class="small text-muted font-monospace">{{ $ord->centre->centre_code }}</div>
                                @elseif($ord->user)
                                    <div class="fw-semibold text-dark">{{ $ord->user->name }}</div>
                                    <div class="small text-muted">{{ $ord->user->email }}</div>
                                @else
                                    <div class="text-muted small">Central Board Direct</div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $ord->item_name }}</div>
                                @if($ord->transaction_ref)
                                    <div class="small text-muted font-monospace">Ref: {{ $ord->transaction_ref }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ ucfirst($ord->item_type) }}
                                </span>
                            </td>
                            <td class="text-end font-monospace fw-bold text-dark">
                                ₹{{ number_format($ord->amount, 2) }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-uppercase">{{ $ord->payment_method }}</span>
                            </td>
                            <td class="text-center">
                                @if($ord->payment_status === 'completed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="fa-solid fa-circle-check me-1 small"></i>Completed
                                    </span>
                                @elseif($ord->payment_status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                        <i class="fa-solid fa-clock me-1 small"></i>Pending
                                    </span>
                                @elseif($ord->payment_status === 'failed')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="fa-solid fa-circle-xmark me-1 small"></i>Failed
                                    </span>
                                @elseif($ord->payment_status === 'refunded')
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                        <i class="fa-solid fa-arrow-rotate-left me-1 small"></i>Refunded
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('admin.downloads.orders.show', $ord) }}" class="btn btn-sm btn-light border text-primary">
                                    <i class="fa-solid fa-eye me-1"></i> Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-receipt fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h6>No Orders Found</h6>
                                    <p class="small text-muted mb-3">No orders have been recorded matching your criteria.</p>
                                    <a href="{{ route('admin.downloads.orders.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fa-solid fa-plus-circle me-1"></i> Log New Order
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="card-footer bg-white py-3 border-0">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

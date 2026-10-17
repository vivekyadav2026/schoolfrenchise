@extends('layouts.admin')

@section('title', 'Order Dossier: ' . $order->order_no)

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.downloads.index') }}">Downloads</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.downloads.orders') }}">Orders</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $order->order_no }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <h2 class="h4 fw-bold mb-0 text-dark">Order: <span class="font-monospace text-primary">{{ $order->order_no }}</span></h2>
                @if($order->payment_status === 'completed')
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Completed (Paid)</span>
                @elseif($order->payment_status === 'pending')
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Pending</span>
                @elseif($order->payment_status === 'failed')
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Failed</span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">Refunded</span>
                @endif
            </div>
        </div>
        <div>
            <a href="{{ route('admin.downloads.orders') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Orders
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Order Particulars -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-receipt text-primary me-2"></i>Order Particulars
                    </h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <span class="text-muted small fw-bold">Order Number</span>
                            <div class="font-monospace fw-bold fs-5 text-dark">{{ $order->order_no }}</div>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small fw-bold">Order Date</span>
                            <div class="text-dark">{{ $order->created_at?->format('d M, Y h:i A') }}</div>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small fw-bold">Item Type</span>
                            <div><span class="badge bg-light text-dark border">{{ ucfirst($order->item_type) }}</span></div>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small fw-bold">Item Name</span>
                            <div class="fw-bold text-dark">{{ $order->item_name }}</div>
                        </div>
                        <div class="col-12"><hr class="my-2"></div>
                        <div class="col-md-6">
                            <span class="text-muted small fw-bold">Total Order Amount</span>
                            <div class="h4 fw-bold text-success font-monospace mb-0">₹{{ number_format($order->amount, 2) }}</div>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small fw-bold">Payment Method & Reference</span>
                            <div class="text-dark text-uppercase fw-semibold">{{ $order->payment_method }}</div>
                            @if($order->transaction_ref)
                                <div class="small text-muted font-monospace">Ref: {{ $order->transaction_ref }}</div>
                            @endif
                        </div>
                        @if($order->notes)
                            <div class="col-12">
                                <span class="text-muted small fw-bold">Order Notes / Dispatch Remarks</span>
                                <div class="p-3 bg-light rounded text-dark mt-1">{{ $order->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Centre Info & Status Update -->
        <div class="col-lg-4">
            <!-- Customer / Centre Card -->
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-building-columns text-primary me-2"></i>Ordered By
                    </h5>
                </div>
                <div class="card-body p-4 pt-0">
                    @if($order->centre)
                        <div class="fw-bold text-dark">{{ $order->centre->centre_name }}</div>
                        <div class="small text-primary font-monospace">{{ $order->centre->centre_code }}</div>
                        <div class="small text-muted mt-2"><i class="fa-solid fa-user-tie me-1"></i>{{ $order->centre->director_name }}</div>
                        <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $order->centre->mobile }}</div>
                        <div class="small text-muted"><i class="fa-solid fa-location-dot me-1"></i>{{ $order->centre->city }}, {{ $order->centre->state?->name }}</div>
                        <div class="mt-3">
                            <a href="{{ route('admin.centres.show', $order->centre) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye me-1"></i> Centre Dossier
                            </a>
                        </div>
                    @elseif($order->user)
                        <div class="fw-bold text-dark">{{ $order->user->name }}</div>
                        <div class="small text-muted">{{ $order->user->email }}</div>
                    @else
                        <div class="text-muted small">Central Board Internal Order</div>
                    @endif
                </div>
            </div>

            <!-- Status Modifier Form -->
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-sliders text-primary me-2"></i>Update Order Status
                    </h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <form method="POST" action="{{ route('admin.downloads.orders.status', $order) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Payment / Fulfillment Status</label>
                            <select name="payment_status" class="form-select">
                                <option value="completed" {{ $order->payment_status === 'completed' ? 'selected' : '' }}>Completed (Paid)</option>
                                <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending Payment</option>
                                <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Notes / Dispatch Updates</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Tracking details or notes...">{{ $order->notes }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 shadow-sm">
                            <i class="fa-solid fa-check me-1"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

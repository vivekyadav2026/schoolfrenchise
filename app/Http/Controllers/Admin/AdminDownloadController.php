<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\Download;
use App\Models\Order;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminDownloadController extends Controller
{
    public function index(Request $request)
    {
        $query = Download::query();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('version', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status == '1');
        }

        $downloads = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total_downloads'         => Download::count(),
            'active_downloads'        => Download::where('status', true)->count(),
            'total_download_hits'     => (int) Download::sum('downloads_count'),
            'total_orders'            => Order::count(),
            'collected_order_revenue' => (float) Order::where('payment_status', 'completed')->sum('amount'),
        ];

        return view('admin.downloads.index', compact('downloads', 'stats'));
    }

    public function create()
    {
        return view('admin.downloads.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:191',
            'category'    => 'required|in:software,prospectus,form,template,driver,study_note',
            'file'        => 'required|file|max:20480',
            'version'     => 'nullable|string|max:30',
            'is_free'     => 'required|boolean',
            'price'       => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ]);

        $filePath = FileUploadService::uploadMaterial($request->file('file'), 'uploads/downloads');
        $rawSize = $request->file('file')->getSize();
        $fileSize = $rawSize > 1048576
            ? round($rawSize / 1048576, 2) . ' MB'
            : round($rawSize / 1024, 1) . ' KB';

        $slugBase = Str::slug($validated['title']);
        $slug = $slugBase;
        $counter = 1;
        while (Download::where('slug', $slug)->exists()) {
            $slug = "{$slugBase}-{$counter}";
            $counter++;
        }

        $download = Download::create([
            'title'           => $validated['title'],
            'slug'            => $slug,
            'category'        => $validated['category'],
            'file_path'       => $filePath,
            'file_size'       => $fileSize,
            'version'         => $validated['version'] ?? null,
            'description'     => $validated['description'] ?? null,
            'downloads_count' => 0,
            'is_free'         => (bool) $validated['is_free'],
            'price'           => $validated['is_free'] ? 0.00 : ($validated['price'] ?? 0.00),
            'status'          => (bool) $validated['status'],
        ]);

        AuditLog::record('download_created', $download, null, $download->toArray());

        return redirect()->route('admin.downloads.index')
            ->with('success', "Resource '{$download->title}' published successfully.");
    }

    public function edit(Download $download)
    {
        return view('admin.downloads.edit', compact('download'));
    }

    public function update(Request $request, Download $download)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:191',
            'category'    => 'required|in:software,prospectus,form,template,driver,study_note',
            'file'        => 'nullable|file|max:20480',
            'version'     => 'nullable|string|max:30',
            'is_free'     => 'required|boolean',
            'price'       => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ]);

        $oldData = $download->toArray();

        if ($request->hasFile('file')) {
            $filePath = FileUploadService::uploadMaterial($request->file('file'), 'uploads/downloads');
            $rawSize = $request->file('file')->getSize();
            $download->file_path = $filePath;
            $download->file_size = $rawSize > 1048576
                ? round($rawSize / 1048576, 2) . ' MB'
                : round($rawSize / 1024, 1) . ' KB';
        }

        $download->title = $validated['title'];
        $download->category = $validated['category'];
        $download->version = $validated['version'] ?? null;
        $download->description = $validated['description'] ?? null;
        $download->is_free = (bool) $validated['is_free'];
        $download->price = $validated['is_free'] ? 0.00 : ($validated['price'] ?? 0.00);
        $download->status = (bool) $validated['status'];
        $download->save();

        AuditLog::record('download_updated', $download, $oldData, $download->toArray());

        return redirect()->route('admin.downloads.index')
            ->with('success', "Resource '{$download->title}' updated successfully.");
    }

    public function toggleStatus(Download $download)
    {
        $oldStatus = $download->status;
        $download->update(['status' => !$oldStatus]);

        AuditLog::record('download_status_toggled', $download, ['status' => $oldStatus], ['status' => !$oldStatus]);

        return back()->with('success', "Status of '{$download->title}' updated.");
    }

    public function downloadFile(Download $download)
    {
        $download->increment('downloads_count');

        if (!Storage::disk('public')->exists($download->file_path)) {
            return back()->with('error', 'File resource does not exist on storage.');
        }

        return Storage::disk('public')->download($download->file_path, basename($download->file_path));
    }

    public function destroy(Download $download)
    {
        $oldData = $download->toArray();
        $download->delete();

        AuditLog::record('download_deleted', $download, $oldData, null);

        return redirect()->route('admin.downloads.index')
            ->with('success', "Resource '{$download->title}' has been removed.");
    }

    // --- Orders Management Submodule ---

    public function orders(Request $request)
    {
        $query = Order::with(['user', 'centre']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('order_no', 'like', "%{$s}%")
                    ->orWhere('item_name', 'like', "%{$s}%")
                    ->orWhere('transaction_ref', 'like', "%{$s}%")
                    ->orWhereHas('centre', function ($cq) use ($s) {
                        $cq->where('centre_name', 'like', "%{$s}%")
                            ->orWhere('centre_code', 'like', "%{$s}%");
                    });
            });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('item_type')) {
            $query->where('item_type', $request->item_type);
        }

        $orders = $query->latest('id')->paginate(15)->withQueryString();

        // Strict financial calculations separating collected, pending, failed, refunded
        $stats = [
            'total_orders'      => Order::count(),
            'completed_revenue' => (float) Order::where('payment_status', 'completed')->sum('amount'),
            'pending_amount'    => (float) Order::where('payment_status', 'pending')->sum('amount'),
            'failed_amount'     => (float) Order::where('payment_status', 'failed')->sum('amount'),
            'refunded_amount'   => (float) Order::where('payment_status', 'refunded')->sum('amount'),
        ];

        return view('admin.downloads.orders', compact('orders', 'stats'));
    }

    public function createOrder()
    {
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.downloads.create_order', compact('centres'));
    }

    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'centre_id'       => 'nullable|exists:centres,id',
            'item_type'       => 'required|in:download,kit,certificates,stationery,other',
            'item_name'       => 'required|string|max:191',
            'amount'          => 'required|numeric|min:0',
            'payment_status'  => 'required|in:pending,completed,failed,refunded',
            'payment_method'  => 'required|string|max:50',
            'transaction_ref' => 'nullable|string|max:100',
            'notes'           => 'nullable|string',
        ]);

        $order = Order::create([
            'order_no'        => Order::generateOrderNo(),
            'centre_id'       => $validated['centre_id'] ?? null,
            'item_type'       => $validated['item_type'],
            'item_name'       => $validated['item_name'],
            'amount'          => $validated['amount'],
            'payment_status'  => $validated['payment_status'],
            'payment_method'  => $validated['payment_method'],
            'transaction_ref' => $validated['transaction_ref'] ?? null,
            'notes'           => $validated['notes'] ?? null,
        ]);

        AuditLog::record('order_created', $order, null, $order->toArray());

        return redirect()->route('admin.downloads.orders')
            ->with('success', "Order #{$order->order_no} created successfully.");
    }

    public function showOrder(Order $order)
    {
        $order->load(['user', 'centre']);

        return view('admin.downloads.show_order', compact('order'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,completed,failed,refunded',
            'notes'          => 'nullable|string',
        ]);

        $oldStatus = $order->payment_status;
        $order->update([
            'payment_status' => $validated['payment_status'],
            'notes'          => $validated['notes'] ?? $order->notes,
        ]);

        AuditLog::record('order_status_updated', $order, ['payment_status' => $oldStatus], ['payment_status' => $validated['payment_status']]);

        return back()->with('success', "Order #{$order->order_no} status changed to " . ucfirst($validated['payment_status']) . ".");
    }
}

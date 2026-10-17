<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminAuditLogController extends Controller
{
    /**
     * Display a listing of administrative audit logs.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('action', 'like', "%{$s}%")
                    ->orWhere('ip_address', 'like', "%{$s}%")
                    ->orWhere('auditable_type', 'like', "%{$s}%")
                    ->orWhereHas('user', function ($uq) use ($s) {
                        $uq->where('name', 'like', "%{$s}%")
                            ->orWhere('email', 'like', "%{$s}%");
                    });
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', $request->auditable_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest('id')->paginate(25)->withQueryString();

        $stats = [
            'total_logs'    => AuditLog::count(),
            'today_logs'    => AuditLog::whereDate('created_at', now()->toDateString())->count(),
            'critical_logs' => AuditLog::where(function ($q) {
                $q->where('action', 'like', '%delete%')
                    ->orWhere('action', 'like', '%revoke%')
                    ->orWhere('action', 'like', '%refund%')
                    ->orWhere('action', 'like', '%freeze%');
            })->count(),
            'unique_users'  => AuditLog::whereNotNull('user_id')->distinct('user_id')->count('user_id'),
        ];

        $actions = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');
        $models = AuditLog::select('auditable_type')->whereNotNull('auditable_type')->distinct()->orderBy('auditable_type')->pluck('auditable_type');
        $users = User::whereIn('id', AuditLog::select('user_id')->whereNotNull('user_id')->distinct())->orderBy('name')->get();

        return view('admin.audit_logs.index', compact('logs', 'stats', 'actions', 'models', 'users'));
    }

    /**
     * Display a specific audit trail record.
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');

        return view('admin.audit_logs.show', compact('auditLog'));
    }

    /**
     * Stream export filtered audit logs to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = AuditLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', $request->auditable_type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $filename = 'audit_logs_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Log ID',
                'Timestamp',
                'Action',
                'User Name',
                'User Email',
                'Auditable Model',
                'Auditable ID',
                'IP Address',
                'User Agent',
                'Old Values JSON',
                'New Values JSON',
            ]);

            $query->latest('id')->chunk(250, function ($logs) use ($handle) {
                foreach ($logs as $l) {
                    fputcsv($handle, [
                        '#LOG-' . $l->id,
                        $l->created_at ? $l->created_at->format('Y-m-d H:i:s') : '-',
                        $l->action,
                        $l->user ? $l->user->name : 'System',
                        $l->user ? $l->user->email : '-',
                        $l->auditable_type ? class_basename($l->auditable_type) : '-',
                        $l->auditable_id ?? '-',
                        $l->ip_address ?? '-',
                        $l->user_agent ?? '-',
                        $l->old_values ? json_encode($l->old_values) : '-',
                        $l->new_values ? json_encode($l->new_values) : '-',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}

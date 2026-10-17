<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminNotificationController extends Controller
{
    /**
     * Notification Center & Broadcast Directory
     */
    public function index(Request $request)
    {
        $query = SystemNotification::with(['user', 'sender']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('message', 'like', "%{$s}%")
                    ->orWhereHas('user', function ($uq) use ($s) {
                        $uq->where('name', 'like', "%{$s}%")
                            ->orWhere('email', 'like', "%{$s}%");
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->whereNull('read_at');
            } elseif ($request->status === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        if ($request->filled('role')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        $notifications = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total_notifications' => SystemNotification::count(),
            'unread_count'        => SystemNotification::whereNull('read_at')->count(),
            'alert_count'         => SystemNotification::where('type', 'alert')->count(),
            'today_dispatched'    => SystemNotification::whereDate('created_at', today())->count(),
        ];

        return view('admin.notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Compose Broadcast Notification View
     */
    public function create()
    {
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();
        $users = User::where('is_active', true)->orderBy('name')->take(100)->get();

        return view('admin.notifications.create', compact('centres', 'users'));
    }

    /**
     * Dispatch Notification Broadcast
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'target_group'    => 'required|in:all,centres,students,specific_centre,specific_user',
            'centre_id'       => 'nullable|required_if:target_group,specific_centre|exists:centres,id',
            'user_id'         => 'nullable|required_if:target_group,specific_user|exists:users,id',
            'title'           => 'required|string|max:191',
            'message'         => 'required|string',
            'type'            => 'required|in:info,alert,fee,exam,certificate,system',
            'action_url'      => 'nullable|string|max:255',
            'send_email'      => 'nullable|boolean',
            'send_whatsapp'   => 'nullable|boolean',
        ]);

        $recipientQuery = User::where('is_active', true);

        switch ($validated['target_group']) {
            case 'centres':
                $recipientQuery->whereIn('role', ['centre', 'staff']);
                break;
            case 'students':
                $recipientQuery->where('role', 'student');
                break;
            case 'specific_centre':
                $recipientQuery->where('centre_id', $validated['centre_id']);
                break;
            case 'specific_user':
                $recipientQuery->where('id', $validated['user_id']);
                break;
            case 'all':
            default:
                // All active users
                break;
        }

        $recipients = $recipientQuery->get();

        if ($recipients->isEmpty()) {
            return back()->withInput()->with('error', 'No active recipient accounts matched the selected targeting criteria.');
        }

        DB::transaction(function () use ($recipients, $validated, $request) {
            $senderId = Auth::id();
            $metaData = [
                'broadcast_group' => $validated['target_group'],
                'send_email'      => $request->boolean('send_email'),
                'send_whatsapp'   => $request->boolean('send_whatsapp'),
                'sent_at'         => now()->toIso8601String(),
            ];

            foreach ($recipients as $recipient) {
                SystemNotification::create([
                    'user_id'    => $recipient->id,
                    'sender_id'  => $senderId,
                    'title'      => $validated['title'],
                    'message'    => $validated['message'],
                    'type'       => $validated['type'],
                    'action_url' => $validated['action_url'] ?? null,
                    'data'       => $metaData,
                    'read_at'    => null,
                ]);
            }

            AuditLog::record('notification_broadcast', null, null, [
                'title'           => $validated['title'],
                'target_group'    => $validated['target_group'],
                'recipient_count' => $recipients->count(),
                'type'            => $validated['type'],
            ]);
        });

        $count = $recipients->count();
        return redirect()->route('admin.notifications.index')
            ->with('success', "Notification broadcast dispatched successfully to {$count} recipient account(s).");
    }

    /**
     * Mark single notification as read
     */
    public function markRead(SystemNotification $notification)
    {
        $notification->markAsRead();

        return back()->with('success', "Notification marked as read.");
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead()
    {
        SystemNotification::whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('success', "All unread notifications marked as read.");
    }

    /**
     * Remove / Delete a notification
     */
    public function destroy(SystemNotification $notification)
    {
        $oldData = $notification->toArray();
        $notification->delete();

        AuditLog::record('notification_deleted', $notification, $oldData, null);

        return redirect()->route('admin.notifications.index')
            ->with('success', "Notification record removed.");
    }
}

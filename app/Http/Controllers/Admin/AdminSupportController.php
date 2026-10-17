<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminSupportController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'centre', 'assignee']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('ticket_no', 'like', "%{$s}%")
                    ->orWhere('subject', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%")
                    ->orWhereHas('user', function ($uq) use ($s) {
                        $uq->where('name', 'like', "%{$s}%")
                            ->orWhere('email', 'like', "%{$s}%");
                    })
                    ->orWhereHas('centre', function ($cq) use ($s) {
                        $cq->where('centre_name', 'like', "%{$s}%")
                            ->orWhere('centre_code', 'like', "%{$s}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $tickets = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total_tickets'       => SupportTicket::count(),
            'open_tickets'        => SupportTicket::where('status', 'open')->count(),
            'in_progress_tickets' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved_tickets'    => SupportTicket::whereIn('status', ['resolved', 'closed'])->count(),
            'urgent_tickets'      => SupportTicket::where('priority', 'urgent')->where('status', '!=', 'closed')->count(),
        ];

        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();
        $staffMembers = User::whereIn('role', ['super_admin', 'admin', 'staff'])->where('is_active', true)->orderBy('name')->get();

        return view('admin.support.index', compact('tickets', 'stats', 'centres', 'staffMembers'));
    }

    public function create()
    {
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();
        $staffMembers = User::whereIn('role', ['super_admin', 'admin', 'staff'])->where('is_active', true)->orderBy('name')->get();

        return view('admin.support.create', compact('centres', 'staffMembers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'centre_id'   => 'nullable|exists:centres,id',
            'user_id'     => 'nullable|exists:users,id',
            'category'    => 'required|string|max:50',
            'priority'    => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'subject'     => 'required|string|max:191',
            'description' => 'required|string',
            'attachment'  => 'nullable|file|max:5120',
        ]);

        $ticket = DB::transaction(function () use ($request, $validated) {
            $ticketUser = !empty($validated['user_id'])
                ? $validated['user_id']
                : Auth::id();

            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = FileUploadService::uploadDocument($request->file('attachment'), 'uploads/support');
            }

            $newTicket = SupportTicket::create([
                'ticket_no'     => SupportTicket::generateTicketNo(),
                'user_id'       => $ticketUser,
                'centre_id'     => $validated['centre_id'] ?? null,
                'category'      => $validated['category'],
                'priority'      => $validated['priority'],
                'assigned_to'   => $validated['assigned_to'] ?? null,
                'subject'       => $validated['subject'],
                'description'   => $validated['description'],
                'status'        => !empty($validated['assigned_to']) ? 'in_progress' : 'open',
                'last_reply_at' => now(),
            ]);

            if ($attachmentPath) {
                SupportTicketReply::create([
                    'ticket_id'       => $newTicket->id,
                    'user_id'         => Auth::id(),
                    'message'         => 'Attachment attached with ticket initiation: ' . basename($attachmentPath),
                    'attachment_path' => $attachmentPath,
                    'is_staff_reply'  => true,
                ]);
            }

            AuditLog::record('support_ticket_created', $newTicket, null, $newTicket->toArray());

            return $newTicket;
        });

        return redirect()->route('admin.support.show', $ticket)
            ->with('success', "Support Ticket #{$ticket->ticket_no} created successfully.");
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user', 'centre', 'assignee', 'replies.user']);

        $staffMembers = User::whereIn('role', ['super_admin', 'admin', 'staff'])->where('is_active', true)->orderBy('name')->get();

        return view('admin.support.show', compact('ticket', 'staffMembers'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'message'        => 'required|string',
            'attachment'     => 'nullable|file|max:5120',
            'mark_resolved'  => 'nullable|boolean',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = FileUploadService::uploadDocument($request->file('attachment'), 'uploads/support');
        }

        $reply = DB::transaction(function () use ($ticket, $validated, $attachmentPath) {
            $rep = SupportTicketReply::create([
                'ticket_id'       => $ticket->id,
                'user_id'         => Auth::id(),
                'message'         => $validated['message'],
                'attachment_path' => $attachmentPath,
                'is_staff_reply'  => true,
            ]);

            if (!empty($validated['status'])) {
                $newStatus = $validated['status'];
            } elseif (!empty($validated['mark_resolved'])) {
                $newStatus = 'resolved';
            } else {
                $ticketUser = $ticket->user;
                $newStatus = ($ticketUser && $ticketUser->hasRole('student')) ? 'waiting_for_student' : 'answered';
            }

            $ticket->update([
                'status'        => $newStatus,
                'last_reply_at' => now(),
            ]);

            AuditLog::record('support_ticket_replied', $ticket, null, [
                'status'         => $newStatus,
                'has_attachment' => !empty($attachmentPath),
                'replied_by'     => Auth::id(),
            ]);

            return $rep;
        });

        try {
            \App\Services\NotificationDispatchService::dispatchTicketReply($ticket, $reply);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Notification dispatch failed for ticket {$ticket->ticket_no}: " . $e->getMessage());
        }

        return back()->with('success', 'Your reply has been posted to the ticket.');
    }

    public function assign(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $oldAssignee = $ticket->assigned_to;
        $ticket->assigned_to = $validated['assigned_to'];
        if ($ticket->status === 'open') {
            $ticket->status = 'in_progress';
        }
        $ticket->save();

        AuditLog::record('support_ticket_assigned', $ticket, ['assigned_to' => $oldAssignee], ['assigned_to' => $ticket->assigned_to]);

        return back()->with('success', "Ticket reassigned to " . $ticket->assignee?->name . ".");
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,waiting_for_student,answered,resolved,closed',
        ]);

        $oldStatus = $ticket->status;
        $ticket->update(['status' => $validated['status']]);

        AuditLog::record('support_ticket_status_updated', $ticket, ['status' => $oldStatus], ['status' => $validated['status']]);

        return back()->with('success', "Ticket status updated to " . ucfirst(str_replace('_', ' ', $validated['status'])) . ".");
    }

    public function destroy(SupportTicket $ticket)
    {
        $oldData = $ticket->toArray();
        $ticket->delete();

        AuditLog::record('support_ticket_deleted', $ticket, $oldData, null);

        return redirect()->route('admin.support.index')
            ->with('success', "Ticket #{$ticket->ticket_no} has been archived.");
    }
}

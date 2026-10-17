<?php

namespace App\Http\Controllers\Centre;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CentreSupportController extends Controller
{
    protected function getAuthCentre(): Centre
    {
        $user = Auth::user();
        $centre = $user->centre;

        if (!$centre && $user->isSuperAdmin()) {
            $centre = Centre::where('status', 'active')->first();
        }

        if (!$centre) {
            abort(403, 'No franchise study centre is linked to your user account.');
        }

        return $centre;
    }

    public function index(Request $request)
    {
        $centre = $this->getAuthCentre();
        $query = SupportTicket::where('centre_id', $centre->id)->with('replies');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $tickets = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total' => SupportTicket::where('centre_id', $centre->id)->count(),
            'open' => SupportTicket::where('centre_id', $centre->id)->where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('centre_id', $centre->id)->where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('centre_id', $centre->id)->where('status', 'resolved')->count(),
        ];

        return view('centre.support.index', compact('centre', 'tickets', 'stats'));
    }

    public function create()
    {
        $centre = $this->getAuthCentre();

        return view('centre.support.create', compact('centre'));
    }

    public function store(Request $request)
    {
        $centre = $this->getAuthCentre();
        $user = Auth::user();

        $validated = $request->validate([
            'category' => 'required|in:academic,fees,exam,certificate,wallet,technical,general',
            'priority' => 'required|in:low,medium,high,urgent',
            'subject' => 'required|string|max:191',
            'description' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $ticketNo = SupportTicket::generateTicketNo();

        $ticket = SupportTicket::create([
            'ticket_no' => $ticketNo,
            'user_id' => $user->id,
            'centre_id' => $centre->id,
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'open',
            'last_reply_at' => now(),
        ]);

        if ($request->hasFile('attachment')) {
            $attachmentPath = FileUploadService::uploadDocument($request->file('attachment'), 'uploads/support/attachments');
            SupportTicketReply::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'message' => 'Attached support document for review.',
                'attachment_path' => $attachmentPath,
                'is_staff_reply' => false,
            ]);
        }

        AuditLog::record('centre_ticket_created', $ticket, null, [
            'ticket_no' => $ticketNo,
            'centre_id' => $centre->id,
            'category' => $ticket->category,
        ]);

        return redirect()->route('centre.support.show', $ticket->id)
            ->with('success', "Support ticket [{$ticketNo}] created successfully! Central team will respond shortly.");
    }

    public function show($ticket)
    {
        $centre = $this->getAuthCentre();
        $ticketModel = SupportTicket::withoutGlobalScope('centre_isolation')->findOrFail($ticket);

        if ($ticketModel->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: This ticket belongs to another study centre.');
        }

        $ticketModel->load(['user', 'assignee', 'replies.user']);

        return view('centre.support.show', ['centre' => $centre, 'ticket' => $ticketModel]);
    }

    public function reply(Request $request, $ticket)
    {
        $centre = $this->getAuthCentre();
        $ticketModel = SupportTicket::withoutGlobalScope('centre_isolation')->findOrFail($ticket);

        if ($ticketModel->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: This ticket belongs to another study centre.');
        }

        $validated = $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $attachmentPath = $request->hasFile('attachment')
            ? FileUploadService::uploadDocument($request->file('attachment'), 'uploads/support/attachments')
            : null;

        $reply = SupportTicketReply::create([
            'ticket_id' => $ticketModel->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'attachment_path' => $attachmentPath,
            'is_staff_reply' => false,
        ]);

        $ticketModel->update([
            'status' => 'open',
            'last_reply_at' => now(),
        ]);

        try {
            \App\Services\NotificationDispatchService::dispatchTicketReply($ticketModel, $reply);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Notification dispatch failed for centre ticket {$ticketModel->ticket_no}: " . $e->getMessage());
        }

        return redirect()->route('centre.support.show', $ticketModel->id)
            ->with('success', 'Your reply has been posted successfully.');
    }
}

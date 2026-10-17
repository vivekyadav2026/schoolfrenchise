<?php

namespace App\Models;

use App\Models\Traits\BelongsToCentre;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use HasFactory, SoftDeletes, BelongsToCentre;

    protected $fillable = [
        'ticket_no',
        'user_id',
        'centre_id',
        'category',
        'priority',
        'subject',
        'description',
        'status',
        'assigned_to',
        'last_reply_at',
    ];

    protected function casts(): array
    {
        return [
            'last_reply_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function replies()
    {
        return $this->hasMany(SupportTicketReply::class, 'ticket_id')->latest();
    }

    public static function generateTicketNo(): string
    {
        return 'TCK-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }
}

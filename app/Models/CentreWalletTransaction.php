<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentreWalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'centre_id',
        'type',
        'amount',
        'balance_after',
        'reference_no',
        'description',
        'approved_by',
        'status',
    ];

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

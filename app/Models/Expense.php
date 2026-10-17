<?php

namespace App\Models;

use App\Models\Traits\BelongsToCentre;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes, BelongsToCentre;

    protected $fillable = [
        'centre_id',
        'category',
        'title',
        'amount',
        'expense_date',
        'payment_mode',
        'reference_no',
        'receipt_doc',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'centre_id',
        'campaign_name',
        'platform',
        'budget',
        'amount_spent',
        'start_date',
        'end_date',
        'leads_generated',
        'conversions_count',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'amount_spent' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'leads_generated' => 'integer',
            'conversions_count' => 'integer',
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

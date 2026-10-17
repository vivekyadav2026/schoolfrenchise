<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_no',
        'user_id',
        'centre_id',
        'item_type',
        'item_id',
        'item_name',
        'amount',
        'payment_status',
        'payment_method',
        'transaction_ref',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public static function generateOrderNo(): string
    {
        return 'ORD-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }
}

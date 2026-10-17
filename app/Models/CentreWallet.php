<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentreWallet extends Model
{
    use HasFactory;

    protected $fillable = ['centre_id', 'balance', 'total_credited', 'total_debited', 'status'];

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function transactions()
    {
        return $this->hasMany(CentreWalletTransaction::class, 'centre_id', 'centre_id');
    }
}

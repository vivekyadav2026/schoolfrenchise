<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationLog extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'search_query', 'result_found', 'ip_address', 'user_agent'];

    protected $casts = [
        'result_found' => 'boolean',
    ];
}

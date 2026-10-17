<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'is_marquee',
        'target_audience',
        'attachment_path',
        'status',
        'published_at',
    ];

    protected $casts = [
        'is_marquee' => 'boolean',
        'status' => 'boolean',
        'published_at' => 'date',
    ];
}

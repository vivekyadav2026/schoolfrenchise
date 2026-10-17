<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Download extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'file_path',
        'file_size',
        'version',
        'description',
        'downloads_count',
        'is_free',
        'price',
        'status',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'price' => 'decimal:2',
        'status' => 'boolean',
        'downloads_count' => 'integer',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'centre_id',
        'name',
        'total_fee',
        'admission_fee',
        'tuition_fee',
        'exam_fee',
        'certificate_fee',
        'installments_count',
        'terms',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'total_fee' => 'decimal:2',
            'admission_fee' => 'decimal:2',
            'tuition_fee' => 'decimal:2',
            'exam_fee' => 'decimal:2',
            'certificate_fee' => 'decimal:2',
            'installments_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }
}

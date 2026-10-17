<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResultSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_result_id',
        'course_subject_id',
        'theory_obtained',
        'practical_obtained',
        'viva_obtained',
        'total_obtained',
        'max_marks',
        'grade',
        'is_passed',
    ];

    protected $casts = [
        'theory_obtained' => 'decimal:2',
        'practical_obtained' => 'decimal:2',
        'viva_obtained' => 'decimal:2',
        'total_obtained' => 'decimal:2',
        'max_marks' => 'decimal:2',
        'is_passed' => 'boolean',
    ];

    public function result()
    {
        return $this->belongsTo(ExamResult::class, 'exam_result_id');
    }

    public function subject()
    {
        return $this->belongsTo(CourseSubject::class, 'course_subject_id');
    }
}

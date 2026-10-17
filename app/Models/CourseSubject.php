<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'subject_code',
        'subject_name',
        'theory_max',
        'practical_max',
        'viva_max',
        'total_max',
        'min_pass_marks',
        'sort_order',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marksheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_result_id',
        'student_id',
        'centre_id',
        'course_id',
        'marksheet_no',
        'issue_date',
        'security_hash',
        'is_issued',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'is_issued' => 'boolean',
    ];

    public function result()
    {
        return $this->belongsTo(ExamResult::class, 'exam_result_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}

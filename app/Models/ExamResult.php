<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'centre_id',
        'course_id',
        'total_marks_obtained',
        'total_max_marks',
        'percentage',
        'grade',
        'result_status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'total_marks_obtained' => 'decimal:2',
        'total_max_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
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

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function subjectResults()
    {
        return $this->hasMany(ExamResultSubject::class);
    }

    public function marksheet()
    {
        return $this->hasOne(Marksheet::class);
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    public static function calculateGrade(float $percentage): string
    {
        if ($percentage >= 85) {
            return 'A+';
        }
        if ($percentage >= 70) {
            return 'A';
        }
        if ($percentage >= 60) {
            return 'B';
        }
        if ($percentage >= 50) {
            return 'C';
        }
        if ($percentage >= 40) {
            return 'D';
        }
        return 'F';
    }

    public function getTotalMarksAttribute()
    {
        return $this->total_max_marks;
    }

    public function getObtainedMarksAttribute()
    {
        return $this->total_marks_obtained;
    }
}


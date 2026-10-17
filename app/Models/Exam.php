<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'exam_title',
        'exam_code',
        'session_name',
        'exam_date',
        'status',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function admitCards()
    {
        return $this->hasMany(AdmitCard::class);
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }
}

<?php

namespace App\Models;

use App\Models\Traits\BelongsToCentre;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use HasFactory, SoftDeletes, BelongsToCentre;

    protected $fillable = [
        'centre_id',
        'course_id',
        'batch_name',
        'start_time',
        'end_time',
        'days',
        'status',
    ];

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, Admission::class, 'batch_id', 'id', 'id', 'student_id');
    }
}

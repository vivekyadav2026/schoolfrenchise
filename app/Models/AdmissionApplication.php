<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_no',
        'centre_id',
        'course_id',
        'applicant_name',
        'father_name',
        'mother_name',
        'dob',
        'gender',
        'mobile',
        'email',
        'state_id',
        'district_id',
        'city',
        'address',
        'pincode',
        'highest_qualification',
        'photo_path',
        'id_proof_doc',
        'status',
        'rejection_reason',
        'reviewed_by',
        'converted_student_id',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
        ];
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'converted_student_id');
    }

    public function convertedStudent()
    {
        return $this->belongsTo(Student::class, 'converted_student_id');
    }

    public static function generateApplicationNumber(): string
    {
        return 'APP-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }
}

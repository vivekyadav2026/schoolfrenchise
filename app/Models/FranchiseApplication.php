<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FranchiseApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_no',
        'applicant_name',
        'centre_name',
        'mobile',
        'email',
        'state_id',
        'district_id',
        'city',
        'address',
        'qualification',
        'computers_count',
        'carpet_area_sqft',
        'status',
        'remarks',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Centre extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'centre_code',
        'centre_name',
        'director_name',
        'email',
        'mobile',
        'alternate_mobile',
        'state_id',
        'district_id',
        'city',
        'address',
        'pincode',
        'lab_capacity',
        'centre_logo',
        'director_photo',
        'director_signature',
        'affiliation_date',
        'valid_upto',
        'status',
    ];

    protected $casts = [
        'affiliation_date' => 'date',
        'valid_upto' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staffUsers()
    {
        return $this->hasMany(User::class, 'centre_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function wallet()
    {
        return $this->hasOne(CentreWallet::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(CentreWalletTransaction::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    public function admissionApplications()
    {
        return $this->hasMany(AdmissionApplication::class);
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function paymentReceipts()
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function studyMaterials()
    {
        return $this->hasMany(StudyMaterial::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function marketingExpenses()
    {
        return $this->hasMany(MarketingExpense::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}

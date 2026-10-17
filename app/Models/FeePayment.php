<?php

namespace App\Models;

use App\Models\Traits\BelongsToCentre;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeePayment extends Model
{
    use HasFactory, SoftDeletes, BelongsToCentre;

    protected $fillable = [
        'student_fee_id',
        'admission_id',
        'student_id',
        'centre_id',
        'receipt_no',
        'payment_date',
        'amount',
        'payment_mode',
        'transaction_ref',
        'collected_by',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function paymentReceipt()
    {
        return $this->hasOne(PaymentReceipt::class, 'fee_payment_id');
    }

    public function receipt()
    {
        return $this->hasOne(PaymentReceipt::class, 'fee_payment_id');
    }
}

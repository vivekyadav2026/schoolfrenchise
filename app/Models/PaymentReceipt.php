<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_payment_id',
        'receipt_no',
        'student_id',
        'admission_id',
        'centre_id',
        'amount',
        'payment_mode',
        'receipt_date',
        'qr_token',
        'receipt_pdf_path',
        'issued_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'receipt_date' => 'date',
        ];
    }

    public function payment()
    {
        return $this->belongsTo(FeePayment::class, 'fee_payment_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public static function generateReceiptNo(int $centreId): string
    {
        $year = date('Y');
        $random = strtoupper(bin2hex(random_bytes(3)));
        return "RCP-{$year}-{$centreId}-{$random}";
    }
}

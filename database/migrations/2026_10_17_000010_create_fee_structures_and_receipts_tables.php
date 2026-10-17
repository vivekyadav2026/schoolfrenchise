<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('centre_id')->nullable()->constrained('centres')->cascadeOnDelete();
            $table->string('name', 120);
            $table->decimal('total_fee', 10, 2);
            $table->decimal('admission_fee', 10, 2)->default(0.00);
            $table->decimal('tuition_fee', 10, 2)->default(0.00);
            $table->decimal('exam_fee', 10, 2)->default(0.00);
            $table->decimal('certificate_fee', 10, 2)->default(0.00);
            $table->unsignedInteger('installments_count')->default(1);
            $table->text('terms')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['course_id', 'centre_id']);
        });

        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_payment_id')->constrained('fee_payments')->cascadeOnDelete();
            $table->string('receipt_no', 60)->unique();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->foreignId('centre_id')->constrained('centres')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('payment_mode', 50)->default('cash');
            $table->date('receipt_date');
            $table->string('qr_token', 64)->unique();
            $table->string('receipt_pdf_path', 255)->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['receipt_no', 'centre_id']);
            $table->index(['student_id', 'receipt_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
        Schema::dropIfExists('fee_structures');
    }
};

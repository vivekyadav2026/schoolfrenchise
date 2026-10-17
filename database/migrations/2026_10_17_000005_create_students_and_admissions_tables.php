<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('centre_id')->constrained('centres')->restrictOnDelete();
            $table->string('registration_no', 60)->unique();
            $table->string('roll_no', 60)->unique();
            $table->string('full_name', 191);
            $table->string('father_name', 191);
            $table->string('mother_name', 191)->nullable();
            $table->date('dob');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('mobile', 20);
            $table->string('alternate_mobile', 20)->nullable();
            $table->string('email', 191)->nullable();
            $table->unsignedInteger('state_id');
            $table->unsignedInteger('district_id');
            $table->string('city', 100);
            $table->text('address');
            $table->string('pincode', 10);
            $table->string('highest_qualification', 100)->nullable();
            $table->string('photo_path')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('id_proof_doc')->nullable();
            $table->enum('status', ['enrolled', 'ongoing', 'completed', 'dropped'])->default('enrolled');
            $table->timestamps();

            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('district_id')->references('id')->on('districts');
            $table->index(['registration_no', 'father_name']);
            $table->index(['roll_no']);
        });

        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('centre_id')->constrained('centres')->restrictOnDelete();
            $table->foreignId('course_id')->constrained('courses')->restrictOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batches')->nullOnDelete();
            $table->date('admission_date');
            $table->decimal('course_fee', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('net_fee', 10, 2);
            $table->decimal('total_paid', 10, 2)->default(0.00);
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('centre_id')->constrained('centres');
            $table->unsignedInteger('installment_no');
            $table->string('title', 100);
            $table->date('due_date');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->enum('status', ['pending', 'partial', 'paid'])->default('pending');
            $table->timestamps();
        });

        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_fee_id')->nullable()->constrained('student_fees')->nullOnDelete();
            $table->foreignId('admission_id')->constrained('admissions');
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('centre_id')->constrained('centres');
            $table->string('receipt_no', 60)->unique();
            $table->date('payment_date');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_mode', ['cash', 'upi', 'online', 'cheque', 'bank_transfer'])->default('online');
            $table->string('transaction_ref', 100)->nullable();
            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->index(['receipt_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('student_fees');
        Schema::dropIfExists('admissions');
        Schema::dropIfExists('students');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Modify enum to support pending and refunded states
        DB::statement("ALTER TABLE `online_transactions` MODIFY `status` VARCHAR(30) NOT NULL DEFAULT 'initiated'");

        Schema::table('online_transactions', function (Blueprint $table) {
            $table->foreignId('admission_id')->nullable()->after('student_roll_no')->constrained('admissions')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->after('admission_id')->constrained('students')->nullOnDelete();
            $table->foreignId('fee_payment_id')->nullable()->after('student_id')->constrained('fee_payments')->nullOnDelete();
            $table->string('idempotency_key', 120)->nullable()->unique()->after('gateway_signature');
            $table->text('error_message')->nullable()->after('response_payload');

            $table->index(['status', 'created_at']);
            $table->index('gateway_payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('online_transactions', function (Blueprint $table) {
            $table->dropForeign(['admission_id']);
            $table->dropForeign(['student_id']);
            $table->dropForeign(['fee_payment_id']);
            $table->dropColumn(['admission_id', 'student_id', 'fee_payment_id', 'idempotency_key', 'error_message']);
        });

        DB::statement("ALTER TABLE `online_transactions` MODIFY `status` ENUM('initiated', 'success', 'failed') NOT NULL DEFAULT 'initiated'");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centre_id')->nullable()->constrained('centres')->nullOnDelete();
            $table->string('category', 60)->default('office_supplies');
            $table->string('title', 191);
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->enum('payment_mode', ['cash', 'bank_transfer', 'upi', 'card', 'cheque'])->default('cash');
            $table->string('reference_no', 100)->nullable();
            $table->string('receipt_doc', 255)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['centre_id', 'expense_date']);
        });

        Schema::create('marketing_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centre_id')->nullable()->constrained('centres')->nullOnDelete();
            $table->string('campaign_name', 191);
            $table->string('platform', 60)->default('meta_ads');
            $table->decimal('budget', 10, 2)->default(0.00);
            $table->decimal('amount_spent', 10, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedInteger('leads_generated')->default(0);
            $table->unsignedInteger('conversions_count')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['centre_id', 'platform']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100);
            $table->string('auditable_type', 120)->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'action']);
            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('marketing_expenses');
        Schema::dropIfExists('expenses');
    }
};

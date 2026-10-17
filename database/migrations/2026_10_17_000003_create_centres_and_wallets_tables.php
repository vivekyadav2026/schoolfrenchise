<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('centres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('centre_code', 50)->unique();
            $table->string('centre_name', 191);
            $table->string('director_name', 191);
            $table->string('email', 191);
            $table->string('mobile', 20);
            $table->string('alternate_mobile', 20)->nullable();
            $table->unsignedInteger('state_id');
            $table->unsignedInteger('district_id');
            $table->string('city', 100);
            $table->text('address');
            $table->string('pincode', 10);
            $table->unsignedInteger('lab_capacity')->default(0);
            $table->string('centre_logo')->nullable();
            $table->string('director_photo')->nullable();
            $table->string('director_signature')->nullable();
            $table->date('affiliation_date');
            $table->date('valid_upto')->nullable();
            $table->enum('status', ['pending', 'active', 'suspended', 'rejected'])->default('active');
            $table->timestamps();

            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('district_id')->references('id')->on('districts');
            $table->index(['centre_code', 'status']);
            $table->index(['state_id', 'district_id']);
        });

        Schema::create('centre_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centre_id')->constrained('centres')->cascadeOnDelete();
            $table->decimal('balance', 10, 2)->default(0.00);
            $table->decimal('total_credited', 12, 2)->default(0.00);
            $table->decimal('total_debited', 12, 2)->default(0.00);
            $table->enum('status', ['active', 'frozen'])->default('active');
            $table->timestamps();
        });

        Schema::create('centre_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centre_id')->constrained('centres')->cascadeOnDelete();
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->string('reference_no', 100)->nullable();
            $table->string('description', 255);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('centre_wallet_transactions');
        Schema::dropIfExists('centre_wallets');
        Schema::dropIfExists('centres');
    }
};

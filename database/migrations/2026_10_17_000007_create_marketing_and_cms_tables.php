<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('franchise_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_no', 50)->unique();
            $table->string('applicant_name', 191);
            $table->string('centre_name', 191);
            $table->string('mobile', 20);
            $table->string('email', 191);
            $table->unsignedInteger('state_id');
            $table->unsignedInteger('district_id');
            $table->string('city', 100);
            $table->text('address');
            $table->string('qualification', 100);
            $table->unsignedInteger('computers_count')->default(5);
            $table->unsignedInteger('carpet_area_sqft')->default(300);
            $table->enum('status', ['pending', 'reviewed', 'approved', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('district_id')->references('id')->on('districts');
            $table->index(['application_no']);
            $table->index(['mobile']);
        });

        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('mobile', 20);
            $table->string('email', 191)->nullable();
            $table->unsignedInteger('state_id')->nullable();
            $table->unsignedInteger('district_id')->nullable();
            $table->string('subject', 191);
            $table->text('message')->nullable();
            $table->enum('type', ['demo_request', 'admission_enquiry', 'general_contact'])->default('demo_request');
            $table->enum('status', ['new', 'contacted', 'converted', 'closed'])->default('new');
            $table->timestamps();

            $table->foreign('state_id')->references('id')->on('states')->nullOnDelete();
            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
        });

        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('content')->nullable();
            $table->boolean('is_marquee')->default(false);
            $table->enum('target_audience', ['all', 'centres', 'students', 'public'])->default('all');
            $table->string('attachment_path')->nullable();
            $table->boolean('status')->default(true);
            $table->date('published_at');
            $table->timestamps();
        });

        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title', 191);
            $table->string('category', 100)->default('Events');
            $table->string('image_path', 255);
            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->longText('value')->nullable();
            $table->string('group', 50)->default('general');
            $table->timestamps();
        });

        Schema::create('online_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id', 100)->unique();
            $table->string('order_id', 100)->unique();
            $table->string('student_roll_no', 60)->nullable();
            $table->string('payer_name', 191);
            $table->string('payer_mobile', 20);
            $table->string('payer_email', 191)->nullable();
            $table->string('purpose', 191);
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50)->default('UPI');
            $table->string('gateway_name', 50)->default('TechEraPay');
            $table->string('gateway_payment_id', 100)->nullable();
            $table->string('gateway_signature', 255)->nullable();
            $table->enum('status', ['initiated', 'success', 'failed'])->default('initiated');
            $table->text('response_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_transactions');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('notices');
        Schema::dropIfExists('enquiries');
        Schema::dropIfExists('franchise_applications');
    }
};

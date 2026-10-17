<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Soft Deletes on Core Tables
        foreach (['students', 'admissions', 'courses', 'centres', 'fee_payments', 'expenses', 'support_tickets', 'batches'] as $table) {
            Schema::table($table, function (Blueprint $tableBlueprint) {
                if (!Schema::hasColumn($tableBlueprint->getTable(), 'deleted_at')) {
                    $tableBlueprint->softDeletes();
                }
            });
        }

        // 2. Questions Bank for Examinations
        if (!Schema::hasTable('questions')) {
            Schema::create('questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->foreignId('course_subject_id')->nullable()->constrained('course_subjects')->nullOnDelete();
                $table->text('question_text');
                $table->string('option_a', 255);
                $table->string('option_b', 255);
                $table->string('option_c', 255);
                $table->string('option_d', 255);
                $table->enum('correct_option', ['a', 'b', 'c', 'd'])->default('a');
                $table->unsignedInteger('marks')->default(1);
                $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
                $table->boolean('status')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['course_id', 'status']);
            });
        }

        // 3. Downloads & Resources
        if (!Schema::hasTable('downloads')) {
            Schema::create('downloads', function (Blueprint $table) {
                $table->id();
                $table->string('title', 191);
                $table->string('slug', 191)->unique();
                $table->enum('category', ['software', 'prospectus', 'form', 'template', 'driver', 'study_note'])->default('form');
                $table->string('file_path', 255);
                $table->string('file_size', 50)->nullable();
                $table->string('version', 30)->nullable();
                $table->text('description')->nullable();
                $table->unsignedBigInteger('downloads_count')->default(0);
                $table->boolean('is_free')->default(true);
                $table->decimal('price', 10, 2)->default(0.00);
                $table->boolean('status')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['category', 'status']);
            });
        }

        // 4. Downloadable & Kit Orders
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_no', 50)->unique();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('centre_id')->nullable()->constrained('centres')->nullOnDelete();
                $table->enum('item_type', ['download', 'kit', 'certificates', 'stationery', 'other'])->default('download');
                $table->unsignedBigInteger('item_id')->nullable();
                $table->string('item_name', 191);
                $table->decimal('amount', 10, 2);
                $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
                $table->string('payment_method', 50)->default('online');
                $table->string('transaction_ref', 100)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['order_no', 'payment_status']);
                $table->index(['centre_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('downloads');
        Schema::dropIfExists('questions');

        foreach (['students', 'admissions', 'courses', 'centres', 'fee_payments', 'expenses', 'support_tickets', 'batches'] as $table) {
            Schema::table($table, function (Blueprint $tableBlueprint) {
                if (Schema::hasColumn($tableBlueprint->getTable(), 'deleted_at')) {
                    $tableBlueprint->dropSoftDeletes();
                }
            });
        }
    }
};

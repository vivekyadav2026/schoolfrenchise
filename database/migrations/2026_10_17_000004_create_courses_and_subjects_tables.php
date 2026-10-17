<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('category_id')->nullable();
            $table->string('course_code', 50)->unique();
            $table->string('course_name', 191);
            $table->string('slug', 191)->unique();
            $table->string('duration', 50);
            $table->string('eligibility', 100);
            $table->decimal('default_course_fee', 10, 2)->default(0.00);
            $table->decimal('registration_fee', 10, 2)->default(0.00);
            $table->decimal('exam_fee', 10, 2)->default(0.00);
            $table->decimal('certificate_fee', 10, 2)->default(0.00);
            $table->longText('syllabus')->nullable();
            $table->string('thumbnail')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('course_categories')->nullOnDelete();
            $table->index(['slug', 'status']);
        });

        Schema::create('course_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('subject_code', 50);
            $table->string('subject_name', 191);
            $table->unsignedInteger('theory_max')->default(70);
            $table->unsignedInteger('practical_max')->default(20);
            $table->unsignedInteger('viva_max')->default(10);
            $table->unsignedInteger('total_max')->default(100);
            $table->unsignedInteger('min_pass_marks')->default(40);
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();
        });

        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centre_id')->constrained('centres')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('batch_name', 100);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('days', 100)->default('Mon-Sat');
            $table->enum('status', ['active', 'completed', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
        Schema::dropIfExists('course_subjects');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_categories');
    }
};

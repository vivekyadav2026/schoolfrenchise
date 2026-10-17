<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centre_id')->constrained('centres')->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'late', 'leave', 'holiday'])->default('present');
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->string('remarks', 255)->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['batch_id', 'student_id', 'attendance_date'], 'unique_batch_student_date_att');
            $table->index(['centre_id', 'attendance_date']);
        });

        Schema::create('study_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centre_id')->nullable()->constrained('centres')->nullOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('course_subject_id')->nullable()->constrained('course_subjects')->nullOnDelete();
            $table->string('title', 191);
            $table->string('slug', 191)->unique();
            $table->enum('material_type', ['pdf', 'video', 'notes', 'syllabus', 'sample_paper', 'link'])->default('pdf');
            $table->string('file_path', 255)->nullable();
            $table->string('external_url', 255)->nullable();
            $table->string('file_size', 50)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index(['course_id', 'status']);
            $table->index(['centre_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_materials');
        Schema::dropIfExists('attendances');
    }
};

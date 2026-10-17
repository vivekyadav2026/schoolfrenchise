<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('exam_title', 191);
            $table->string('exam_code', 50)->unique();
            $table->string('session_name', 50);
            $table->date('exam_date');
            $table->enum('status', ['scheduled', 'ongoing', 'evaluated', 'published'])->default('scheduled');
            $table->timestamps();
        });

        Schema::create('admit_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('centre_id')->constrained('centres');
            $table->string('admit_card_no', 60)->unique();
            $table->string('exam_venue', 191);
            $table->text('instructions')->nullable();
            $table->timestamps();
        });

        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->nullOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('centre_id')->constrained('centres');
            $table->foreignId('course_id')->constrained('courses');
            $table->decimal('total_marks_obtained', 6, 2);
            $table->decimal('total_max_marks', 6, 2);
            $table->decimal('percentage', 5, 2);
            $table->string('grade', 10);
            $table->enum('result_status', ['passed', 'failed', 'withheld'])->default('passed');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'course_id']);
        });

        Schema::create('exam_result_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_result_id')->constrained('exam_results')->cascadeOnDelete();
            $table->foreignId('course_subject_id')->constrained('course_subjects')->cascadeOnDelete();
            $table->decimal('theory_obtained', 5, 2)->default(0.00);
            $table->decimal('practical_obtained', 5, 2)->default(0.00);
            $table->decimal('viva_obtained', 5, 2)->default(0.00);
            $table->decimal('total_obtained', 5, 2);
            $table->decimal('max_marks', 5, 2);
            $table->string('grade', 5)->nullable();
            $table->boolean('is_passed')->default(true);
            $table->timestamps();
        });

        Schema::create('marksheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_result_id')->unique()->constrained('exam_results')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('centre_id')->constrained('centres');
            $table->foreignId('course_id')->constrained('courses');
            $table->string('marksheet_no', 60)->unique();
            $table->date('issue_date');
            $table->string('security_hash', 64)->nullable();
            $table->boolean('is_issued')->default(true);
            $table->timestamps();

            $table->index(['marksheet_no']);
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_result_id')->unique()->constrained('exam_results')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('centre_id')->constrained('centres');
            $table->foreignId('course_id')->constrained('courses');
            $table->string('certificate_no', 60)->unique();
            $table->date('issue_date');
            $table->string('security_hash', 64)->nullable();
            $table->string('qr_code_url', 255)->nullable();
            $table->enum('status', ['issued', 'revoked'])->default('issued');
            $table->timestamps();

            $table->index(['certificate_no']);
        });

        Schema::create('verification_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['certificate', 'marksheet', 'admission', 'centre']);
            $table->string('search_query', 191);
            $table->boolean('result_found')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_logs');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('marksheets');
        Schema::dropIfExists('exam_result_subjects');
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('admit_cards');
        Schema::dropIfExists('exams');
    }
};

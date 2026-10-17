<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_no', 50)->unique();
            $table->foreignId('centre_id')->constrained('centres')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('applicant_name', 191);
            $table->string('father_name', 191);
            $table->string('mother_name', 191)->nullable();
            $table->date('dob');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('mobile', 20);
            $table->string('email', 191)->nullable();
            $table->unsignedInteger('state_id');
            $table->unsignedInteger('district_id');
            $table->string('city', 100);
            $table->text('address');
            $table->string('pincode', 10);
            $table->string('highest_qualification', 100)->nullable();
            $table->string('photo_path')->nullable();
            $table->string('id_proof_doc')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('converted_student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->timestamps();

            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('district_id')->references('id')->on('districts');
            $table->index(['application_no', 'status']);
            $table->index(['centre_id', 'course_id']);
            $table->index(['mobile']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_applications');
    }
};

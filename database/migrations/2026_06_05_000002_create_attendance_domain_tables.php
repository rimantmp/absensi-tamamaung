<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('academic_year');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('nis')->unique();
            $table->string('nisn')->nullable()->unique();
            $table->string('name');
            $table->string('gender', 16);
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->string('barcode_value')->unique();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in_time')->nullable();
            $table->string('status');
            $table->string('input_type');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['student_id', 'date']);
        });

        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->time('school_start_time')->default('07:00:00');
            $table->time('late_time_limit')->default('07:15:00');
            $table->json('active_days')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_settings');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('students');
        Schema::dropIfExists('classes');
    }
};

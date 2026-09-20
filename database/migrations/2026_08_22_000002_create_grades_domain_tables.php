<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 16)->nullable();
            $table->timestamps();
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->string('school_year', 16);
            $table->string('semester', 16);
            $table->decimal('score_tugas', 5, 2)->nullable();
            $table->decimal('score_mid', 5, 2)->nullable();
            $table->decimal('score_semester', 5, 2)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['student_id', 'subject_id', 'school_year', 'semester']);
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('to_class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('school_year', 16);
            $table->string('decision', 16);
            $table->decimal('average', 5, 2)->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->unsignedTinyInteger('level')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('level');
        });
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('subjects');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->string('school_id');
            $table->date('date');
            $table->unsignedBigInteger('class_wise_student_id');
            $table->enum('status', ['Present', 'Absent', 'Late', 'Leave']);
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->timestamps();

            $table->foreign('class_wise_student_id')->references('id')->on('class_wise_student_data')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};

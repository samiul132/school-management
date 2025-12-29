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
        Schema::create('class_wise_student_data', function (Blueprint $table) {
            $table->id();
            $table->string('school_id');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('version_id')->nullable();
            $table->unsignedBigInteger('session_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->integer('class_roll')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->boolean('has_transport')->default(0)->nullable();
            $table->timestamps();
            
            $table->foreign('class_id')->references('id')->on('class_management')->onDelete('cascade');
            $table->foreign('version_id')->references('id')->on('version_management')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('session_management')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('section_management')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('student_profiles')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('shift_management')->onDelete('cascade');
            
            $table->unique(['student_id', 'class_id', 'session_id'], 'unique_student_class_session');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_wise_student_data');
    }
};

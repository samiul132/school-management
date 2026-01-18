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
        Schema::create('custom_routines', function (Blueprint $table) {
            $table->id();
            $table->string('school_id');
            $table->date('date');
            $table->json('periods')->nullable();
            $table->json('teacher_wise_data')->nullable();
            $table->json('class_wise_data')->nullable();
            $table->timestamps();
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_routines');
    }
};

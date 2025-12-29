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
        Schema::create('subject_assign_details', function (Blueprint $table) {
            $table->id();
            $table->string('school_id');
            $table->unsignedBigInteger('assign_id');
            $table->unsignedBigInteger('subject_id');
            $table->timestamps();

            $table->foreign('assign_id')->references('id')->on('subject_assigns')->cascadeOnDelete();
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_assign_details');
    }
};

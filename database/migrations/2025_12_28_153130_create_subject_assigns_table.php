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
        Schema::create('subject_assigns', function (Blueprint $table) {
            $table->id();
            $table->string('school_id');
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('version_id');
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('section_id');
            $table->timestamps();

            $table->foreign('session_id')->references('id')->on('session_management')->cascadeOnDelete();
            $table->foreign('version_id')->references('id')->on('version_management')->cascadeOnDelete();
            $table->foreign('shift_id')->references('id')->on('shift_management')->cascadeOnDelete();
            $table->foreign('class_id')->references('id')->on('class_management')->cascadeOnDelete();
            $table->foreign('section_id')->references('id')->on('section_management')->cascadeOnDelete();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_assigns');
    }
};

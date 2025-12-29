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
        Schema::create('wavers', function (Blueprint $table) {
            $table->id();
            $table->string('school_id');
            $table->unsignedBigInteger('class_wise_student_id');
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('version_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('section_id');
            $table->unsignedBigInteger('month_id');
            $table->unsignedBigInteger('head_id');
            $table->integer('roll')->nullable();
            $table->enum('waver_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('waver_amount', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('class_wise_student_id')->references('id')->on('class_wise_student_data')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('session_management')->onDelete('cascade');
            $table->foreign('version_id')->references('id')->on('version_management')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('class_management')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('section_management')->onDelete('cascade');
            $table->foreign('month_id')->references('id')->on('month_management')->onDelete('cascade');
            $table->foreign('head_id')->references('id')->on('fee_heads')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wavers');
    }
};

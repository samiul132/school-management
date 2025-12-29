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
        Schema::create('student_wise_fee_assigns', function (Blueprint $table) {
            $table->id();
            $table->string('school_id');
            $table->unsignedBigInteger('assign_id');  
            $table->unsignedBigInteger('head_id');  
            $table->unsignedBigInteger('class_wise_student_id');
            $table->unsignedBigInteger('month_id');
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('waver_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2)->default(0);
            $table->decimal('payble_amount', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('assign_id')->references('id')->on('fee_assigns')->onDelete('cascade');
            $table->foreign('head_id')->references('id')->on('fee_heads')->onDelete('cascade');
            $table->foreign('class_wise_student_id')->references('id')->on('class_wise_student_data')->onDelete('cascade');
            $table->foreign('month_id')->references('id')->on('month_management')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_wise_fee_assigns');
    }
};

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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->date('payment_date'); 
            $table->string('school_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('class_wise_student_id');
            $table->unsignedBigInteger('month_id');
            $table->decimal('total_paid_amount', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('cash_banks')->onDelete('cascade');
            $table->foreign('class_wise_student_id')->references('id')->on('class_wise_student_data')->onDelete('cascade');
            $table->foreign('month_id')->references('id')->on('month_management')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

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
        Schema::create('staff_salary_payment_details', function (Blueprint $table) {
            $table->id();
            $table->string('school_id');
            $table->integer('payment_id');
            $table->integer('salary_details_id');
            $table->integer('staff_id');
            $table->decimal('paid_amount', 11, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_salary_payment_details');
    }
};

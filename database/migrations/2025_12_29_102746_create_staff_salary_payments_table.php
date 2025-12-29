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
        Schema::create('staff_salary_payments', function (Blueprint $table) {
            $table->id();
            $table->string('school_id'); 
            $table->integer('sheet_id');
            $table->integer('acc_id');
            $table->date('date');
            $table->decimal('total_paid', 11, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_salary_payments');
    }
};

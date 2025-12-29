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
        Schema::create('staff_salaries', function (Blueprint $table) {
            $table->id();
            $table->string('school_id');
            $table->text('title');
            $table->string('month', 2);
            $table->integer('year');
            $table->decimal('total_salary', 11, 2)->default(0);
            $table->decimal('total_advance', 11, 2)->default(0);
            $table->decimal('total_over_time', 11, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_salaries');
    }
};

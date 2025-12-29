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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->string('school_id');
            $table->unsignedBigInteger('staff_id');
            $table->date('application_date');
            $table->date('leave_from_date');
            $table->date('leave_to_date');
            $table->text('reason_of_leave')->nullable();
            $table->integer('total_leave')->default(0);
            $table->enum('status', ['PENDING', 'APPROVED', 'DECLINED'])->default('PENDING');
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staff')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};

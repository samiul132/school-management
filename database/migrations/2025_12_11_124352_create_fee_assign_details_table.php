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
        Schema::create('fee_assign_details', function (Blueprint $table) {
            $table->id();
            $table->string('school_id');
            $table->unsignedBigInteger('fee_assign_id');
            $table->unsignedBigInteger('head_id');
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('fee_assign_id')->references('id')->on('fee_assigns')->onDelete('cascade');
            $table->foreign('head_id')->references('id')->on('fee_heads')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_assign_details');
    }
};

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
        Schema::create('cash_transfers', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('school_id');
            $table->unsignedBigInteger('from_account');
            $table->unsignedBigInteger('to_account');
            $table->decimal('amount', 15, 2);
            $table->longText('description')->nullable();
            $table->timestamps();

            $table->foreign('from_account')->references('id')->on('cash_banks')->onDelete('cascade');
            $table->foreign('to_account')->references('id')->on('cash_banks')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transfers');
    }
};

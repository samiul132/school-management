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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('school_id');
            $table->date('date')->nullable();
            $table->enum('voucher_type', ['DEBIT', 'CREDIT']);
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('subsidiaries_id')->nullable();
            $table->unsignedBigInteger('head_id')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->longText('description')->nullable();
            $table->string('module_name')->nullable();
            $table->unsignedBigInteger('ref_module_id')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('cash_banks')->onDelete('set null');
            $table->foreign('subsidiaries_id')->references('id')->on('subsidiaries')->onDelete('set null');
            $table->foreign('head_id')->references('id')->on('account_heads')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};

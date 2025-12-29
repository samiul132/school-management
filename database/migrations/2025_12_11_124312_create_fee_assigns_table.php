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
        Schema::create('fee_assigns', function (Blueprint $table) {
            $table->id();
            $table->string('school_id');
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('version_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('month_id');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('session_id')->references('id')->on('session_management')->onDelete('cascade');
            $table->foreign('version_id')->references('id')->on('version_management')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('class_management')->onDelete('cascade');
            $table->foreign('month_id')->references('id')->on('month_management')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_assigns');
    }
};

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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id')->nullable()->after('id'); 
            $table->unsignedBigInteger('student_id')->nullable()->after('school_id'); 
            $table->enum('type', ['ADMIN', 'SUPER_ADMIN', 'STUDENT'])->default('STUDENT')->after('school_id');
            $table->string('user_name')->nullable()->after('name');
            
            $table->foreign('school_id')->references('id')->on('school_settings')->onDelete('set null');
            $table->foreign('student_id')->references('id')->on('student_profiles')->onDelete('set null'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};



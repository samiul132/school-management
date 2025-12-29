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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('school_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('fathers_name')->nullable();
            $table->string('mothers_name')->nullable();
            $table->boolean('is_teachers')->default(0);
            $table->string('designation');
            $table->enum('maritial_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('qualification')->nullable();
            $table->text('address')->nullable();
            $table->string('nid')->nullable();
            $table->string('subsidiaries_id')->nullable();
            $table->decimal('basic_salary', 10, 2);
            $table->integer('working_hour');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};

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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('school_id');
            $table->string('student_name'); 
            $table->string('id_card_number')->nullable();
            $table->string('student_image')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('religion')->nullable();
            $table->string('birth_certificate_number')->nullable();
            $table->string('nationality')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_profession')->nullable();
            $table->string('father_mobile_number')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_profession')->nullable();
            $table->string('mother_mobile_number')->nullable();
            $table->string('local_guardian_name')->nullable(); 
            $table->string('local_guardian_profession')->nullable();
            $table->string('local_guardian_mobile_number')->nullable();
            $table->string('present_village')->nullable();
            $table->string('present_post_office')->nullable();
            $table->string('present_upazila_id')->nullable();
            $table->string('present_district_id')->nullable();
            $table->string('permanent_village')->nullable();
            $table->string('permanent_post_office')->nullable();
            $table->string('permanent_upazila_id')->nullable();
            $table->string('permanent_district_id')->nullable();
            $table->string('previous_institute_name')->nullable();
            $table->string('previous_institute_class')->nullable();
            $table->string('previous_institute_section')->nullable();
            $table->string('previous_institute_roll')->nullable();
            $table->string('previous_institute_result')->nullable();
            $table->string('tc_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};

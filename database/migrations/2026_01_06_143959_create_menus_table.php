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
        Schema::create('menus', function (Blueprint $table) {
            $table->id(); 
            $table->text('title')->nullable();
            $table->string('permition', 100)->nullable();
            $table->string('icon', 50)->nullable();
            $table->integer('sorting')->default(1);
            $table->boolean('is_primary_menu')->default(0);
            $table->boolean('show_on_navbar')->default(0);
            $table->boolean('is_section')->default(0);
            $table->integer('parent_id')->nullable();
            $table->text('backend_route')->nullable();
            $table->text('frontend_route')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};

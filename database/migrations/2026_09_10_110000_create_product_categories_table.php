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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('name_de')->nullable();
            $table->string('name_es')->nullable();
            $table->string('name_tr')->nullable();
            $table->string('name_zh')->nullable();
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->string('disk')->default('public');
            $table->foreignId('parent_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->enum('status', ['active', 'pending'])->default('active');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};

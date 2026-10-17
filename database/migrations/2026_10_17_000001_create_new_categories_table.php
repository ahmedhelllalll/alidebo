<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('new_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedTinyInteger('level')->default(1);
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('name_de')->nullable();
            $table->string('name_es')->nullable();
            $table->string('name_tr')->nullable();
            $table->string('name_zh')->nullable();
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->enum('status', ['active', 'pending'])->default('active');
            $table->string('disk')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('new_categories')->cascadeOnDelete();
            $table->index('parent_id');
            $table->index('level');
            $table->fullText(['name_en', 'name_ar', 'name_de', 'name_es', 'name_tr', 'name_zh'], 'new_categories_names_fulltext');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_categories');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_profile_id')->constrained('business_profiles')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('sku')->nullable();
            $table->string('unit');
            $table->integer('moq')->nullable();
            $table->decimal('base_price', 10, 2)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

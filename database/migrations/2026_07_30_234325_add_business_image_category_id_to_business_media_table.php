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
        Schema::table('business_media', function (Blueprint $table) {
            if (!Schema::hasColumn('business_media', 'business_image_category_id')) {
                $table->foreignId('business_image_category_id')->nullable()->constrained('business_image_categories')->nullOnDelete();
            } else {
                $table->foreign('business_image_category_id')->references('id')->on('business_image_categories')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_media', function (Blueprint $table) {
            $table->dropForeign(['business_image_category_id']);
            $table->dropColumn('business_image_category_id');
        });
    }
};

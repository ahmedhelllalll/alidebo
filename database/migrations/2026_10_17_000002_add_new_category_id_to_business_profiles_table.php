<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('new_category_id')->nullable()->after('category_id');
            $table->boolean('category_needs_review')->default(false)->after('new_category_id');

            $table->foreign('new_category_id')->references('id')->on('new_categories')->nullOnDelete();
            $table->index('new_category_id');
            $table->index('category_needs_review');
        });
    }

    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropForeign(['new_category_id']);
            $table->dropIndex(['new_category_id']);
            $table->dropIndex(['category_needs_review']);
            $table->dropColumn(['new_category_id', 'category_needs_review']);
        });
    }
};

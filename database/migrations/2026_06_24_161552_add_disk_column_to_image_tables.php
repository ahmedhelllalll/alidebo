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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('disk')->default('public')->after('icon');
        });

        Schema::table('business_profiles', function (Blueprint $table) {
            $table->string('disk')->default('public')->after('cover');
        });

        Schema::table('business_media', function (Blueprint $table) {
            $table->string('disk')->default('public')->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('disk');
        });

        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn('disk');
        });

        Schema::table('business_media', function (Blueprint $table) {
            $table->dropColumn('disk');
        });
    }
};

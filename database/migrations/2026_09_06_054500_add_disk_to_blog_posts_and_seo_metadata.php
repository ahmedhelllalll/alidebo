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
        if (Schema::hasTable('blog_posts') && !Schema::hasColumn('blog_posts', 'disk')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->string('disk')->default('public')->after('media_url');
            });
        }

        if (Schema::hasTable('seo_metadata') && !Schema::hasColumn('seo_metadata', 'disk')) {
            Schema::table('seo_metadata', function (Blueprint $table) {
                $table->string('disk')->default('public')->after('og_image');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('blog_posts') && Schema::hasColumn('blog_posts', 'disk')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->dropColumn('disk');
            });
        }

        if (Schema::hasTable('seo_metadata') && Schema::hasColumn('seo_metadata', 'disk')) {
            Schema::table('seo_metadata', function (Blueprint $table) {
                $table->dropColumn('disk');
            });
        }
    }
};

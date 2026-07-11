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
        try {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropFullText(['name_en', 'name_ar']);
            });
        } catch (\Exception $e) {
            // Index might not exist or already dropped
        }
        
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'name_de')) {
                $table->string('name_de')->nullable()->after('name_ar');
                $table->string('name_es')->nullable()->after('name_de');
                $table->string('name_tr')->nullable()->after('name_es');
                $table->string('name_zh')->nullable()->after('name_tr');
            }

            $table->fullText(['name_en', 'name_ar', 'name_de', 'name_es', 'name_tr', 'name_zh'], 'categories_names_fulltext');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropFullText(['name_en', 'name_ar', 'name_de', 'name_es', 'name_tr', 'name_zh']);
            $table->fullText(['name_en', 'name_ar']);

            $table->dropColumn(['name_de', 'name_es', 'name_tr', 'name_zh']);
        });
    }
};

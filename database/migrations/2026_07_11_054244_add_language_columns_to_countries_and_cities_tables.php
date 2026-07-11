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
        Schema::table('countries', function (Blueprint $table) {
            $table->string('name_de')->nullable()->after('name_ar');
            $table->string('name_es')->nullable()->after('name_de');
            $table->string('name_tr')->nullable()->after('name_es');
            $table->string('name_zh')->nullable()->after('name_tr');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->string('name_de')->nullable()->after('name_ar');
            $table->string('name_es')->nullable()->after('name_de');
            $table->string('name_tr')->nullable()->after('name_es');
            $table->string('name_zh')->nullable()->after('name_tr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['name_de', 'name_es', 'name_tr', 'name_zh']);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['name_de', 'name_es', 'name_tr', 'name_zh']);
        });
    }
};

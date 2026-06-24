<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->string('name_en')->after('id');
            $table->string('name_ar')->after('name_en');
        });

        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->string('name_en')->after('id');
            $table->string('name_ar')->after('name_en');
            $table->enum('status', ['active', 'pending'])->default('active')->after('code');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->string('name_en')->after('id');
            $table->string('name_ar')->after('name_en');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'name_ar']);
            $table->string('name')->after('id');
        });

        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'name_ar', 'status']);
            $table->string('name')->after('id');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'name_ar']);
            $table->string('name')->after('id');
        });
    }
};

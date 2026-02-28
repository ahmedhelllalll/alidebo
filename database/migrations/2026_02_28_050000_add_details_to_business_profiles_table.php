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
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('user_id')->constrained();
            $table->foreignId('city_id')->nullable()->after('category_id')->constrained();
            $table->string('whatsapp')->nullable()->after('description');
            $table->string('phone')->nullable()->after('whatsapp');
            $table->string('address')->nullable()->after('phone');
            $table->string('cover')->nullable()->after('logo');
        });
    }

    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn(['category_id', 'city_id', 'whatsapp', 'phone', 'address', 'cover']);
        });
    }
};

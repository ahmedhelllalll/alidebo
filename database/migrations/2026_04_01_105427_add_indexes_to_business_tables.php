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
            $table->index('status');
        });

        Schema::table('business_views', function (Blueprint $table) {
            $table->index(['business_profile_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('business_views', function (Blueprint $table) {
            $table->dropIndex(['business_profile_id', 'created_at']);
        });
    }
};

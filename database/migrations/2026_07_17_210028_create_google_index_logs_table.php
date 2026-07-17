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
        Schema::create('google_index_logs', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->nullableMorphs('indexable');
            $table->string('status')->default('pending'); // pending, submitted, failed
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_index_logs');
    }
};

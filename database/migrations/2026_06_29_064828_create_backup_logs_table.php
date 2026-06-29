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
        Schema::create('backup_logs', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->timestamp('backup_date');
            $table->bigInteger('file_size')->default(0);
            $table->string('status')->default('in_progress'); // in_progress, success, failed
            $table->boolean('stored_locally')->default(false);
            $table->boolean('stored_on_r2')->default(false);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_logs');
    }
};

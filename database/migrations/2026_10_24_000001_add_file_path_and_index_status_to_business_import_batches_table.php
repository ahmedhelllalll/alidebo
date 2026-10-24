<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_import_batches', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('original_file_name');
            $table->string('target_sheet')->nullable()->after('file_path');
            $table->string('status')->default('queued')->change();

            $table->index('status');
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('business_import_batches', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['status']);
            $table->dropColumn(['file_path', 'target_sheet']);
            $table->string('status')->default('processing')->change();
        });
    }
};

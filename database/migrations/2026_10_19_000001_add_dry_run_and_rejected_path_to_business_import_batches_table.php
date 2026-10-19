<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_import_batches', function (Blueprint $table) {
            $table->boolean('is_dry_run')->default(false)->after('status');
            $table->integer('updated_rows')->default(0)->after('imported_rows');
            $table->string('rejected_file_path')->nullable()->after('error_log_path');
        });
    }

    public function down(): void
    {
        Schema::table('business_import_batches', function (Blueprint $table) {
            $table->dropColumn(['is_dry_run', 'updated_rows', 'rejected_file_path']);
        });
    }
};

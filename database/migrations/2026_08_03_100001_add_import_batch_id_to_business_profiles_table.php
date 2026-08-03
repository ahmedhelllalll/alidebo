<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('import_batch_id')->nullable()->after('disk');
            
            $table->foreign('import_batch_id')
                  ->references('id')
                  ->on('business_import_batches')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropForeign(['import_batch_id']);
            $table->dropColumn('import_batch_id');
        });
    }
};

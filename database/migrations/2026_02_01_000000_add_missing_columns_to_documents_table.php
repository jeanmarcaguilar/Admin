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
        Schema::table('documents', function (Blueprint $table) {
            // Only add columns that don't already exist
            if (!Schema::hasColumn('documents', 'file_type')) {
                $table->string('file_type')->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('documents', 'uploaded_on')) {
                $table->date('uploaded_on')->nullable()->after('status');
            }
            // Note: code and description columns already exist in the database
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['file_type', 'uploaded_on']);
        });
    }
};

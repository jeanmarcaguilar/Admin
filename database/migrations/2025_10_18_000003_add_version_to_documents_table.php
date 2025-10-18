<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('documents', 'version')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('version', 32)->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('documents', 'version')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropColumn('version');
            });
        }
    }
};

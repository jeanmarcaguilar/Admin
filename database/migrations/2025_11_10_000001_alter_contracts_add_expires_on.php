<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('contracts') && !Schema::hasColumn('contracts', 'expires_on')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->date('expires_on')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('contracts') && Schema::hasColumn('contracts', 'expires_on')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->dropColumn('expires_on');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContractManagementToCaseFiles extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('case_files')) {
            return;
        }

        Schema::table('case_files', function (Blueprint $table) {
            if (!Schema::hasColumn('case_files', 'contract_type')) {
                $table->string('contract_type')->nullable()->after('hearing_time');
            }
            if (!Schema::hasColumn('case_files', 'contract_number')) {
                $table->string('contract_number')->nullable()->after('contract_type');
            }
            if (!Schema::hasColumn('case_files', 'contract_date')) {
                $table->date('contract_date')->nullable()->after('contract_number');
            }
            if (!Schema::hasColumn('case_files', 'contract_expiration')) {
                $table->date('contract_expiration')->nullable()->after('contract_date');
            }
            if (!Schema::hasColumn('case_files', 'contract_notes')) {
                $table->text('contract_notes')->nullable()->after('contract_expiration');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('case_files')) {
            return;
        }

        $cols = [];
        foreach (['contract_type', 'contract_number', 'contract_date', 'contract_expiration', 'contract_notes'] as $col) {
            if (Schema::hasColumn('case_files', $col)) {
                $cols[] = $col;
            }
        }

        if (!empty($cols)) {
            Schema::table('case_files', function (Blueprint $table) use ($cols) {
                $table->dropColumn($cols);
            });
        }
    }
};

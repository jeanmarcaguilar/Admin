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
        Schema::table('case_files', function (Blueprint $table) {
            $table->string('contract_type')->nullable()->after('hearing_time');
            $table->string('contract_number')->nullable()->after('contract_type');
            $table->date('contract_date')->nullable()->after('contract_number');
            $table->date('contract_expiration')->nullable()->after('contract_date');
            $table->text('contract_notes')->nullable()->after('contract_expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            $table->dropColumn([
                'contract_type',
                'contract_number',
                'contract_date',
                'contract_expiration',
                'contract_notes'
            ]);
        });
    }
};

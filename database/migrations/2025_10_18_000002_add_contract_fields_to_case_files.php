<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            // Contract type (employee, employment, service, etc.)
            $table->enum('contract_type', [
                'employee', 'employment', 'service', 'other'
            ])->nullable()->after('hearing_time');
            
            // Contract dates
            $table->date('contract_date')->nullable()->after('contract_type');
            $table->date('contract_expiration')->nullable()->after('contract_date');
            
            // Contract status (active, expired, terminated, renewed)
            $table->enum('contract_status', [
                'active', 'expired', 'terminated', 'renewed'
            ])->default('active')->after('contract_expiration');
            
            // Contract document reference (path to the document)
            $table->string('contract_document_path')->nullable()->after('contract_status');
            
            // Contract notes
            $table->text('contract_notes')->nullable()->after('contract_document_path');
            
            // Indexes for better performance
            $table->index('contract_type');
            $table->index('contract_status');
            $table->index('contract_expiration');
        });
    }

    public function down(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            $table->dropColumn([
                'contract_type',
                'contract_date',
                'contract_expiration',
                'contract_status',
                'contract_document_path',
                'contract_notes'
            ]);
            
            $table->dropIndex(['contract_type']);
            $table->dropIndex(['contract_status']);
            $table->dropIndex(['contract_expiration']);
        });
    }
};

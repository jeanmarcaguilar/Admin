<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $dbName = DB::getDatabaseName();
            $rows = DB::select(
                'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
                [$dbName, $table, $indexName]
            );
            return !empty($rows);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function up(): void
    {
        if (!Schema::hasTable('case_files')) {
            return;
        }

        Schema::table('case_files', function (Blueprint $table) {
            // Contract type (employee, employment, service, etc.)
            if (!Schema::hasColumn('case_files', 'contract_type')) {
                $table->enum('contract_type', [
                    'employee', 'employment', 'service', 'other'
                ])->nullable()->after('hearing_time');
            }

            // Contract dates
            if (!Schema::hasColumn('case_files', 'contract_date')) {
                $table->date('contract_date')->nullable()->after('contract_type');
            }
            if (!Schema::hasColumn('case_files', 'contract_expiration')) {
                $table->date('contract_expiration')->nullable()->after('contract_date');
            }

            // Contract status (active, expired, terminated, renewed)
            if (!Schema::hasColumn('case_files', 'contract_status')) {
                $table->enum('contract_status', [
                    'active', 'expired', 'terminated', 'renewed'
                ])->default('active')->after('contract_expiration');
            }

            // Contract document reference (path to the document)
            if (!Schema::hasColumn('case_files', 'contract_document_path')) {
                $table->string('contract_document_path')->nullable()->after('contract_status');
            }

            // Contract notes
            if (!Schema::hasColumn('case_files', 'contract_notes')) {
                $table->text('contract_notes')->nullable()->after('contract_document_path');
            }
        });

        // Indexes for better performance (idempotent)
        if (Schema::hasColumn('case_files', 'contract_type') && !$this->indexExists('case_files', 'case_files_contract_type_index')) {
            Schema::table('case_files', fn (Blueprint $table) => $table->index('contract_type'));
        }
        if (Schema::hasColumn('case_files', 'contract_status') && !$this->indexExists('case_files', 'case_files_contract_status_index')) {
            Schema::table('case_files', fn (Blueprint $table) => $table->index('contract_status'));
        }
        if (Schema::hasColumn('case_files', 'contract_expiration') && !$this->indexExists('case_files', 'case_files_contract_expiration_index')) {
            Schema::table('case_files', fn (Blueprint $table) => $table->index('contract_expiration'));
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('case_files')) {
            return;
        }

        Schema::table('case_files', function (Blueprint $table) {
            if (Schema::hasColumn('case_files', 'contract_type')) {
                $table->dropIndex('case_files_contract_type_index');
            }
            if (Schema::hasColumn('case_files', 'contract_status')) {
                $table->dropIndex('case_files_contract_status_index');
            }
            if (Schema::hasColumn('case_files', 'contract_expiration')) {
                $table->dropIndex('case_files_contract_expiration_index');
            }
        });

        $cols = [];
        foreach (['contract_type', 'contract_date', 'contract_expiration', 'contract_status', 'contract_document_path', 'contract_notes'] as $col) {
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

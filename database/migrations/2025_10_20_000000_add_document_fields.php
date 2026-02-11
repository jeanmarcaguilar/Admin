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
        if (!Schema::hasTable('documents')) {
            return;
        }

        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'code')) {
                $table->string('code')->nullable()->after('id');
            }
            if (!Schema::hasColumn('documents', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('documents', 'type')) {
                $table->string('type')->nullable();
            }
            if (!Schema::hasColumn('documents', 'category')) {
                $table->string('category')->nullable();
            }
            if (!Schema::hasColumn('documents', 'size')) {
                $table->string('size')->nullable();
            }
            if (!Schema::hasColumn('documents', 'date')) {
                $table->date('date')->nullable();
            }
            if (!Schema::hasColumn('documents', 'status')) {
                $table->string('status', 50)->nullable()->default('Indexed');
            }
            if (!Schema::hasColumn('documents', 'is_shared')) {
                $table->boolean('is_shared')->default(false);
            }
            if (!Schema::hasColumn('documents', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('documents', 'file_path')) {
                $table->string('file_path')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('documents')) {
            return;
        }

        $cols = [];
        foreach (['code','name','type','category','size','date','status','is_shared','description','file_path'] as $col) {
            if (Schema::hasColumn('documents', $col)) {
                $cols[] = $col;
            }
        }

        if (!empty($cols)) {
            Schema::table('documents', function (Blueprint $table) use ($cols) {
                $table->dropColumn($cols);
            });
        }
    }
};

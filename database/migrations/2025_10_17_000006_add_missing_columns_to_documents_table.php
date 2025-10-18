<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add columns only if they are missing to avoid errors across environments
        if (!Schema::hasColumn('documents', 'code')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('code')->nullable()->after('id');
            });
            Schema::table('documents', function (Blueprint $table) {
                $table->index('code');
            });
        }

        if (!Schema::hasColumn('documents', 'name')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('name')->nullable()->after('code');
            });
        }

        if (!Schema::hasColumn('documents', 'type')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('type')->nullable()->after('name');
            });
        }

        if (!Schema::hasColumn('documents', 'category')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('category')->nullable()->after('type');
            });
        }

        if (!Schema::hasColumn('documents', 'size_label')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('size_label')->nullable()->after('category');
            });
        }

        if (!Schema::hasColumn('documents', 'uploaded_on')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->date('uploaded_on')->nullable()->after('size_label');
            });
        }

        if (!Schema::hasColumn('documents', 'status')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('status')->nullable()->after('uploaded_on');
            });
        }

        if (!Schema::hasColumn('documents', 'is_archived')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->boolean('is_archived')->default(false)->after('status');
            });
        }
    }

    public function down(): void
    {
        // Only drop columns if they exist
        if (Schema::hasColumn('documents', 'code')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropIndex(['code']);
            });
            Schema::table('documents', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }
        foreach (['name','type','category','size_label','uploaded_on','status','is_archived'] as $col) {
            if (Schema::hasColumn('documents', $col)) {
                Schema::table('documents', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }
    }
};



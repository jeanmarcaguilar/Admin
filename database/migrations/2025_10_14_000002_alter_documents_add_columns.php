<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('code')->nullable()->after('id');
            $table->string('name')->nullable()->after('code');
            $table->string('type')->nullable()->after('name');
            $table->string('category')->nullable()->after('type');
            $table->string('size_label')->nullable()->after('category');
            $table->date('uploaded_on')->nullable()->after('size_label');
            $table->string('status')->nullable()->after('uploaded_on');
            $table->boolean('is_archived')->default(false)->after('status');
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['code']);
            $table->dropColumn(['code','name','type','category','size_label','uploaded_on','status','is_archived']);
        });
    }
};

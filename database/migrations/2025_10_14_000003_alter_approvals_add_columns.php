<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->string('title')->nullable()->after('id');
            $table->text('description')->nullable()->after('title');
            $table->string('status')->default('pending')->after('description');
            $table->unsignedBigInteger('requester_id')->nullable()->after('status');
            $table->unsignedBigInteger('approver_id')->nullable()->after('requester_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['title','description','status','requester_id','approver_id']);
        });
    }
};

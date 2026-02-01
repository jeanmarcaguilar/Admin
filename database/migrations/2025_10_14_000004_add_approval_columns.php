<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->string('request_id')->nullable()->after('id');
            $table->string('title')->nullable()->after('request_id');
            $table->text('description')->nullable()->after('title');
            $table->string('status')->default('pending')->after('description');
            $table->string('type')->nullable()->after('status');
            $table->string('requested_by')->nullable()->after('type');
            $table->date('date')->nullable()->after('requested_by');
            $table->integer('lead_time')->nullable()->after('date');
            $table->unsignedBigInteger('requester_id')->nullable()->after('lead_time');
            $table->unsignedBigInteger('approver_id')->nullable()->after('requester_id');
            $table->string('approved_by')->nullable()->after('approver_id');
            $table->string('rejected_by')->nullable()->after('approved_by');
            $table->timestamp('approved_at')->nullable()->after('rejected_by');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
            
            $table->index('status');
            $table->index('request_id');
        });
    }

    public function down(): void
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['request_id']);
            $table->dropColumn([
                'request_id',
                'title', 
                'description',
                'status',
                'type',
                'requested_by',
                'date',
                'lead_time',
                'requester_id',
                'approver_id',
                'approved_by',
                'rejected_by',
                'approved_at',
                'rejected_at'
            ]);
        });
    }
};

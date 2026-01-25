<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('action_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('collection_accounts')->cascadeOnDelete();
            $table->enum('type', ['call','sms','email','visit','letter','note','dispute','escalation']);
            $table->text('content')->nullable();
            $table->string('outcome')->nullable();
            $table->date('next_action_on')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index(['account_id','type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('action_logs');
    }
};

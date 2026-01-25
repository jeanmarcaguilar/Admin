<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('collection_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debtor_id')->constrained('debtors')->cascadeOnDelete();
            $table->string('reference_no')->unique();
            $table->string('original_creditor')->nullable();
            $table->decimal('principal', 12, 2)->default(0);
            $table->decimal('interest', 12, 2)->default(0);
            $table->decimal('fees', 12, 2)->default(0);
            $table->decimal('current_balance', 12, 2)->default(0);
            $table->enum('status', ['new','in_contact','promise_to_pay','paying','broken_promise','unresponsive','dispute','legal'])->default('new');
            $table->date('opened_on')->nullable();
            $table->date('last_activity_on')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamps();
            $table->index(['debtor_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_accounts');
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promises_to_pay', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('collection_accounts')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('due_on');
            $table->enum('status', ['pending','kept','broken'])->default('pending');
            $table->timestamps();
            $table->index(['account_id','due_on','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promises_to_pay');
    }
};

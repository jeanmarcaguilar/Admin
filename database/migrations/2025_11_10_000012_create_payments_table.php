<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('collection_accounts')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('method')->nullable();
            $table->date('posted_on');
            $table->string('reference')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->index(['account_id','posted_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

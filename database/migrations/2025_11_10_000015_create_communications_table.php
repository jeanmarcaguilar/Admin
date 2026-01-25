<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('collection_accounts')->cascadeOnDelete();
            $table->enum('channel', ['call','sms','email','letter']);
            $table->enum('direction', ['outgoing','incoming'])->default('outgoing');
            $table->timestamp('when_at')->nullable();
            $table->string('summary')->nullable();
            $table->timestamps();
            $table->index(['account_id','channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};

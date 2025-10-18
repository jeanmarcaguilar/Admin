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
        Schema::create('compliance_tracking', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->enum('type', ['legal', 'financial', 'hr', 'safety', 'environmental', 'other']);
            $table->enum('status', ['active', 'pending', 'overdue', 'completed'])->default('active');
            $table->date('due_date');
            $table->text('description')->nullable();
            $table->string('responsible_person')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_tracking');
    }
};

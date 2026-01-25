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
        Schema::table('documents', function (Blueprint $table) {
            $table->string('code')->unique()->after('id');
            $table->string('name');
            $table->string('type');
            $table->string('category');
            $table->bigInteger('size')->nullable();
            $table->string('size_label')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_receipt')->default(false);
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2)->nullable();
            $table->date('receipt_date')->nullable();
            $table->string('status')->default('Indexed');
            $table->string('visibility')->default('private');
            $table->string('data_type')->nullable();
            $table->boolean('is_shared')->default(false);
            $table->date('uploaded_on')->nullable();
            
            // Add indexes
            $table->index(['type', 'category', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn([
                'code', 'name', 'type', 'category', 'size', 'size_label', 'file_path',
                'file_type', 'description', 'is_receipt', 'client_id', 'amount',
                'receipt_date', 'status', 'visibility', 'data_type', 'is_shared', 'uploaded_on'
            ]);
        });
    }
};

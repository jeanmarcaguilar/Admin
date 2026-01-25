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
            $table->string('size')->nullable();
            $table->date('date');
            $table->enum('status', ['Indexed', 'Pending', 'Archived'])->default('Indexed');
            $table->boolean('is_shared')->default(false);
            $table->text('description')->nullable();
            $table->string('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'code',
                'name',
                'type',
                'category',
                'size',
                'date',
                'status',
                'is_shared',
                'description',
                'file_path'
            ]);
        });
    }
};

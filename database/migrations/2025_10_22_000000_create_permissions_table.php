<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('type'); // user | group | department
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('group_id')->nullable(); // generic group/department identifier
                $table->string('role'); // admin | editor | viewer | custom
                $table->string('document_type'); // all | financial | hr | legal | other
                $table->json('permissions')->nullable(); // JSON array of granular permissions
                $table->string('status')->default('active');
                $table->timestamps();

                $table->index(['type', 'role']);
                $table->index(['document_type']);
                $table->index(['user_id']);
                $table->index(['group_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};

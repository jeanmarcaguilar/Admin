<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_proposal_status_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no')->unique();
            $table->string('status', 50);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_proposal_status_overrides');
    }
};

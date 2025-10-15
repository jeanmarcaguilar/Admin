<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('company');
            $table->string('visitor_type');
            $table->string('host');
            $table->string('host_department')->nullable();
            $table->date('check_in_date');
            $table->string('check_in_time');
            $table->date('check_out_date')->nullable();
            $table->string('check_out_time')->nullable();
            $table->string('purpose');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};

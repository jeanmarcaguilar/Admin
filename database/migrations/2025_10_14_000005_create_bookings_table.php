<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type'); // room|equipment
            $table->string('name');
            $table->date('date');
            $table->string('start_time');
            $table->string('end_time')->nullable();
            $table->date('return_date')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->string('status')->default('pending');
            $table->string('purpose')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

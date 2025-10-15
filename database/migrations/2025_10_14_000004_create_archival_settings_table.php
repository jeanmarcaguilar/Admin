<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archival_settings', function (Blueprint $table) {
            $table->id();
            $table->string('default_retention')->default('5');
            $table->boolean('auto_archive')->default(true);
            $table->text('notification_emails')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archival_settings');
    }
};

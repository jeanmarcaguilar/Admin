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
        Schema::create('hearings', function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD
            $table->string('title');
            $table->string('type');
            $table->string('case_number')->nullable();
            $table->date('hearing_date');
            $table->string('hearing_time')->nullable();
            $table->string('court_location')->nullable();
            $table->string('judge')->nullable();
            $table->string('status')->default('scheduled');
            $table->string('priority')->default('medium');
            $table->text('description')->nullable();
            $table->string('responsible_lawyer')->nullable();
            $table->string('client_name')->nullable();
            $table->string('case_type')->nullable();
            $table->boolean('reminder_sent')->default(false);
            $table->timestamps();
            
            $table->index('hearing_date');
            $table->index('status');
            $table->index('priority');
=======
            $table->timestamps();
>>>>>>> 3467a8cdf3aef1c3632815755eba1f09b252a719
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hearings');
    }
};

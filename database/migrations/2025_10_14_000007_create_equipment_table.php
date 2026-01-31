<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('audio'); // audio, video, computer, other
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->boolean('available')->default(true);
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        // Insert default equipment
        \DB::table('equipment')->insert([
            [
                'name' => 'Projector',
                'type' => 'video',
                'description' => 'HD projector with HDMI connection',
                'location' => 'Storage Room A',
                'available' => true,
                'quantity' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Laptop',
                'type' => 'computer',
                'description' => 'Windows laptop with office software',
                'location' => 'Storage Room B',
                'available' => true,
                'quantity' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Audio System',
                'type' => 'audio',
                'description' => 'Portable audio system with microphone',
                'location' => 'Storage Room A',
                'available' => true,
                'quantity' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};

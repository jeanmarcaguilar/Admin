<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('meeting'); // meeting, conference, training
            $table->integer('capacity')->default(10);
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->boolean('available')->default(true);
            $table->timestamps();
        });

        // Insert default rooms
        \DB::table('rooms')->insert([
            [
                'name' => 'Conference Room',
                'type' => 'conference',
                'capacity' => 20,
                'description' => 'Main conference room with projector and audio system',
                'location' => 'Floor 2',
                'available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Meeting Room',
                'type' => 'meeting',
                'capacity' => 8,
                'description' => 'Small meeting room for team discussions',
                'location' => 'Floor 1',
                'available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Training Room',
                'type' => 'training',
                'capacity' => 15,
                'description' => 'Training room with whiteboard and projector',
                'location' => 'Floor 3',
                'available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

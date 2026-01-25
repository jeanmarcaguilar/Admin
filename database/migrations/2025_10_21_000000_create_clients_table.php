<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Add some sample clients
        DB::table('clients')->insert([
            [
                'name' => 'Acme Corporation',
                'email' => 'info@acme.com',
                'phone' => '123-456-7890',
                'company' => 'Acme Corp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Globex Corporation',
                'email' => 'contact@globex.com',
                'phone' => '234-567-8901',
                'company' => 'Globex',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more sample clients as needed
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
};

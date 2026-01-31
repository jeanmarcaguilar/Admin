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
        // Create roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create user_role pivot table
        Schema::create('user_role', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->primary(['user_id', 'role_id']);
        });

        // Add department and last_login_at to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('department')->nullable();
            $table->timestamp('last_login_at')->nullable();
        });

        // Insert default roles
        DB::table('roles')->insert([
            ['name' => 'Administrator', 'description' => 'Full system access', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manager', 'description' => 'Department management access', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Employee', 'description' => 'Basic access level', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Guest', 'description' => 'Limited view-only access', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('roles');
        
        // Remove columns from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['department', 'last_login_at']);
        });
    }
};

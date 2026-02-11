<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create roles table (idempotent)
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // Create user_role pivot table (idempotent)
        if (!Schema::hasTable('user_role')) {
            Schema::create('user_role', function (Blueprint $table) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('role_id')->constrained()->onDelete('cascade');
                $table->timestamps();

                $table->primary(['user_id', 'role_id']);
            });
        }

        // Add department and last_login_at to users table (idempotent)
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'department')) {
                    $table->string('department')->nullable();
                }
                if (!Schema::hasColumn('users', 'last_login_at')) {
                    $table->timestamp('last_login_at')->nullable();
                }
            });
        }

        // Insert default roles (only if roles table exists and is empty)
        if (Schema::hasTable('roles')) {
            $count = DB::table('roles')->count();
            if ($count === 0) {
                DB::table('roles')->insert([
                    ['name' => 'Administrator', 'description' => 'Full system access', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'Manager', 'description' => 'Department management access', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'Employee', 'description' => 'Basic access level', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'Guest', 'description' => 'Limited view-only access', 'created_at' => now(), 'updated_at' => now()],
                ]);
            }
        }
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

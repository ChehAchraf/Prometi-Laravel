<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Backup current roles
        $roles = DB::table('roles')->get();
        
        // Drop the current roles table
        Schema::dropIfExists('roles');
        
        // Create a new roles table with the worker role included in the constraint
        DB::statement("
            CREATE TABLE roles (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                user_id BIGINT UNSIGNED NOT NULL,
                role VARCHAR(255) NOT NULL CHECK (role IN ('superadmin', 'hr_editor', 'pointage_editor', 'project_viewer', 'technical_director', 'worker')),
                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
            )
        ");
        
        // Restore the backed up roles
        foreach ($roles as $role) {
            DB::table('roles')->insert((array) $role);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Backup current roles
        $roles = DB::table('roles')->get();
        
        // Drop the current roles table
        Schema::dropIfExists('roles');
        
        // Create a new roles table without the worker role
        DB::statement("
            CREATE TABLE roles (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                user_id BIGINT UNSIGNED NOT NULL,
                role VARCHAR(255) NOT NULL CHECK (role IN ('superadmin', 'hr_editor', 'pointage_editor', 'project_viewer', 'technical_director')),
                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
            )
        ");
        
        // Restore the backed up roles (excluding worker roles)
        foreach ($roles as $role) {
            if ($role->role !== 'worker') {
                DB::table('roles')->insert((array) $role);
            }
        }
    }
}; 
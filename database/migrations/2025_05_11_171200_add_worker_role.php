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
        // Find the current roles table SQL structure
        $tables = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name='roles'");
        
        if (count($tables) > 0) {
            $currentSql = $tables[0]->sql;
            
            // If the table has a check constraint that doesn't include 'worker'
            if (str_contains($currentSql, "CHECK") && !str_contains($currentSql, "'worker'")) {
                // Create a new table with the updated constraint
                $newSql = preg_replace(
                    "/CHECK\s*\(\s*(`role`|role)\s+IN\s*\(([^)]+)\)\s*\)/i",
                    "CHECK (role IN ($2, 'worker'))",
                    $currentSql
                );
                
                // If replacement was successful, recreate the table with the new constraint
                if ($newSql !== $currentSql) {
                    // Save the current data
                    $roles = DB::table('roles')->get();
                    
                    // Drop the existing table
                    Schema::dropIfExists('roles');
                    
                    // Create the new table with the updated constraint
                    DB::statement($newSql);
                    
                    // Restore the data
                    foreach ($roles as $role) {
                        DB::table('roles')->insert((array) $role);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hard to reverse this change without knowing the original constraint
        // It's unlikely this would need to be reversed
    }
}; 
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
        // Check if the Worker status already exists
        $exists = DB::table('user_statuses')
            ->where('name', 'Worker')
            ->exists();
            
        if (!$exists) {
            // Add the Worker status
            DB::table('user_statuses')->insert([
                'name' => 'Worker',
                'color' => '#3498DB', // Blue color
                'icon' => 'fas fa-hard-hat',
                'description' => 'Regular worker/employee',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the Worker status
        DB::table('user_statuses')
            ->where('name', 'Worker')
            ->delete();
    }
}; 
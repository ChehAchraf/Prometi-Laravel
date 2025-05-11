<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkerStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
            
            $this->command->info('Worker status added successfully!');
        } else {
            $this->command->info('Worker status already exists.');
        }
    }
} 
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\UserStatus;
use Illuminate\Support\Facades\Hash;

class WorkerRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if we already have users with worker role
        $workerUserCount = Role::where('role', Role::WORKER)->count();
        
        if ($workerUserCount === 0) {
            // Create 3 test worker users
            for ($i = 1; $i <= 3; $i++) {
                $email = "worker{$i}@example.com";
                
                // Skip if email already exists
                if (User::where('email', $email)->exists()) {
                    $this->command->info("User with email {$email} already exists, skipping.");
                    continue;
                }
                
                $user = User::create([
                    'name' => "Worker User {$i}",
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'user_status_id' => UserStatus::ACTIVE,
                    'phone' => "123456789{$i}",
                    'address' => "Worker Address {$i}",
                ]);
                
                // Assign worker role
                Role::create([
                    'user_id' => $user->id,
                    'role' => Role::WORKER,
                ]);
                
                $this->command->info("Created worker user: {$email}");
            }
            
            $this->command->info('Worker users created successfully!');
        } else {
            $this->command->info("Worker users already exist ({$workerUserCount} found).");
        }
    }
} 
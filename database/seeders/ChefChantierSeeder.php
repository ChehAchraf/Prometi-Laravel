<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ChefChantierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createChefChantier(
            'Jean Dupont', 
            'chef1@example.com', 
            'password123',
            '0123456789'
        );
        
        $this->createChefChantier(
            'Marie Martin', 
            'chef2@example.com', 
            'password123',
            '0123456788'
        );
        
        $this->createChefChantier(
            'Pierre Dubois', 
            'chef3@example.com', 
            'password123',
            '0123456787'
        );
        
        $this->command->info('Chef de chantier users created successfully!');
    }
    
    /**
     * Create a chef de chantier user
     */
    private function createChefChantier(string $name, string $email, string $password, string $phone): void
    {
        try {
            // Check if user already exists
            $existingUser = User::where('email', $email)->first();
            
            if ($existingUser) {
                $this->command->info("User {$email} already exists. Updating role to pointage_editor.");
                
                // Update user's role to pointage_editor
                $role = Role::where('user_id', $existingUser->id)->first();
                if ($role) {
                    $role->role = Role::POINTAGE_EDITOR;
                    $role->save();
                } else {
                    // Create role if it doesn't exist
                    Role::create([
                        'user_id' => $existingUser->id,
                        'role' => Role::POINTAGE_EDITOR,
                    ]);
                }
                
                return;
            }
            
            // Create new user
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->phone = $phone;
            $user->user_status_id = UserStatus::ACTIVE; // Set status to Active
            $user->save();
            
            // Assign pointage_editor role
            Role::create([
                'user_id' => $user->id,
                'role' => Role::POINTAGE_EDITOR,
            ]);
            
            $this->command->info("Created chef de chantier: {$name} ({$email})");
            
        } catch (\Exception $e) {
            Log::error("Error creating chef de chantier {$name}: " . $e->getMessage());
            $this->command->error("Failed to create chef de chantier {$name}: " . $e->getMessage());
        }
    }
} 
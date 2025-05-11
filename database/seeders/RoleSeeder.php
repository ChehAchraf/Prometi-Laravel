<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create test users with roles if they don't exist
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'role' => 'superadmin'
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr@example.com',
                'password' => Hash::make('password'),
                'role' => 'hr_editor'
            ],
            [
                'name' => 'Chef de Chantier',
                'email' => 'chef@example.com',
                'password' => Hash::make('password'),
                'role' => 'pointage_editor'
            ],
            [
                'name' => 'Project Manager',
                'email' => 'project@example.com',
                'password' => Hash::make('password'),
                'role' => 'project_viewer'
            ],
            [
                'name' => 'Technical Director',
                'email' => 'director@example.com',
                'password' => Hash::make('password'),
                'role' => 'technical_director'
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            
            // Create or update user
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            // Assign role
            Role::updateOrCreate(
                ['user_id' => $user->id],
                ['role' => $role]
            );
        }
    }
} 
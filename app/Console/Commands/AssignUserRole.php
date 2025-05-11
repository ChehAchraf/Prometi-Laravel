<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class AssignUserRole extends Command
{
    protected $signature = 'users:assign-role {email} {role}';
    protected $description = 'Assign a role to a user by email';

    public function handle()
    {
        $email = $this->argument('email');
        $roleSlug = $this->argument('role');
        
        // Find the user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }
        
        // Find the role
        $role = Role::where('slug', $roleSlug)->first();
        
        if (!$role) {
            $this->error("Role with slug {$roleSlug} not found.");
            $this->info("Available roles:");
            
            foreach (Role::all() as $availableRole) {
                $this->info("- {$availableRole->slug} ({$availableRole->name})");
            }
            
            return 1;
        }
        
        // Check if the user already has this role
        if ($user->hasRole($roleSlug)) {
            $this->info("User {$email} already has the role {$role->name}.");
            return 0;
        }
        
        // Assign role
        $user->roles()->attach($role->id);
        
        $this->info("Successfully assigned the role {$role->name} to user {$email}.");
        return 0;
    }
} 
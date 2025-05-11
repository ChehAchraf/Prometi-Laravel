<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RoleSeeder::class,
        ]);
        
        // Create some sample projects
        $projects = [
            [
                'name' => 'Office Renovation',
                'description' => 'Renovation of the main office building',
                'location' => 'Headquarters',
                'start_date' => now(),
                'end_date' => now()->addMonths(3),
                'status' => 'active',
            ],
            [
                'name' => 'New Residential Complex',
                'description' => 'Construction of a 50-unit residential complex',
                'location' => 'Downtown',
                'start_date' => now()->addWeek(),
                'end_date' => now()->addYear(),
                'status' => 'active',
            ],
            [
                'name' => 'Road Repair Project',
                'description' => 'Repair and maintenance of city roads',
                'location' => 'Various locations',
                'start_date' => now()->subWeek(),
                'end_date' => now()->addMonths(6),
                'status' => 'active',
            ],
        ];
        
        foreach ($projects as $projectData) {
            Project::create($projectData);
        }
        
        // Assign projects to users
        $chefUser = User::whereHas('role', function($query) {
            $query->where('role', 'pointage_editor');
        })->first();
        
        if ($chefUser) {
            $chefUser->projects()->attach([1, 2]); // Assign first two projects to the chef
        }
        
        // Create sample time entries
        $timeEntries = [
            [
                'user_id' => 2, // HR Manager
                'project_id' => 1,
                'date' => now()->subDays(1),
                'check_in' => '08:00',
                'check_out' => '16:00',
                'status' => 'present',
                'notes' => 'Regular work day',
                'total_hours' => 8,
                'overtime_hours' => 0,
            ],
            [
                'user_id' => 3, // Chef de Chantier
                'project_id' => 1,
                'date' => now()->subDays(1),
                'check_in' => '07:30',
                'check_out' => '17:30',
                'status' => 'present',
                'notes' => 'Extended work day to complete milestone',
                'total_hours' => 10,
                'overtime_hours' => 2,
            ],
            [
                'user_id' => 4, // Project Manager
                'project_id' => 2,
                'date' => now()->subDays(2),
                'check_in' => '09:00',
                'check_out' => '15:00',
                'status' => 'early_leave',
                'notes' => 'Left early for doctor appointment',
                'total_hours' => 6,
                'overtime_hours' => 0,
            ],
        ];
        
        foreach ($timeEntries as $entryData) {
            // Only create entries if the users and projects exist
            if (User::find($entryData['user_id']) && Project::find($entryData['project_id'])) {
                TimeEntry::create($entryData);
            }
        }
    }
}

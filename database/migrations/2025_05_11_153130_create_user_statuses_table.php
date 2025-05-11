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
        Schema::create('user_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#6B7280'); // Default gray color
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default statuses
        $statuses = [
            ['name' => 'Active', 'color' => '#10B981', 'icon' => 'fas fa-check-circle', 'description' => 'User is actively working'],
            ['name' => 'Leave', 'color' => '#F59E0B', 'icon' => 'fas fa-umbrella-beach', 'description' => 'User is on leave/vacation'],
            ['name' => 'Mission', 'color' => '#3B82F6', 'icon' => 'fas fa-briefcase', 'description' => 'User is on a business trip/mission'],
            ['name' => 'Absent', 'color' => '#EF4444', 'icon' => 'fas fa-times-circle', 'description' => 'User is absent'],
            ['name' => 'Remote', 'color' => '#8B5CF6', 'icon' => 'fas fa-home', 'description' => 'User is working remotely'],
            ['name' => 'Training', 'color' => '#EC4899', 'icon' => 'fas fa-graduation-cap', 'description' => 'User is attending training'],
        ];
        
        DB::table('user_statuses')->insert($statuses);

        // Add user_status_id column to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('user_status_id')->nullable()->after('role_id')
                ->constrained()->nullOnDelete();
        });

        // Set all existing users to 'Active' status
        DB::statement('UPDATE users SET user_status_id = 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_status_id');
        });
        
        Schema::dropIfExists('user_statuses');
    }
};

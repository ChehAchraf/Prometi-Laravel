<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', [
                'superadmin',           // SuperAdmin: Full access to everything
                'hr_editor',            // HR Editor: Can create/update/delete users and projects
                'pointage_editor',      // Pointage Editor/Chef de chantier: Can input working hours
                'project_viewer',       // Project Viewer: Can view project data where assigned
                'technical_director'    // Technical Director: Can view all projects and reports
            ]);
            $table->timestamps();   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends Model
{
    protected $fillable = [
        'user_id',
        'role',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Role constants for easier reference - must match exactly with enum values in migration
    public const SUPERADMIN = 'superadmin';
    public const HR_EDITOR = 'hr_editor';
    public const POINTAGE_EDITOR = 'pointage_editor';
    public const PROJECT_VIEWER = 'project_viewer';
    public const TECHNICAL_DIRECTOR = 'technical_director';
    public const WORKER = 'worker';
    
    // All available roles
    public static function availableRoles(): array
    {
        return [
            self::SUPERADMIN => 'Super Admin',
            self::HR_EDITOR => 'HR Editor',
            self::POINTAGE_EDITOR => 'Pointage Editor/Chef de Chantier',
            self::PROJECT_VIEWER => 'Project Viewer',
            self::TECHNICAL_DIRECTOR => 'Technical Director',
            self::WORKER => 'Worker',
        ];
    }
    
    /**
     * Get the formatted display name for the role
     */
    public function getDisplayName(): string
    {
        $roles = self::availableRoles();
        return $roles[$this->role] ?? ucfirst(str_replace('_', ' ', $this->role));
    }
} 
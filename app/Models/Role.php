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
    public const MAGASINIER = 'magasinier';
    public const CHEF_DE_PROJET = 'chef_de_projet';
    public const WORKER = 'worker';
    
    // All available roles
    public static function availableRoles(): array
    {
        return [
            self::SUPERADMIN => 'Super Admin',
            self::HR_EDITOR => 'HR Editor',
            self::POINTAGE_EDITOR => 'Pointage Editor',
            self::MAGASINIER => 'Magasinier',
            self::CHEF_DE_PROJET => 'Chef de Projet',
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
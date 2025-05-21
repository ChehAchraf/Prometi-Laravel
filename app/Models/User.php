<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'address',
        'user_status_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function role(): HasOne
    {
        return $this->hasOne(Role::class);
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole($roleName): bool
    {
        return $this->role && $this->role->role === $roleName;
    }

    /**
     * Check if the user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->role && in_array($this->role->role, $roles);
    }

    /**
     * Check if user is an admin (superadmin)
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    /**
     * Check if user can manage projects (create, edit, delete)
     */
    public function canManageProjects(): bool
    {
        return $this->hasAnyRole(['superadmin', 'hr_editor']);
    }

    /**
     * Check if user can manage time entries
     */
    public function canManageTimeEntries(): bool
    {
        return $this->hasAnyRole(['superadmin', 'hr_editor', 'pointage_editor', 'magasinier']);
    }

    /**
     * Check if user can view all projects
     */
    public function canViewAllProjects(): bool
    {
        return $this->hasAnyRole(['superadmin', 'hr_editor', 'chef_de_projet']);
    }

    /**
     * Check if the user is a worker (by role)
     */
    public function isWorkerRole(): bool
    {
        return $this->hasRole(Role::WORKER);
    }
    
    /**
     * Check if user can manage users (create, edit, delete)
     */
    public function canManageUsers(): bool
    {
        return $this->hasAnyRole(['superadmin', 'hr_editor']);
    }
    
    /**
     * Check if user can view reports
     */
    public function canViewReports(): bool
    {
        return $this->hasAnyRole(['superadmin', 'hr_editor', 'chef_de_projet']);
    }
    
    /**
     * Check if user can assign users to projects
     */
    public function canAssignUsersToProjects(): bool
    {
        return $this->hasAnyRole(['superadmin', 'hr_editor']);
    }

    /**
     * The projects that the user is assigned to.
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user');
    }

    /**
     * Get the user's status.
     */
    public function status()
    {
        return $this->belongsTo(UserStatus::class, 'user_status_id');
    }

    /**
     * Check if the user is active (not on leave, mission, etc.)
     */
    public function isActive(): bool
    {
        return $this->user_status_id === UserStatus::ACTIVE;
    }

    /**
     * Check if the user is on leave
     */
    public function isOnLeave(): bool
    {
        return $this->user_status_id === UserStatus::LEAVE;
    }

    /**
     * Check if the user is a worker
     */
    public function isWorker(): bool
    {
        return $this->user_status_id === UserStatus::WORKER;
    }

    /**
     * Update the user's status to a new value
     */
    public function updateStatus(int $statusId): bool
    {
        $this->user_status_id = $statusId;
        return $this->save();
    }
}

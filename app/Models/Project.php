<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'location',
        'start_date',
        'end_date',
        'status',
        'manager_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $with = ['manager']; // Always load the manager relationship

    /**
     * Get the manager of the project.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the employees assigned to the project.
     */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
                    ->withTimestamps();
    }

    /**
     * Get the time entries for the project.
     */
    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    /**
     * Get the progress attribute.
     */
    public function getProgressAttribute(): int
    {
        // You can implement your own logic to calculate progress
        // For now, we'll return a random value between 0 and 100
        return rand(0, 100);
    }

    /**
     * Get the status attribute.
     */
    public function getStatusAttribute($value): string
    {
        return $value ?? 'in_progress';
    }

    /**
     * Get the manager name attribute.
     */
    public function getManagerNameAttribute(): string
    {
        return $this->manager ? $this->manager->name : 'Non assigné';
    }

    /**
     * Get the manager avatar URL attribute.
     */
    public function getManagerAvatarUrlAttribute(): string
    {
        if ($this->manager) {
            return "https://ui-avatars.com/api/?name=" . urlencode($this->manager->name) . "&background=random";
        }
        return "https://ui-avatars.com/api/?name=Non+Assigne&background=random";
    }
}

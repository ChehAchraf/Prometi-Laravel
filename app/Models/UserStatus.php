<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'color',
        'icon',
        'description',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the users that have this status.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Status constants for easier reference
     */
    public const ACTIVE = 1;
    public const LEAVE = 2;
    public const MISSION = 3;
    public const ABSENT = 4;
    public const REMOTE = 5;
    public const TRAINING = 6;
    public const WORKER = 7;
}

<?php

namespace App\Policies;

use App\Models\Overtime;
use App\Models\TimeEntry;
use App\Models\User;

class OvertimePolicy
{
    public function view(User $user, Overtime $overtime): bool
    {
        return $user->id === $overtime->user_id || $user->role === 'admin' || $user->role === 'manager';
    }

    public function create(User $user, TimeEntry $timeEntry): bool
    {
        return $user->id === $timeEntry->user_id && $timeEntry->hasOvertime();
    }

    public function approve(User $user, Overtime $overtime): bool
    {
        return ($user->role === 'admin' || $user->role === 'manager') && $overtime->isPending();
    }
} 
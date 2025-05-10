<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Overtime extends Model
{
    protected $fillable = [
        'time_entry_id',
        'user_id',
        'project_id',
        'date',
        'hours',
        'reason',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'date' => 'date',
        'hours' => 'decimal:2',
        'approved_at' => 'datetime'
    ];

    public function timeEntry(): BelongsTo
    {
        return $this->belongsTo(TimeEntry::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function approve(User $approver, ?string $reason = null): bool
    {
        return $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'reason' => $reason
        ]);
    }

    public function reject(User $approver, string $reason): bool
    {
        return $this->update([
            'status' => 'rejected',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'rejection_reason' => $reason
        ]);
    }
} 
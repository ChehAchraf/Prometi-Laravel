<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class TimeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'date',
        'check_in',
        'check_out',
        'total_hours',
        'overtime_hours',
        'status',
        'notes',
        'jour_type'
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'total_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($timeEntry) {
            if ($timeEntry->check_in && $timeEntry->check_out) {
                $timeEntry->calculateHours();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function overtime(): HasOne
    {
        return $this->hasOne(Overtime::class);
    }

    protected function calculateHours(): void
    {
        $checkIn = Carbon::parse($this->check_in);
        $checkOut = Carbon::parse($this->check_out);

        // Calculate total hours worked
        $totalHours = $checkOut->floatDiffInHours($checkIn);
        $this->total_hours = round($totalHours, 2);

        // Calculate overtime (hours worked beyond 8 hours)
        $this->overtime_hours = max(0, round($totalHours - 8, 2));
    }

    public function calculateWorkHours(): float
    {
        if (!$this->check_in || !$this->check_out) {
            return 0;
        }

        return $this->check_out->diffInHours($this->check_in, true);
    }

    public function calculateOvertime(): float
    {
        $workHours = $this->calculateWorkHours();
        return max(0, $workHours - 8);
    }

    public function hasOvertime(): bool
    {
        return $this->calculateOvertime() > 0;
    }

    public function createOvertimeRequest(): ?Overtime
    {
        if (!$this->hasOvertime()) {
            return null;
        }

        return $this->overtime()->create([
            'user_id' => $this->user_id,
            'project_id' => $this->project_id,
            'date' => $this->date,
            'hours' => $this->calculateOvertime(),
            'status' => 'pending'
        ]);
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'present' => 'success',
            'absent' => 'danger',
            'late' => 'warning',
            default => 'secondary'
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            default => ucfirst($this->status)
        };
    }
}

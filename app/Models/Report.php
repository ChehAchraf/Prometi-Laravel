<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'start_date',
        'end_date',
        'attendance_data',
        'summary',
        'report_type',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'attendance_data' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}

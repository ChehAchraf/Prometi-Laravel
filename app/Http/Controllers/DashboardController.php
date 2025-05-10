<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\TimeEntry;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalUsers' => User::count(),
            'activeProjects' => Project::where('status', 'active')->count(),
            'presentToday' => TimeEntry::whereDate('date', today())
                ->where('status', 'present')
                ->count(),
            'absentToday' => TimeEntry::whereDate('date', today())
                ->where('status', 'absent')
                ->count(),
            'recentActivities' => TimeEntry::with(['user', 'project'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($entry) {
                    return (object)[
                        'icon' => $entry->status === 'present' ? 'fa-user-check' : 'fa-user-times',
                        'description' => "{$entry->user->name} a pointé {$entry->status} sur {$entry->project->name}",
                        'time' => $entry->created_at->diffForHumans(),
                    ];
                }),
            'todayAttendance' => TimeEntry::with(['user', 'project'])
                ->whereDate('date', today())
                ->get(),
        ];

        return view('dashboard.index', $data);
    }
}

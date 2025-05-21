<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use App\Models\Project;
use App\Models\Overtime;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Only users who can manage time entries can create, edit, delete
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->canManageTimeEntries()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        })->except(['index', 'show']);
    }

    /**
     * Display a listing of the time entries.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role ? $user->role->role : null;

        // Get accessible projects for filtering
        if ($user->canViewAllProjects()) {
            $projects = Project::orderBy('name')->get();
        } elseif ($userRole === Role::POINTAGE_EDITOR) {
            $projects = Project::whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })->orderBy('name')->get();
        } else {
            $projects = Project::whereHas('timeEntries', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->orderBy('name')->get();
        }

        // Base query
        $query = TimeEntry::with(['user', 'project']);

        // Apply project filter if provided
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Apply user role based filters
        if ($user->canViewAllProjects()) {
            // No additional filters needed - can see all entries
        } elseif ($userRole === Role::POINTAGE_EDITOR) {
            // Get projects where this user is assigned
            $projectIds = $projects->pluck('id')->toArray();
            $query->whereIn('project_id', $projectIds);
        } else {
            // Regular users can only see their own entries
            $query->where('user_id', $user->id);
        }

        $timeEntries = $query->latest()->paginate(15);

        return view('time-entries.index', compact('timeEntries', 'projects'));
    }

    /**
     * Show the form for creating a new time entry.
     */
    public function create()
    {
        $user = Auth::user();
        $userRole = $user->role ? $user->role->role : null;

        // Get projects the user has access to
        if ($user->canManageProjects()) {
            $projects = Project::all();
            $users = User::with('role')->get();
        } else {
            // Pointage editors and magasiniers can only add time entries for their projects
            $projects = Project::whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })->get();

            // Get users who work in chef's projects
            $projectIds = $projects->pluck('id')->toArray();
            $users = User::with('role')
                ->whereHas('projects', function ($query) use ($projectIds) {
                    $query->whereIn('projects.id', $projectIds);
                })
                ->orWhere('id', $user->id) // Always include the current user
                ->get();
        }

        $statuses = ['present', 'absent', 'late', 'early_leave'];

        // Log the user retrieval for debugging
        \Illuminate\Support\Facades\Log::info('TimeEntryController@create users', [
            'count' => $users->count(),
            'userIds' => $users->pluck('id')->toArray(),
            'userNames' => $users->pluck('name')->toArray()
        ]);

        return view('time-entries.create', compact('projects', 'users', 'statuses'));
    }

    /**
     * Store a newly created time entry in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|array',
            'user_id.*' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,absent,late,early_leave',
            'notes' => 'nullable|string',
            'total_hours' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'jour_type' => 'required'
        ]);

        // If user is not admin or HR, verify they have access to this project
        $user = Auth::user();
        $userRole = $user->role ? $user->role->role : null;

        if (!in_array($userRole, [Role::SUPERADMIN, Role::HR_EDITOR])) {
            $hasAccess = Project::where('id', $validated['project_id'])
                ->whereHas('users', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                ->exists();

            if (!$hasAccess) {
                return redirect()->back()->with('error', 'You do not have permission to add time entries for this project.');
            }
        }

        // Calculate hours if check-in and check-out are provided
        if (!empty($validated['check_in']) && !empty($validated['check_out'])) {
            $checkIn = new \DateTime($validated['check_in']);
            $checkOut = new \DateTime($validated['check_out']);
            $interval = $checkIn->diff($checkOut);
            $hours = $interval->h + ($interval->i / 60);

            $validated['total_hours'] = $hours;
        }

        foreach ($validated["user_id"] as $userId) {
            $data = $validated;
            $data['user_id'] = $userId;

            TimeEntry::create($data);

        }

        return redirect()->route('time-entries.index')
            ->with('success', 'Time entry created successfully.');
    }

    /**
     * Display the specified time entry.
     */
    public function show(TimeEntry $timeEntry)
    {
        $user = Auth::user();
        $userRole = $user->role ? $user->role->role : null;

        // Check if user has permission to view this time entry
        if (!$user->canViewAllProjects()) {
            // Chef de chantier can view entries for their projects
            if ($userRole === Role::POINTAGE_EDITOR) {
                $hasAccess = Project::where('id', $timeEntry->project_id)
                    ->whereHas('users', function ($query) use ($user) {
                        $query->where('users.id', $user->id);
                    })
                    ->exists();

                if (!$hasAccess) {
                    abort(403, 'You do not have permission to view this time entry.');
                }
            }
            // Regular users can only view their own entries
            elseif ($timeEntry->user_id !== $user->id) {
                abort(403, 'You do not have permission to view this time entry.');
            }
        }

        return view('time-entries.show', compact('timeEntry'));
    }

    /**
     * Show the form for editing the specified time entry.
     */
    public function edit(TimeEntry $timeEntry)
    {
        $user = Auth::user();
        $userRole = $user->role ? $user->role->role : null;

        // Check if user has permission to edit this time entry
        if (!in_array($userRole, [Role::SUPERADMIN, Role::HR_EDITOR])) {
            // Pointage editors and magasiniers can edit entries for their projects
            if ($user->hasAnyRole([Role::POINTAGE_EDITOR, Role::MAGASINIER])) {
                $hasAccess = Project::where('id', $timeEntry->project_id)
                    ->whereHas('users', function ($query) use ($user) {
                        $query->where('users.id', $user->id);
                    })
                    ->exists();

                if (!$hasAccess) {
                    abort(403, 'You do not have permission to edit this time entry.');
                }
            } else {
                abort(403, 'You do not have permission to edit time entries.');
            }
        }

        // Get projects and users based on permissions
        if ($user->canManageProjects()) {
            $projects = Project::all();
            $users = User::all();
        } else {
            $projects = Project::whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })->get();

            $projectIds = $projects->pluck('id')->toArray();
            $users = User::whereHas('projects', function ($query) use ($projectIds) {
                $query->whereIn('projects.id', $projectIds);
            })->get();
        }

        $statuses = ['present', 'absent', 'late', 'early_leave'];

        return view('time-entries.edit', compact('timeEntry', 'projects', 'users', 'statuses'));
    }

    /**
     * Update the specified time entry in storage.
     */
    public function update(Request $request, TimeEntry $timeEntry)
    {
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,absent,late,early_leave',
            'notes' => 'nullable|string',
            'total_hours' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'jour_type' => 'required'
        ]);

        // Check user permission for this project
        $user = Auth::user();
        $userRole = $user->role ? $user->role->role : null;

        if (!in_array($userRole, [Role::SUPERADMIN, Role::HR_EDITOR])) {
            $hasAccess = Project::where('id', $validated['project_id'])
                ->whereHas('users', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                ->exists();

            if (!$hasAccess) {
                return redirect()->back()->with('error', 'You do not have permission to update time entries for this project.');
            }
        }

        // Calculate hours if check-in and check-out are provided
        if (!empty($validated['check_in']) && !empty($validated['check_out'])) {
            $checkIn = new \DateTime($validated['check_in']);
            $checkOut = new \DateTime($validated['check_out']);
            $interval = $checkIn->diff($checkOut);
            $hours = $interval->h + ($interval->i / 60);

            $validated['total_hours'] = $hours;
        }

        $timeEntry->update($validated);

        return redirect()->route('time-entries.index')
            ->with('success', 'Time entry updated successfully.');
    }

    /**
     * Remove the specified time entry from storage.
     */
    public function destroy(TimeEntry $timeEntry)
    {
        $user = Auth::user();
        $userRole = $user->role ? $user->role->role : null;

        // Only superadmin can delete time entries
        if (!$user->isAdmin()) {
            abort(403, 'You do not have permission to delete time entries.');
        }

        $timeEntry->delete();

        return redirect()->route('time-entries.index')
            ->with('success', 'Time entry deleted successfully.');
    }
}

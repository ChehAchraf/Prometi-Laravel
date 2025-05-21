<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function __construct()
    {
        // Only authenticated users can access these methods
        $this->middleware('auth');
        
        // Only superadmin and hr_editor can create, edit, and delete projects
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->canManageProjects()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }
    
    /**
     * Display a listing of the projects.
     */
    public function index()
    {
        $user = Auth::user();
        $userRole = $user->role ? $user->role->role : null;
        
        // Superadmins, HR editors, and chef_de_projet can see all projects
        if ($user->canViewAllProjects()) {
            $projects = Project::latest()->paginate(10);
        } else {
            // Other users can only see projects they're assigned to
            $projects = Project::whereHas('users', function($query) use ($user) {
                $query->where('users.id', $user->id);
            })->latest()->paginate(10);
        }
        
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        // Get chefs de chantier for assignment
        $chefs = User::whereHas('role', function($query) {
            $query->where('role', Role::POINTAGE_EDITOR);
        })->get();
        
        return view('projects.create', compact('chefs'));
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,completed,on_hold',
            'chef_id' => 'nullable|exists:users,id',
            'chef_ids' => 'nullable|array',
            'chef_ids.*' => 'nullable|exists:users,id',
        ]);

        Log::info('Project creation with chef ID: ' . $request->input('chef_id'));
        Log::info('Project creation with additional chefs: ' . json_encode($request->input('chef_ids')));

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $validated['status'],
            'chef_id' => $validated['chef_id'] ?? null,
        ]);

        // Assign additional chefs to the project
        if ($request->has('chef_ids') && is_array($request->chef_ids)) {
            $project->users()->attach($request->chef_ids);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        // Check if user can access this project
        $user = Auth::user();
        $userRole = $user->role ? $user->role->role : null;
        
        // Check if user has permission to view this project
        $canViewAll = $user->canViewAllProjects();
        
        if (!$canViewAll && !$project->users->contains($user->id)) {
            abort(403, 'You do not have permission to view this project.');
        }
        
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        // Get pointage editors and magasiniers for assignment
        $chefs = User::whereHas('role', function($query) {
            $query->whereIn('role', [Role::POINTAGE_EDITOR, Role::MAGASINIER]);
        })->get();
        
        // Get currently assigned chefs
        $assignedChefs = $project->users()->pluck('users.id')->toArray();
        
        return view('projects.edit', compact('project', 'chefs', 'assignedChefs'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,completed,on_hold',
            'chef_id' => 'nullable|exists:users,id',
            'chef_ids' => 'nullable|array',
            'chef_ids.*' => 'nullable|exists:users,id',
        ]);

        Log::info('Project update with chef ID: ' . $request->input('chef_id'));
        Log::info('Project update with additional chefs: ' . json_encode($request->input('chef_ids')));

        $project->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $validated['status'],
            'chef_id' => $validated['chef_id'] ?? null,
        ]);

        // Sync additional chefs assigned to the project
        if ($request->has('chef_ids') && is_array($request->chef_ids)) {
            $project->users()->sync($request->chef_ids);
        } else {
            $project->users()->detach();
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}

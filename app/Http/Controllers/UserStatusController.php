<?php

namespace App\Http\Controllers;

use App\Models\UserStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserStatusController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:superadmin,hr_editor');
    }
    
    /**
     * Display a listing of the statuses.
     */
    public function index(): View
    {
        $statuses = UserStatus::orderBy('name')->get();
        return view('user_statuses.index', compact('statuses'));
    }

    /**
     * Show the form for creating a new status.
     */
    public function create(): View
    {
        return view('user_statuses.create');
    }

    /**
     * Store a newly created status in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:user_statuses',
            'color' => 'required|string|max:7',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        // Default to active if not specified
        $validated['is_active'] = $request->has('is_active');
        
        UserStatus::create($validated);
        
        return redirect()->route('user-statuses.index')
            ->with('success', 'Status created successfully.');
    }

    /**
     * Show the form for editing the specified status.
     */
    public function edit(UserStatus $userStatus): View
    {
        return view('user_statuses.edit', compact('userStatus'));
    }

    /**
     * Update the specified status in storage.
     */
    public function update(Request $request, UserStatus $userStatus): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:user_statuses,name,' . $userStatus->id,
            'color' => 'required|string|max:7',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        // Default to active if not specified
        $validated['is_active'] = $request->has('is_active');
        
        $userStatus->update($validated);
        
        return redirect()->route('user-statuses.index')
            ->with('success', 'Status updated successfully.');
    }

    /**
     * Remove the specified status from storage.
     */
    public function destroy(UserStatus $userStatus): RedirectResponse
    {
        // Check if status is in use
        if ($userStatus->users()->count() > 0) {
            return redirect()->route('user-statuses.index')
                ->with('error', 'Cannot delete status because it is assigned to users.');
        }
        
        $userStatus->delete();
        
        return redirect()->route('user-statuses.index')
            ->with('success', 'Status deleted successfully.');
    }
    
    /**
     * Toggle the active status of a user status.
     */
    public function toggleActive(UserStatus $userStatus): RedirectResponse
    {
        $userStatus->is_active = !$userStatus->is_active;
        $userStatus->save();
        
        $status = $userStatus->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('user-statuses.index')
            ->with('success', "Status {$status} successfully.");
    }
}

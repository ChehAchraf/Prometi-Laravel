<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UserStatus;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $users = User::with(['role', 'status'])->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $roles = Role::availableRoles();
        $statuses = UserStatus::where('is_active', true)->pluck('name', 'id');
        
        return view('users.create', compact('roles', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:superadmin,hr_editor,pointage_editor,project_viewer,technical_director,worker',
            'user_status_id' => 'required|exists:user_statuses,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // Begin transaction to ensure user and role are created together
        DB::beginTransaction();
        
        try {
            // Extract role from validated data
            $roleName = $validated['role'];
            unset($validated['role']);
            
            // Create user with hashed password
            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);
            
            // Assign role to user
            $this->assignRoleToUser($user, $roleName);
            
            DB::commit();
            
            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User creation error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the user.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        $user->load('role');
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $user->load('role');
        
        // Get available roles for dropdown
        $roles = Role::availableRoles();
        
        // Get available statuses for dropdown
        $statuses = UserStatus::where('is_active', true)->pluck('name', 'id');
        
        return view('users.edit', compact('user', 'roles', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:superadmin,hr_editor,pointage_editor,project_viewer,technical_director,worker',
            'user_status_id' => 'required|exists:user_statuses,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // Handle password update if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        // Begin transaction to ensure user and role are updated together
        DB::beginTransaction();
        
        try {
            // Extract role from validated data
            $roleName = $validated['role'];
            unset($validated['role']);
            
            // Update user
            $user->update($validated);
            
            // Update or create role
            $this->assignRoleToUser($user, $roleName);
            
            DB::commit();
            
            return redirect()->route('users.index')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User update error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the user.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Delete user (role will be automatically deleted due to foreign key constraint)
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Search for users
     */
    public function search(Request $request)
    {
        try {
            $search = $request->get('search');
            Log::info('User search request received', [
                'search' => $search,
                'headers' => $request->headers->all(),
                'ajax' => $request->ajax()
            ]);
            
            $users = User::when($search, function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->with('role')
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get();
            
            Log::info('User search results', [
                'count' => $users->count(),
                'users' => $users->pluck('name', 'id')->toArray()
            ]);

            return response()->json([
                'success' => true,
                'data' => $users->toArray(),
                'next_page_url' => null
            ]);
        } catch (\Exception $e) {
            Log::error('User search error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
                'next_page_url' => null
            ], 500);
        }
    }
    
    /**
     * Helper method to assign a role to user
     */
    private function assignRoleToUser(User $user, string $roleName): void
    {
        try {
            // Get existing role or create new one
            $role = Role::where('user_id', $user->id)->first();
            
            if ($role) {
                // Update existing role
                $role->role = $roleName;
                $role->save();
            } else {
                // Create new role
                $role = new Role();
                $role->user_id = $user->id;
                $role->role = $roleName;
                $role->save();
            }
        } catch (\Exception $e) {
            // Log the specific error
            Log::error('Role assignment error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Search for chef de chantier (pointage editors)
     */
    public function searchChefChantier(Request $request)
    {
        try {
            $search = $request->get('search', '');
            Log::info('Searching for chef de chantier with term: ' . $search);
            Log::info('Request headers: ' . json_encode($request->headers->all()));
            
            // First, check how many pointage_editor users exist in the system
            $totalChefs = User::whereHas('role', function($query) {
                $query->where('role', Role::POINTAGE_EDITOR);
            })->count();
            
            Log::info('Total chef de chantier users in system: ' . $totalChefs);
            
            $query = User::whereHas('role', function($query) {
                $query->where('role', Role::POINTAGE_EDITOR);
            });
            
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            $chefs = $query->select('id', 'name', 'email', 'phone')
                ->limit(10)
                ->get();
            
            Log::info('Found ' . $chefs->count() . ' chefs de chantier matching search');
            
            // Add each found chef to the log for debugging
            foreach ($chefs as $chef) {
                Log::info("Chef found: ID={$chef->id}, Name={$chef->name}, Email={$chef->email}");
            }
            
            return response()->json([
                'success' => true,
                'data' => $chefs->toArray(),
                'total_chefs' => $totalChefs
            ]);
        } catch (\Exception $e) {
            Log::error('Chef de chantier search error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to search for chef de chantier: ' . $e->getMessage()
            ], 500);
        }
    }
} 
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\UserStatusController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes
Auth::routes();

// Redirect root to login if not authenticated, otherwise to dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User search route (accessible to all authenticated users)
    Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
    
    // Chef de chantier search route
    Route::get('/search/chef-chantier', [UserController::class, 'searchChefChantier'])->name('search.chef-chantier');
    
    // TEST routes
    Route::get('/test/chef-search', function() {
        return view('test-chef-search');
    })->name('test.chef-search');
    
    Route::get('/test/user-search', function() {
        return view('test-user-search');
    })->name('test.user-search');
    
    // Routes for project management (admin and HR)
    Route::middleware(['role:superadmin,hr_editor'])->group(function () {
        Route::resource('projects', ProjectController::class)->except(['index', 'show']);
        Route::resource('users', UserController::class);
        
        // User Status management routes
        Route::resource('user-statuses', UserStatusController::class)->except(['show']);
        Route::patch('/user-statuses/{userStatus}/toggle', [UserStatusController::class, 'toggleActive'])
            ->name('user-statuses.toggle');
    });
    
    // Routes for pointage editors (chef de chantier)
    Route::middleware(['role:superadmin,hr_editor,pointage_editor'])->group(function () {
        Route::resource('time-entries', TimeEntryController::class)->except(['index', 'show']);
    });
    
    // Overtime routes
    Route::get('/overtimes', [OvertimeController::class, 'index'])->name('overtimes.index');
    Route::get('/overtimes/{overtime}', [OvertimeController::class, 'show'])->name('overtimes.show');
    Route::post('/overtimes/{timeEntry}', [OvertimeController::class, 'store'])->name('overtimes.store');
    Route::post('/overtimes/{overtime}/approve', [OvertimeController::class, 'approve'])->name('overtimes.approve');
    Route::post('/overtimes/{overtime}/reject', [OvertimeController::class, 'reject'])->name('overtimes.reject');
    
    // Routes accessible to all roles for viewing
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/time-entries', [TimeEntryController::class, 'index'])->name('time-entries.index');
    Route::get('/time-entries/{timeEntry}', [TimeEntryController::class, 'show'])->name('time-entries.show');
    
    // Reports routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
        Route::get('/create', [ReportController::class, 'create'])->name('create');
        Route::post('/', [ReportController::class, 'store'])->name('store');
        Route::get('/{report}', [ReportController::class, 'show'])->name('show');
        Route::get('/{report}/edit', [ReportController::class, 'edit'])->name('edit');
        Route::put('/{report}', [ReportController::class, 'update'])->name('update');
        Route::delete('/{report}', [ReportController::class, 'destroy'])->name('destroy');
    });
});

// Add the route for the application home page
Route::get('/', function () {
    return view('welcome');
});
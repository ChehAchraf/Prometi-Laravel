<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OvertimeController;
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
    
    // Projects routes
    Route::resource('projects', ProjectController::class);
    
    // Time entries routes
    Route::resource('time-entries', TimeEntryController::class);
    
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

    // Users routes (admin only)
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Overtime routes
    Route::get('/overtimes', [OvertimeController::class, 'index'])->name('overtimes.index');
    Route::get('/overtimes/{overtime}', [OvertimeController::class, 'show'])->name('overtimes.show');
    Route::post('/overtimes/{timeEntry}', [OvertimeController::class, 'store'])->name('overtimes.store');
    Route::post('/overtimes/{overtime}/approve', [OvertimeController::class, 'approve'])->name('overtimes.approve');
    Route::post('/overtimes/{overtime}/reject', [OvertimeController::class, 'reject'])->name('overtimes.reject');
});
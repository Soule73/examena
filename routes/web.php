<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;

// Routes publiques
Route::get('/', function () {
    return view('welcome');
});

// Routes d'authentification
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Routes protégées nécessitant une authentification
Route::middleware('auth')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Dashboards spécifiques par rôle
    Route::middleware('role:student')->group(function () {
        Route::get('/dashboard/student', [DashboardController::class, 'student'])->name('student.dashboard');
    });
    
    Route::middleware('role:teacher')->group(function () {
        Route::get('/dashboard/teacher', [DashboardController::class, 'teacher'])->name('teacher.dashboard');
    });
    
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('admin.dashboard');
        
        // Routes d'administration des utilisateurs
        Route::prefix('admin/users')->name('admin.users.')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('index');
            Route::get('/create', [UserManagementController::class, 'create'])->name('create');
            Route::post('/', [UserManagementController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        });
    });
    
    // Déconnexion
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;

// Routes publiques
Route::get('/', function () {
    return view('welcome');
});

// Routes d'authentification (sans inscription)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Routes d'authentification - Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Routes protégées
Route::middleware('auth')->group(function () {
    // Dashboard principal (redirection selon le rôle)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Tableaux de bord spécifiques
    Route::get('/student/dashboard', [DashboardController::class, 'student'])->name('student.dashboard')->middleware('role:student');
    Route::get('/teacher/dashboard', [DashboardController::class, 'teacher'])->name('teacher.dashboard')->middleware('role:teacher');
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard')->middleware('role:admin');
    
    // Routes administrateur
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserManagementController::class);
        Route::post('users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    });
});

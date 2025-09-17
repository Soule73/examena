<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Teacher\ExamController;
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
        
        // Routes de gestion des examens pour les enseignants
        // Exam routes for teachers
        Route::controller(\App\Http\Controllers\Teacher\ExamController::class)
            ->prefix('teacher/exams')
            ->name('teacher.exams.')
            ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{exam}', 'show')->name('show');
            Route::get('/{exam}/edit', 'edit')->name('edit');
            Route::put('/{exam}', 'update')->name('update');
            Route::delete('/{exam}', 'destroy')->name('destroy');
            Route::post('/{exam}/duplicate', 'duplicate')->name('duplicate');
            Route::patch('/{exam}/toggle-active', 'toggleActive')->name('toggle-active');
            Route::get('/{exam}/stats', 'stats')->name('stats');
            
            // Routes d'assignation d'examens
            Route::get('/{exam}/assign', 'showAssignForm')->name('assign');
            Route::post('/{exam}/assign', 'assignToStudents')->name('assign.store');
            Route::get('/{exam}/assignments', 'showAssignments')->name('assignments');
            Route::delete('/{exam}/assignments/{user}', 'removeAssignment')->name('assignment.remove');
            });
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
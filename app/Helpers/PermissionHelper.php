<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Vérifier si l'utilisateur connecté est un enseignant
     */
    public static function isTeacher(): bool
    {
        return Auth::check() && Auth::user()->hasRole('teacher');
    }

    /**
     * Vérifier si l'utilisateur connecté est un étudiant
     */
    public static function isStudent(): bool
    {
        return Auth::check() && Auth::user()->hasRole('student');
    }

    /**
     * Vérifier si l'utilisateur connecté est un admin
     */
    public static function isAdmin(): bool
    {
        return Auth::check() && Auth::user()->hasRole('admin');
    }

    /**
     * Vérifier si l'utilisateur peut gérer les examens
     */
    public static function canManageExams(): bool
    {
        return Auth::check() && Auth::user()->can('create-exams');
    }

    /**
     * Vérifier si l'utilisateur peut passer des examens
     */
    public static function canTakeExams(): bool
    {
        return Auth::check() && Auth::user()->can('submit-answers');
    }

    /**
     * Vérifier si l'utilisateur peut voir tous les résultats
     */
    public static function canViewAllResults(): bool
    {
        return Auth::check() && Auth::user()->can('view-all-results');
    }

    /**
     * Vérifier si l'utilisateur peut exporter des données
     */
    public static function canExportData(): bool
    {
        return Auth::check() && Auth::user()->can('export-data');
    }

    /**
     * Obtenir le rôle principal de l'utilisateur connecté
     */
    public static function getUserMainRole(): ?string
    {
        if (!Auth::check()) {
            return null;
        }

        $roles = Auth::user()->getRoleNames();
        
        // Prioriser admin > teacher > student
        if ($roles->contains('admin')) {
            return 'admin';
        } elseif ($roles->contains('teacher')) {
            return 'teacher';
        } elseif ($roles->contains('student')) {
            return 'student';
        }

        return null;
    }

    /**
     * Rediriger vers le tableau de bord approprié selon le rôle
     */
    public static function getDashboardRoute(): string
    {
        $role = self::getUserMainRole();
        
        return match($role) {
            'admin' => '/admin/dashboard',
            'teacher' => '/teacher/dashboard',
            'student' => '/student/dashboard',
            default => '/dashboard'
        };
    }
}
<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Determines if the currently authenticated user has the 'teacher' role.
     *
     * @return bool Returns true if the user is a teacher, false otherwise.
     */
    public static function isTeacher(): bool
    {
        return Auth::check() && Auth::user()->hasRole('teacher');
    }

    /**
     * Determines if the currently authenticated user has the 'student' role.
     *
     * @return bool Returns true if the user is a student, false otherwise.
     */
    public static function isStudent(): bool
    {
        return Auth::check() && Auth::user()->hasRole('student');
    }

    /**
     * Determines if the currently authenticated user has the 'admin' role.
     *
     * @return bool Returns true if the user is an admin, false otherwise.
     */
    public static function isAdmin(): bool
    {
        return Auth::check() && Auth::user()->hasRole('admin');
    }

    /**
     * Determines if the currently authenticated user can manage exams.
     *
     * @return bool Returns true if the user can manage exams, false otherwise.
     */
    public static function canManageExams(): bool
    {
        return Auth::check() && Auth::user()->can('create-exams');
    }

    /**
     * Determines if the currently authenticated user can take exams.
     *
     * @return bool Returns true if the user can take exams, false otherwise.
     */
    public static function canTakeExams(): bool
    {
        return Auth::check() && Auth::user()->can('submit-answers');
    }

    /**
     * Determines if the currently authenticated user can view all exam results.
     *
     * @return bool Returns true if the user can view all exam results, false otherwise.
     */
    public static function canViewAllResults(): bool
    {
        return Auth::check() && Auth::user()->can('view-all-results');
    }

    /**
     * Determines if the currently authenticated user can export data.
     *
     * @return bool Returns true if the user can export data, false otherwise.
     */
    public static function canExportData(): bool
    {
        return Auth::check() && Auth::user()->can('export-data');
    }

    /**
     * Get the main role of the authenticated user.
     * 
     * Prioritizes roles in the order: admin > teacher > student.
     * 
     * @return string|null The main role of the user, or null if not authenticated or no role found.
     */
    public static function getUserMainRole(): ?string
    {
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();
        $roles = $user->getRoleNames();

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
     * Get the dashboard route based on the user's role.
     */
    public static function getDashboardRoute(): string
    {
        $role = self::getUserMainRole();


        return match ($role) {
            'admin' => route('admin.dashboard'),
            'teacher' => route('teacher.dashboard'),
            'student' => route('student.dashboard'),
            default => route('dashboard')
        };
    }
}

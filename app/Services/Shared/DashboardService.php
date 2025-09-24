<?php

namespace App\Services\Shared;

use App\Models\User;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    /**
     * Déterminer le type de dashboard selon le rôle de l'utilisateur
     */
    public function getDashboardType(?User $user = null): string
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            throw new \Exception('Utilisateur non authentifié');
        }

        return PermissionHelper::getUserMainRole();
    }

    /**
     * Obtenir l'URL de redirection appropriée selon le rôle
     */
    public function getDashboardRoute(?User $user = null): string
    {
        $role = $this->getDashboardType($user);

        return match ($role) {
            'admin' => 'admin.dashboard',
            'teacher' => 'teacher.dashboard',
            'student' => 'student.dashboard',
            default => throw new \Exception('Rôle non reconnu: ' . $role)
        };
    }

    /**
     * Vérifier si l'utilisateur a accès au type de dashboard demandé
     */
    public function canAccessDashboard(string $dashboardType, ?User $user = null): bool
    {
        $userRole = $this->getDashboardType($user);

        return $userRole === $dashboardType;
    }
}

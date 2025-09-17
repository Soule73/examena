<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            abort(401, 'Non autorisé');
        }

        $user = Auth::user();
        
        // Vérifier si l'utilisateur a l'un des rôles requis
        if (!$user->hasAnyRole($roles)) {
            abort(403, 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
        }

        return $next($request);
    }
}

<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class ProfessorDeviceRestrictionMiddleware
{
    public function handle($request, Closure $next)
    {
        // Only apply for teachers
        if ($request->user() && $request->user()->hasRole('teacher')) {
            $profEmail = $request->user()->email;
            $macAddress = $request->input('mac_address'); // Must be sent by client app
            $allowedMac = Redis::get('mac:' . $profEmail);

            // If no MAC restriction set, allow access
            if ($allowedMac && $allowedMac !== $macAddress) {
                abort(403, 'Accès refusé : appareil non autorisé pour ce professeur.');
            }
        }
        return $next($request);
    }
}

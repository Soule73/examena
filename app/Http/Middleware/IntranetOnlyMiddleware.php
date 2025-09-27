<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IntranetOnlyMiddleware
{
    public function handle($request, Closure $next)
    {
        $ip = $request->ip();
        $allowed = explode(',', env('INTRANET_IPS', '10.,192.168.,172.16.,172.17.,172.18.,172.19.,172.20.,172.21.,172.22.,172.23.,172.24.,172.25.,172.26.,172.27.,172.28.,172.29.,172.30.,172.31.'));
        $isAllowed = false;

        foreach ($allowed as $prefix) {
            if (strpos($ip, $prefix) === 0) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            \Log::warning('Blocked IP: ' . $ip);
            abort(403, 'Accès réservé au réseau intranet.');
        }

        return $next($request);
    }
}

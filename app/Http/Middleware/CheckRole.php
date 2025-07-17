<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next, $role)
    {
        // Vérifie si l'utilisateur est connecté
        $user = Auth::user(); 

        // Vérifie si l'utilisateur a le rôle requis
        if (!$user || !$user->hasRole($role)) {
            abort(403, 'Accès interdit');
        }

        return $next($request);
    }
}
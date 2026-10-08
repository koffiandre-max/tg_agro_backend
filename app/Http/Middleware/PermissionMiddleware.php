<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Vérifie que l'utilisateur possède au moins une des permissions passées
     * au middleware : Route::middleware('permission:farms.edit') ou
     * Route::middleware('permission:farms.edit,farms.create').
     *
     * L'administrateur passe toujours (bypass).
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Accès non autorisé.');
        }

        if ($user->role === 'admin') {
            return $next($request);
        }

        if (empty($permissions)) {
            abort(403, 'Aucune permission définie pour cette route.');
        }

        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        abort(403, 'Accès non autorisé.');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware pour autoriser les administrateurs ET les techniciens.
 * Utilisé sur les routes partagées (ex: rapports de visite) où
 * les deux rôles ont besoin d'accéder à la même ressource.
 */
class AdminOrTechnicianMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role, ['admin', 'technician'])) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}

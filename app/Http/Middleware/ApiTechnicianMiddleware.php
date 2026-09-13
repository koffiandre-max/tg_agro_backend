<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vérifie que l'utilisateur authentifié via l'API possède le rôle "technician",
 * indispensable pour accéder aux endpoints de l'espace technicien mobile.
 */
class ApiTechnicianMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('api');

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentification requise.',
            ], 401);
        }

        if ($user->role !== 'technician') {
            Log::channel('api')->warning('API - Accès technicien refusé', [
                'user_id' => $user->id,
                'role' => $user->role,
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Accès réservé à l\'espace technicien.',
            ], 403);
        }

        return $next($request);
    }
}
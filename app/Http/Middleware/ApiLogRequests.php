<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Journalise chaque requête API (méthode, chemin, statut, durée, utilisateur)
 * dans le canal `api` afin de faciliter le débogage de l'application mobile.
 */
class ApiLogRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        try {
            $response = $next($request);
        } catch (\Throwable $e) {
            Log::channel('api')->error('API - Exception non gérée', [
                'method' => $request->method(),
                'path' => $request->path(),
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
            ]);

            throw $e;
        }

        $duration = round((microtime(true) - $start) * 1000, 2);

        $user = $request->user('api');

        if ($response->getStatusCode() >= 400) {
            Log::channel('api')->warning('API - Réponse non-2xx', [
                'method' => $request->method(),
                'path' => $request->path(),
                'status' => $response->getStatusCode(),
                'duration_ms' => $duration,
                'user_id' => $user?->id,
                'role' => $user?->role,
                'ip' => $request->ip(),
            ]);
        } else {
            Log::channel('api')->info('API - Requête', [
                'method' => $request->method(),
                'path' => $request->path(),
                'status' => $response->getStatusCode(),
                'duration_ms' => $duration,
                'user_id' => $user?->id,
                'role' => $user?->role,
                'ip' => $request->ip(),
            ]);
        }

        return $response;
    }
}
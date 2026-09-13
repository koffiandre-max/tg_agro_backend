<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware "feature:xxx" utilisé par les routes des modules.
 *
 * Vérifie que la feature demandée est active pour le business courant.
 * Par défaut (aucune table features), il laisse passer pour ne pas
 * bloquer les modules lors du développement / sur un hôte simplifié.
 */
class CheckFeatureMiddleware
{
    public function handle(Request $request, Closure $next, string ...$features)
    {
        // Si aucune feature n'est exigée, on passe.
        if (empty($features)) {
            return $next($request);
        }

        $user = Auth::user();

        // Module public ou utilisateur non connecté : on laisse passer
        // (les modules gèrent eux-mêmes leur garde.
        if (! $user) {
            return $next($request);
        }

        // Vérification via la table "features" si elle existe.
        if (\Illuminate\Support\Facades\Schema::hasTable('features')) {
            $code = $features[0] ?? null;

            if ($code) {
                $exists = \Illuminate\Support\Facades\DB::table('features')
                    ->where('code', $code)
                    ->exists();

                if (! $exists) {
                    abort(403, 'Cette fonctionnalité n\'est pas activée pour votre compte.');
                }
            }
        }

        return $next($request);
    }
}

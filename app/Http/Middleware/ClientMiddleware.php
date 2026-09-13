<?php

namespace App\Http\Middleware;

use App\Models\Subscription;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ClientMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'client') {
            abort(403, 'Accès non autorisé.');
        }

        $client = $user->client;
        
        if (!$client) {
            abort(403, 'Aucune fiche client trouvée.');
        }

        $activeSubscription = Subscription::where('user_id', $client->user_id)
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->exists();

        $subscriptionExpired = !$activeSubscription;

        if ($subscriptionExpired) {
            $method = $request->method();
            
            if (!in_array($method, ['GET', 'HEAD', 'OPTIONS'])) {
                abort(403, 'Votre abonnement est expiré ou inactif. Vous ne pouvez plus effectuer cette action.');
            }
        }

        View::share('subscriptionExpired', $subscriptionExpired);

        return $next($request);
    }
}

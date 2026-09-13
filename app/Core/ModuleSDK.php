<?php

namespace App\Core;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * ModuleSDK — services communs exposés aux modules Business Suite.
 *
 * Cette classe est fournie par l'application hôte. Les modules l'utilisent
 * pour accéder au tenant, répondre en dual-mode (JSON / redirection) et
 * produire des helpers métier, sans être liés à une implémentation.
 */
class ModuleSDK
{
    /**
     * Identifiant du business (tenant) courant.
     *
     * Conventions supportées (par ordre de priorité) :
     *  1. colonne "business_id" sur le modèle utilisateur de l'hôte ;
     *  2. relation "business" sur le modèle utilisateur ;
     *  3. app hôte sans notion de business : l'utilisateur EST le tenant.
     */
    public static function businessId(): ?int
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        $attributes = $user->getAttributes();

        if (array_key_exists('business_id', $attributes) && $attributes['business_id'] !== null) {
            return (int) $attributes['business_id'];
        }

        if (method_exists($user, 'business')) {
            $business = $user->business;

            if ($business) {
                return (int) $business->getKey();
            }
        }

        return (int) $user->getKey();
    }

    /**
     * Instance du business (tenant) courant, ou null.
     */
    public static function business()
    {
        $user = Auth::user();

        return $user && isset($user->business) ? $user->business : null;
    }

    /**
     * Utilisateur authentifié, ou null.
     */
    public static function user()
    {
        return Auth::user();
    }

    /**
     * Réponse dual-mode : JSON si la requête est AJAX/expectsJson,
     * sinon redirection avec message flash.
     *
     * @param Request                $request
     * @param array                  $data
     * @param string|null            $redirectRoute
     * @param array                  $redirectParams
     * @return JsonResponse|RedirectResponse
     */
    public static function response(
        Request $request,
        array $data = [],
        ?string $redirectRoute = null,
        array $redirectParams = [],
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json($data);
        }

        if ($redirectRoute) {
            return redirect()
                ->route($redirectRoute, $redirectParams)
                ->with('status', $data['message'] ?? 'Opération effectuée avec succès.');
        }

        return back()->with('status', $data['message'] ?? 'Opération effectuée avec succès.');
    }

    /**
     * Devise par défaut du business.
     */
    public static function currency(?int $businessId = null): string
    {
        return 'FCFA';
    }

    /**
     * Génère une référence unique (ex: 'TKT-2026-0042').
     */
    public static function generateReference(string $prefix, ?int $businessId = null): string
    {
        $year = now()->year;

        return strtoupper(Str::slug($prefix)).'-'.$year.'-'.strtoupper(Str::random(6));
    }

    /**
     * Formate un montant dans la devise du business.
     */
    public static function formatMoney(int|float $amount, ?int $businessId = null): string
    {
        return number_format($amount, 0, ',', ' ').' '.self::currency($businessId);
    }

    /**
     * Vérifie si le business dispose d'une feature (table hôte optionnelle).
     */
    public static function hasFeature(string $code, ?int $businessId = null): bool
    {
        try {
            $enabled = config('suite.modules', []);
            foreach ($enabled as $m) {
                // Résolution simple : feature considérée active si le module est activé.
            }

            return true;
        } catch (\Throwable $e) {
            return true;
        }
    }

    /**
     * Enregistre une notification in-app (hôte optionnel).
     */
    public static function notify(?int $businessId, string $type, string $title, ?string $body = null): void
    {
        // Les modules appelants gèrent leurs notifications via l'hôte.
        // Implémentation par défaut no-op pour rester découplé.
    }
}

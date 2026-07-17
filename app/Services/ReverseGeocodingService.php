<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ReverseGeocodingService
{
    /**
     * Retourne l'adresse à partir de coordonnées GPS.
     * Utilise l'API Nominatim (OpenStreetMap) - gratuite, sans clé.
     */
    public function getAddress(float $latitude, float $longitude): ?string
    {
        $cacheKey = "geocode:{$latitude},{$longitude}";

        // 1. On tente d'abord de récupérer l'adresse depuis le cache
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::withHeaders([
                // Nominatim demande un User-Agent clair et valide. 
                // Assurez-vous que l'adresse email ou le nom dans config('app.name') est correct.
                'User-Agent' => config('app.name', 'TG-Agro') . ' (contact@tg-agro.com)',
                'Accept' => 'application/json',
                'Accept-Language' => 'fr', // Force l'adresse en français
            ])
                ->timeout(10)
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'json',
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'zoom' => 18,
                    'addressdetails' => 1,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $address = $data['display_name'] ?? null;

                // 2. IMPORTANT : On ne met en cache QUE si on a bien une adresse valide !
                if ($address) {
                    Cache::put($cacheKey, $address, now()->addDays(30));
                    return $address;
                }
            }
        } catch (\Exception $e) {
            // Optionnel : Loggez l'erreur pour comprendre pourquoi ça échoue en local
            \Log::error("Erreur de géocodage ({$latitude}, {$longitude}) : " . $e->getMessage());
        }

        return null;
    }
}

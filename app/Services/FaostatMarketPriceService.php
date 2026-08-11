<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FaostatMarketPriceService
{
    protected string $baseUrl;
    protected ?string $apiKey;
    protected int $timeout;
    protected int $ttl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.faostat.base_url', 'https://api.faostat.org/v1/data'), '/');
        $this->apiKey = config('services.faostat.api_key');
        $this->timeout = (int) config('services.faostat.timeout', 10);
        $this->ttl = (int) config('services.faostat.cache_ttl', 86400);
    }

    /**
     * Retourne les prix de certains produits agricoles pour la Côte d'Ivoire.
     *
     * @param array|null $products Liste de codes éléments FAO (optionnel)
     * @return array{period: string, country: string, currency: string, products: array, source: string}
     */
    public function getCoteDIvoirePrices(?array $products = null): array
    {
        $products = $products ?? config('market.products', []);
        $country = 'Côte d\'Ivoire';
        $countryCode = 214; // Code FAO de la Côte d'Ivoire

        $cacheKey = 'faostat_prices_ci_' . md5(json_encode($products));

        return Cache::remember($cacheKey, $this->ttl, function () use ($products, $country, $countryCode) {
            $items = [];

            foreach ($products as $product) {
                $item = $this->fetchProduct($product, $countryCode);
                if ($item) {
                    $items[] = $item;
                }
            }

            return [
                'country' => $country,
                'period' => $this->resolvePeriod($items),
                'currency' => 'USD',
                'source' => 'FAOSTAT',
                'products' => $items,
            ];
        });
    }

    protected function fetchProduct(array $product, int $countryCode): ?array
    {
        $dataset = $product['dataset'] ?? 'PD_PRICES';
        $itemCode = $product['item_code'] ?? null;
        $elementCode = $product['element_code'] ?? 5532; // Prix au producteur (USD/t)

        if (!$itemCode) {
            return null;
        }

        try {
            $query = [
                'area_code' => $countryCode,
                'item_code' => $itemCode,
                'element_code' => $elementCode,
                'output_type' => 'objects',
                'limit' => 50,
            ];

            if ($this->apiKey) {
                $query['api_key'] = $this->apiKey;
            }

            $response = Http::timeout($this->timeout)
                ->get($this->baseUrl . '/' . $dataset, $query);

            if (!$response->successful()) {
                Log::warning('FAOSTAT request failed', [
                    'dataset' => $dataset,
                    'item_code' => $itemCode,
                    'status' => $response->status(),
                ]);
                return null;
            }

            $data = $response->json('data') ?? $response->json() ?? [];
            $latest = $this->pickLatest($data);

            if (!$latest) {
                return null;
            }

            return [
                'name' => $product['name'] ?? ($latest['item'] ?? $product['item_code']),
                'price' => $this->toNumber($latest['value'] ?? null),
                'unit' => $product['unit'] ?? 't',
                'year' => $latest['year'] ?? $latest['period'] ?? null,
                'recorded_at' => $latest['date'] ?? ($latest['year'] ?? null),
            ];
        } catch (\Throwable $e) {
            Log::error('FAOSTAT exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function pickLatest(array $data): ?array
    {
        if (empty($data)) {
            return null;
        }

        usort($data, function ($a, $b) {
            $ya = (int) ($a['year'] ?? $a['period'] ?? 0);
            $yb = (int) ($b['year'] ?? $b['period'] ?? 0);
            return $yb <=> $ya;
        });

        return $data[0];
    }

    protected function resolvePeriod(array $items): string
    {
        $years = array_filter(array_column($items, 'year'));
        if (empty($years)) {
            return 'Période non disponible';
        }
        $max = max($years);
        $min = min($years);
        return $min === $max ? (string) $max : $min . '–' . $max;
    }

    protected function toNumber($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        return (float) $value;
    }
}

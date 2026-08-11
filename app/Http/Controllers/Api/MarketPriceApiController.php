<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FaostatMarketPriceService;
use Illuminate\Http\JsonResponse;

class MarketPriceApiController extends Controller
{
    public function __construct(
        protected FaostatMarketPriceService $service
    ) {}

    public function coteDIvoire(): JsonResponse
    {
        $data = $this->service->getCoteDIvoirePrices();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}

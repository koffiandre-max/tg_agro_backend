<?php

namespace App\Http\Controllers\Portail;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\MarketPrice;
use Illuminate\Http\Request;

class PortailDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $client = $user?->client;

        $farms = collect();

        if ($client) {
            $farms = Farm::where('user_id', $client->user_id)
                ->orderBy('name')
                ->get();
        }

        $marketPrices = MarketPrice::orderBy('product_name')
            ->limit(6)
            ->get();

        return view('portail.dashboard', compact('farms', 'marketPrices'));
    }

    public function farms()
    {
        $user = auth()->user();
        $client = $user?->client;


        $farms = collect();

        if ($client) {
            $farms = Farm::where('user_id', $client->user_id)
                ->orderBy('name')
                ->get();

        }

        return view('portail.farms', compact('farms'));
    }
}

<?php

namespace App\Http\Controllers\Portail;

use App\Http\Controllers\Controller;
use App\Models\Farm;
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

        return view('portail.dashboard', compact('farms'));
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

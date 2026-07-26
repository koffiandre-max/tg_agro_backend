<?php

namespace App\Http\Controllers\Portail;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ClientMiddleware;
use App\Models\DataEntry;
use Illuminate\Support\Facades\Auth;

class DataEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware(ClientMiddleware::class);
    }

    public function index()
    {
        $user = Auth::user();
        $client = $user?->client;

        $entries = collect();

        if ($client) {
            $entries = DataEntry::with(['farm', 'technician'])
                ->where('client_id', $client->id)
                ->where('status', 'validated')
                ->latest()
                ->get();
        }

        return view('portail.data.index', compact('entries'));
    }
}
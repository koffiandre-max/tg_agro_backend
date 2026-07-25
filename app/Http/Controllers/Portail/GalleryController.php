<?php

namespace App\Http\Controllers\Portail;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ClientMiddleware;
use App\Models\Client;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    public function __construct()
    {
        $this->middleware(ClientMiddleware::class);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $client = Client::where('user_id', $user->id)->firstOrFail();

        $query = Photo::with(['farm', 'technician'])
            ->where('client_id', $client->id)
            ->where('is_validated', true);

        if ($request->filled('farm_id')) {
            $query->where('farm_id', $request->farm_id);
        }

        $photos = $query->latest()->paginate(24);

        $farms = \App\Models\Farm::whereHas('clients', function ($q) use ($client) {
            $q->where('clients.id', $client->id);
        })->orderBy('name')->get();

        return view('portail.gallery', compact('photos', 'client', 'farms'));
    }
}

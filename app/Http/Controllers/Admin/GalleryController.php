<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Farm;
use App\Models\Photo;
use App\Models\User;
use App\Services\ReverseGeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    public function geocode(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $service = new ReverseGeocodingService();
        $address = $service->getAddress($validated['latitude'], $validated['longitude']);

        return response()->json([
            'address' => $address,
        ]);
    }

    public function index(Request $request)
    {
        $query = Photo::with(['farm', 'client.user', 'technician']);

        // Filtre par client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filtre par plantation / ferme
        if ($request->filled('farm_id')) {
            $query->where('farm_id', $request->farm_id);
        }

        $user = Auth::user();

        // Filtre par technicien (admin uniquement)
        if ($request->filled('technician_id') && $user->role === 'admin') {
            $query->where('technician_id', $request->technician_id);
        }

        // Si technicien connecté, ne voir que ses photos
        if ($user->role === 'technician') {
            $query->where('technician_id', $user->id);
        }

        $photos = $query->latest()->paginate(24);

        // Données pour les filtres
        $clients = Client::with('user')
            ->join('users', 'users.id', '=', 'clients.user_id')
            ->orderBy('users.name')
            ->select('clients.*')
            ->get();

        $farms = Farm::orderBy('name')->get();

        $technicians = collect();
        if ($user->role === 'admin') {
            $technicians = User::where('role', 'technician')->orderBy('name')->get();
        }

        return view('admin.gallery.index', compact('photos', 'clients', 'farms', 'technicians'));
    }
}

<?php

namespace App\Http\Controllers\Technitian;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Farm;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function create()
    {
        $clients = Client::with('user')
            ->join('users', 'users.id', '=', 'clients.user_id')
            ->orderBy('users.name')
            ->select('clients.*')
            ->get();

        $farms = Farm::with('clients.user')
            ->orderBy('name')
            ->get();

        return view('technitian.photos.create', compact('clients', 'farms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'farm_id' => 'required|integer|exists:farms,id',
            'photos' => 'required|array',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'caption' => 'nullable|string|max:500',
        ]);

        $now = now();
        $uploaded = 0;

        foreach ($request->file('photos') as $photo) {
            $path = $photo->store('gallery', 'public');

            $size = $photo->getSize();

            Photo::create([
                'farm_id' => $validated['farm_id'],
                'client_id' => $validated['client_id'],
                'technician_id' => Auth::id(),
                'photo_path' => $path,
                'thumbnail_path' => null,
                'caption' => $validated['caption'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'taken_at' => $now,
                'is_visible_to_client' => false,
                'file_size' => $size,
            ]);

            $uploaded++;
        }

        return redirect()->route('admin.technitian.photos.create')
            ->with('success', "$uploaded photo(s) ajoutée(s) avec succès.");
    }
}

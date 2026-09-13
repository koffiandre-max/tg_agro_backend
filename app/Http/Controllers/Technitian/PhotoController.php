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
    public function index(Request $request)
    {
        $user = Auth::user();
        $technicianId = $user->id;
        $technician = \App\Models\Technician::where('user_id', $user->id)->firstOrFail();

        $query = Photo::with(['farm', 'client', 'technician'])
            ->where('technician_id', $technicianId)
            ->where('is_validated', true);

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('farm_id')) {
            $query->where('farm_id', $request->farm_id);
        }

        $photos = $query->latest()->get()->groupBy(function ($photo) {
            return $photo->farm?->name ?? 'Sans exploitation';
        });

        $pendingCount = Photo::where('is_validated', false)
            ->where('technician_id', $technicianId)
            ->count();

        $farmIds = \App\Models\Farm::where('assigned_technician_id', $technician->id)->pluck('id');

        $clients = Client::query()
            ->where('assigned_technician_id', $technician->id)
            ->orWhereIn('user_id', function ($query) use ($farmIds) {
                $query->select('user_id')->from('farms')->whereIn('id', $farmIds);
            })
            ->orWhereHas('assignedFarms', function ($query) use ($farmIds) {
                $query->whereIn('farms.id', $farmIds);
            })
            ->distinct()
            ->orderBy('code')
            ->get();

        $farms = \App\Models\Farm::where('assigned_technician_id', $technician->id)->orderBy('name')->get();

        return view('technician.gallery.index', compact('photos', 'clients', 'farms', 'pendingCount'));
    }

    public function create()
    {
        $user = Auth::user();
        $technician = \App\Models\Technician::where('user_id', $user->id)->firstOrFail();

        $farmIds = \App\Models\Farm::where('assigned_technician_id', $technician->id)->pluck('id');

        $clients = Client::query()
            ->where('assigned_technician_id', $technician->id)
            ->orWhereIn('user_id', function ($query) use ($farmIds) {
                $query->select('user_id')->from('farms')->whereIn('id', $farmIds);
            })
            ->orWhereHas('assignedFarms', function ($query) use ($farmIds) {
                $query->whereIn('farms.id', $farmIds);
            })
            ->distinct()
            ->orderBy('code')
            ->get();

        $farms = \App\Models\Farm::where('assigned_technician_id', $technician->id)
            ->with('clients.user')
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
                'is_validated' => false,
                'file_size' => $size,
            ]);

            $uploaded++;
        }

        return redirect()->route('admin.technitian.gallery.index')
            ->with('success', "$uploaded photo(s) ajoutée(s) avec succès.");
    }
}

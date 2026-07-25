<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Farm;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Http\Request;

class PhotoValidationController extends Controller
{
    public function index()
    {
        $photos = Photo::with(['farm', 'client.user', 'technician'])
            ->where('is_validated', false)
            ->latest()
            ->get()
            ->groupBy(function ($photo) {
                return $photo->farm?->name ?? 'Sans exploitation';
            });

        $clients = Client::with('user')
            ->join('users', 'users.id', '=', 'clients.user_id')
            ->orderBy('users.name')
            ->select('clients.*')
            ->get();

        $farms = Farm::orderBy('name')->get();

        $technicians = User::where('role', 'technician')->orderBy('name')->get();

        return view('admin.photos.validation', compact('photos', 'clients', 'farms', 'technicians'));
    }

    public function approve($id)
    {
        $photo = Photo::findOrFail($id);
        $photo->update([
            'is_validated' => true,
            'validated_at' => now(),
        ]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Photo validée avec succès.');
    }

    public function reject($id)
    {
        $photo = Photo::findOrFail($id);
        $photo->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Photo supprimée avec succès.');
    }
}

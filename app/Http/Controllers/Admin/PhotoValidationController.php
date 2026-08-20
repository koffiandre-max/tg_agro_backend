<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Farm;
use App\Models\Photo;
use App\Models\User;
use App\Services\SendmailService;
use Illuminate\Http\Request;

class PhotoValidationController extends Controller
{
    public function __construct(private SendmailService $mailer) {}

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

        $admins = User::where('role', 'admin')
            ->where('is_active', true)
            ->get();

        foreach ($admins as $admin) {
            if ($admin->email) {
                $this->mailer->sendView(
                    $admin->email,
                    'Photo validée : ' . ($photo->farm?->name ?? 'Photo'),
                    'emails.photos.validated',
                    ['photo' => $photo]
                );
            }
        }

        if ($photo->client?->user?->email) {
            $this->mailer->sendView(
                $photo->client->user->email,
                'Photo validée : ' . ($photo->farm?->name ?? 'Photo'),
                'emails.photos.validated',
                ['photo' => $photo]
            );
        }

        if ($photo->technician?->email) {
            $this->mailer->sendView(
                $photo->technician->email,
                'Photo validée : ' . ($photo->farm?->name ?? 'Photo'),
                'emails.photos.validated',
                ['photo' => $photo]
            );
        }

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

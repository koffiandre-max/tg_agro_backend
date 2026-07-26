<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataEntry;
use App\Services\SendmailService;
use Illuminate\Http\Request;

class DataValidationController extends Controller
{
    public function __construct(private SendmailService $mailer) {}

    public function index()
    {
        $entries = DataEntry::with(['farm', 'client.user', 'technician'])
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->groupBy('farm.name');

        return view('technitian.data.index', compact('entries'));
    }

    public function validate(DataEntry $dataEntry)
    {
        $dataEntry->update([
            'status' => 'validated',
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        if ($dataEntry->client?->user?->email) {
            $this->mailer->sendView(
                $dataEntry->client->user->email,
                'Saisie de données validée',
                'emails.data.validated',
                ['dataEntry' => $dataEntry]
            );
        }

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Saisie de données validée avec succès.');
    }

    public function reject(Request $request, DataEntry $dataEntry)
    {
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $dataEntry->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        if ($dataEntry->client?->user?->email) {
            $this->mailer->sendView(
                $dataEntry->client->user->email,
                'Saisie de données rejetée',
                'emails.data.rejected',
                ['dataEntry' => $dataEntry]
            );
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Saisie de données rejetée avec succès.');
    }
}

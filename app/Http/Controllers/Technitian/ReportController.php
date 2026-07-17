<?php

namespace App\Http\Controllers\Technitian;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Farm;
use App\Models\Report;
use App\Models\User;
use App\Services\SendmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'technician') {
            abort(403, 'Accès non autorisé.');
        }

        $farms = Farm::query();

        // Filtrer selon le technicien
        if ($user->technician) {
            $farms = $farms->where('assigned_technician_id', $user->technician->id);
        }

        $farms = $farms->with(['user', 'clients.user'])
            ->orderBy('name')
            ->get();

        // Récupérer les clients associés aux farms
        $clientIds = $farms->flatMap(function ($farm) {
            return $farm->clients->pluck('id');
        })->unique();

        $clients = Client::whereIn('id', $clientIds)
            ->join('users', 'users.id', '=', 'clients.user_id')
            ->orderBy('users.name')
            ->select('clients.*')
            ->with('user')
            ->get();

        if ($clients->isEmpty()) {
            $clients = Client::join('users', 'users.id', '=', 'clients.user_id')
                ->orderBy('users.name')
                ->select('clients.*')
                ->with('user')
                ->get();
        }

        $formAction = route('admin.technitian.reports.store');

        return view('technitian.reports.create', compact('farms', 'clients', 'formAction'));
    }

    public function __construct(private SendmailService $mailer) {}

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'technician') {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'farm_id' => 'required|integer|exists:farms,id',
            'client_id' => 'required|integer|exists:clients,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:monthly,soil_analysis,harvest,other,inspection,diagnostic,suivi',
            'file' => 'required|file|mimes:pdf|max:10240',
            'notes' => 'nullable|string',
        ]);

        $path = $request->file('file')->store('reports', 'public');

        $report = Report::create([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'farm_id' => $validated['farm_id'],
            'client_id' => $validated['client_id'],
            'technician_id' => $user->id,
            'file_path' => $path,
            'file_original_name' => $request->file('file')->getClientOriginalName(),
            'file_size' => $request->file('file')->getSize(),
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        // Notification de l'administrateur (étape 2 : Admin reçoit notification)
        $admins = User::where('role', 'admin')
            ->where('is_active', true)
            ->get();

        foreach ($admins as $admin) {
            if ($admin->email) {
                $this->mailer->sendView(
                    $admin->email,
                    'Nouveau rapport à valider : ' . $report->title,
                    'emails.reports.submitted',
                    ['report' => $report]
                );
            }
        }

        return redirect()->route('admin.technitian.reports.create')
            ->with('success', 'Rapport créé avec succès et en attente de validation.');
    }
}

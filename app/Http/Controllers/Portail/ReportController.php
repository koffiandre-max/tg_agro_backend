<?php

namespace App\Http\Controllers\Portail;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Liste des rapports disponibles pour le client connecté (étape 5).
     */
    public function index()
    {
        $user = Auth::user();

        $client = $user?->client;

        $reports = collect();

        if ($client) {
            $reports = Report::query()
                ->where('client_id', $client->id)
                ->where('status', 'validated')
                ->with(['farm', 'technician', 'validator'])
                ->orderByDesc('validated_at')
                ->orderByDesc('created_at')
                ->get();
        }

        return view('portail.reports.index', compact('reports'));
    }

    /**
     * Téléchargement du rapport PDF depuis l'espace client (étape 5).
     * Le client ne peut télécharger que ses propres rapports validés.
     */
    public function download(Report $report)
    {
        $user = Auth::user();
        $client = $user?->client;

        if (!$client || $report->client_id !== $client->id) {
            abort(403, 'Vous n\'êtes pas autorisé à télécharger ce rapport.');
        }

        if ($report->status !== 'validated') {
            return redirect()->route('admin.portail.reports')
                ->with('error', 'Ce rapport n\'est pas encore disponible au téléchargement.');
        }

        if (!$report->file_path || !Storage::disk('public')->exists($report->file_path)) {
            return redirect()->route('admin.portail.reports')
                ->with('error', 'Fichier introuvable.');
        }

        // Marquer comme vu par le client
        if (!$report->seen_by_client) {
            $report->update(['seen_by_client' => true]);
        }

        return Storage::disk('public')->download(
            $report->file_path,
            $report->file_original_name ?? 'rapport.pdf'
        );
    }
}
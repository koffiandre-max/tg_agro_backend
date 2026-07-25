<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\SendmailService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    protected $sendmailService;
    public function __construct(SendmailService $sendmailService)
    {
        $this->sendmailService = $sendmailService;
    }
    public function index()
    {
        return view('admin.reports.index');
    }

    public function datatable()
    {
        return view('admin.reports.datatable');
    }

    public function show(Report $report)
    {
        $report->load(['farm', 'client.user', 'technician', 'validator']);

        return view('admin.reports.show', compact('report'));
    }

    public function download(Report $report)
    {
        if (!$report->file_path || !Storage::disk('public')->exists($report->file_path)) {
            return redirect()->back()->with('error', 'Fichier introuvable.');
        }

        return Storage::disk('public')->download(
            $report->file_path,
            $report->file_original_name ?? 'rapport.pdf'
        );
    }

    public function viewPdf(Report $report)
    {
        if (!$report->file_path || !Storage::disk('public')->exists($report->file_path)) {
            abort(404, 'Fichier introuvable.');
        }

        return response()->file(Storage::disk('public')->path($report->file_path));
    }

    public function create()
    {
        $farmData = app(FarmService::class)->getFarmsAndClients();
        extract($farmData->toArray());

        $formAction = route('admin.reports.store');

        return view('admin.reports.create', compact('farms', 'clients', 'formAction'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'farm_id' => 'required|integer|exists:farms,id',
                'client_id' => 'required|integer|exists:clients,id',
                'title' => 'required|string|max:255',
                'type' => 'required|string|in:monthly,soil_analysis,harvest,other,inspection,diagnostic,suivi',
                'file' => 'required|file|mimes:pdf|max:10240',
                'notes' => 'nullable|string|max:500',
            ]);

            $path = $request->file('file')->store('reports', 'public');

            $report = Report::create([
                'title' => $validated['title'],
                'type' => $validated['type'],
                'farm_id' => $validated['farm_id'],
                'client_id' => $validated['client_id'],
                'technician_id' => Auth::user()->id,
                'file_path' => $path,
                'file_original_name' => $request->file('file')->getClientOriginalName(),
                'file_size' => $request->file('file')->getSize(),
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);

            $this->sendmailService->sendView(
                env("ADMINSTOR_EMAIL"),
                "Creation de nouveau rapport : " . $report->title,
                "emails.reports.submitted",
                ["report" => $report]
            );

            return redirect()->route('admin.reports.show', $report)->with('success', 'Rapport créé avec succès.');
        } catch (Exception $e) {
            Log::error('Failed to create report: '.$e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création du rapport.');
        }
    }
}

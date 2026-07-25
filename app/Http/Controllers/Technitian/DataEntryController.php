<?php

namespace App\Http\Controllers\Technitian;

use App\Http\Controllers\Controller;
use App\Http\Middleware\TechnicianMiddleware;
use App\Models\DataEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware(TechnicianMiddleware::class);
    }

    public function index()
    {
        $user = Auth::user();

        return view('technitian.data.index');
    }

    public function create()
    {
        $user = Auth::user();
        $farmData = app(FarmService::class)->getFarmsAndClients();
        extract($farmData->toArray());

        return view('technitian.data.create', compact('farms', 'clients'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'farm_id' => 'required|integer|exists:farms,id',
            'client_id' => 'required|integer|exists:clients,id',
            'crop_stage' => 'nullable|string|max:255',
            'crop_stage_progress' => 'nullable|integer|min:0|max:100',
            'estimated_harvest_date' => 'nullable|date',
            'inputs_used' => 'nullable|string|max:500',
            'observations' => 'nullable|string|max:1000',
            'weather_conditions' => 'nullable|string|max:255',
        ]);

        DataEntry::create([
            'farm_id' => $validated['farm_id'],
            'client_id' => $validated['client_id'],
            'technician_id' => $user->id,
            'crop_stage' => $validated['crop_stage'] ?? null,
            'crop_stage_progress' => $validated['crop_stage_progress'] ?? null,
            'estimated_harvest_date' => $validated['estimated_harvest_date'] ?? null,
            'inputs_used' => $validated['inputs_used'] ?? null,
            'observations' => $validated['observations'] ?? null,
            'weather_conditions' => $validated['weather_conditions'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.technitian.data.create')
            ->with('success', 'Saisie de données créée avec succès et en attente de validation.');
    }

    public function show($id)
    {
        $entry = DataEntry::with(['farm', 'client.user', 'technician'])->findOrFail($id);

        return view('technitian.data.show', compact('entry'));
    }

    public function edit($id)
    {
        $entry = DataEntry::with(['farm', 'client.user'])->findOrFail($id);
        $farmData = app(FarmService::class)->getFarmsAndClients();
        extract($farmData->toArray());

        return view('technitian.data.edit', compact('entry', 'farms', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $entry = DataEntry::findOrFail($id);

        $validated = $request->validate([
            'farm_id' => 'required|integer|exists:farms,id',
            'client_id' => 'required|integer|exists:clients,id',
            'crop_stage' => 'nullable|string|max:255',
            'crop_stage_progress' => 'nullable|integer|min:0|max:100',
            'estimated_harvest_date' => 'nullable|date',
            'inputs_used' => 'nullable|string|max:500',
            'observations' => 'nullable|string|max:1000',
            'weather_conditions' => 'nullable|string|max:255',
        ]);

        $entry->update([
            'farm_id' => $validated['farm_id'],
            'client_id' => $validated['client_id'],
            'crop_stage' => $validated['crop_stage'] ?? null,
            'crop_stage_progress' => $validated['crop_stage_progress'] ?? null,
            'estimated_harvest_date' => $validated['estimated_harvest_date'] ?? null,
            'inputs_used' => $validated['inputs_used'] ?? null,
            'observations' => $validated['observations'] ?? null,
            'weather_conditions' => $validated['weather_conditions'] ?? null,
        ]);

        return redirect()->route('admin.technitian.data.index')
            ->with('success', 'Saisie de données mise à jour avec succès.');
    }

    public function destroy($id)
    {
        DataEntry::findOrFail($id)->delete();

        return redirect()->route('admin.technitian.data.index')
            ->with('success', 'Saisie de données supprimée avec succès.');
    }
}

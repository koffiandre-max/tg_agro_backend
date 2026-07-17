<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    public function index()
    {
        return view('admin.farms.datatable');
    }

    public function create()
    {
        $clients = User::where('role', 'client')->get(['id', 'name']);
        return view('admin.farms.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'exists:users,id'],
            'location' => ['required', 'string', 'max:255'],
            'culture_type' => ['required', 'string', 'max:255'],
            'total_area_hectares' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive,fallow'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'crop_stage' => ['nullable', 'string', 'max:100'],
            'crop_stage_progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'expected_harvest_date' => ['nullable', 'date'],
            'last_visit_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        Farm::create([
            'name' => $validated['name'],
            'user_id' => $validated['user_id'],
            'location' => $validated['location'],
            'culture_type' => $validated['culture_type'],
            'total_area_hectares' => $validated['total_area_hectares'],
            'status' => $validated['status'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'crop_stage' => $validated['crop_stage'] ?? null,
            'crop_stage_progress' => $validated['crop_stage_progress'] ?? 0,
            'expected_harvest_date' => $validated['expected_harvest_date'] ?? null,
            'last_visit_date' => $validated['last_visit_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.farms.index')->with('success', 'Exploitation créée avec succès.');
    }

    public function show(Farm $farm)
    {
        $farm->load('user', 'photos', 'reports', 'clients.user');

        return view('admin.farms.show', compact('farm'));
    }

    public function edit($id)
    {
        return view('admin.farms.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // La logique de mise à jour est gérée par le composant Livewire FarmForm
        return redirect()->route('admin.farms.index');
    }

    public function destroy($id)
    {
        $farm = Farm::findOrFail($id);
        $farm->delete();

        return redirect()->route('admin.farms.index')
            ->with('success', 'Exploitation supprimée avec succès.');
    }
}

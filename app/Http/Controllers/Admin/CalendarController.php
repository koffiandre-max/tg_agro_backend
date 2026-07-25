<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\Mission;
use App\Models\Technician;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $missions = Mission::with(['technician.user', 'farm'])
            ->orderBy('scheduled_date', 'desc')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'title' => $m->title,
                'start' => $m->scheduled_date?->format('Y-m-d'),
                'status' => $m->status,
                'farm_id' => $m->farm_id,
                'technician_id' => $m->technician_id,
                'technician' => $m->technician?->user?->name ?? 'Technicien #' . $m->technician_id,
                'farm' => $m->farm?->name ?? '',
                'description' => $m->description,
            ]);

        $technicians = Technician::with('user')->get();
        $farms = Farm::all();

        return view('admin.calendar', compact('missions', 'technicians', 'farms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'technician_id' => ['required', 'exists:technicians,id'],
            'farm_id' => ['required', 'exists:farms,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'scheduled_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
        ], [
            'technician_id.required' => 'Veuillez sélectionner un technicien.',
            'farm_id.required' => 'Veuillez sélectionner une exploitation.',
            'title.required' => 'Le titre de la mission est requis.',
            'scheduled_date.required' => 'La date planifiée est requise.',
            'status.required' => 'Veuillez sélectionner un statut.',
        ]);

        $mission = Mission::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'La mission a été créée avec succès.',
                'mission' => $this->formatMission($mission),
            ]);
        }

        return redirect()->back()->with('success', 'La mission a été créée avec succès.');
    }

    public function update(Request $request, Mission $mission)
    {
        $validated = $request->validate([
            'technician_id' => ['required', 'exists:technicians,id'],
            'farm_id' => ['required', 'exists:farms,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'scheduled_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
        ], [
            'technician_id.required' => 'Veuillez sélectionner un technicien.',
            'farm_id.required' => 'Veuillez sélectionner une exploitation.',
            'title.required' => 'Le titre de la mission est requis.',
            'scheduled_date.required' => 'La date planifiée est requise.',
            'status.required' => 'Veuillez sélectionner un statut.',
        ]);

        $mission->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'La mission a été mise à jour avec succès.',
                'mission' => $this->formatMission($mission),
            ]);
        }

        return redirect()->back()->with('success', 'La mission a été mise à jour avec succès.');
    }

    public function destroy(Mission $mission)
    {
        $mission->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'La mission a été supprimée avec succès.',
            ]);
        }

        return redirect()->back()->with('success', 'La mission a été supprimée avec succès.');
    }

    private function formatMission(Mission $mission): array
    {
        $mission->load(['technician.user', 'farm']);
        
        return [
            'id' => $mission->id,
            'title' => $mission->title,
            'start' => $mission->scheduled_date?->format('Y-m-d'),
            'status' => $mission->status,
            'farm_id' => $mission->farm_id,
            'technician_id' => $mission->technician_id,
            'technician' => $mission->technician?->user?->name ?? 'Technicien #' . $mission->technician_id,
            'farm' => $mission->farm?->name ?? '',
            'description' => $mission->description,
        ];
    }
}
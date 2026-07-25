<?php

namespace App\Http\Controllers\Technitian;

use App\Http\Controllers\Controller;
use App\Http\Middleware\TechnicianMiddleware;
use App\Models\Mission;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(TechnicianMiddleware::class);
    }

    public function index()
    {
        return view('technitian.missions.index');
    }

    public function show($id)
    {
        return view('technitian.missions.show', compact('id'));
    }

    public function store(Request $request)
    {
        $technician = Technician::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'farm_id' => ['required', 'exists:farms,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'scheduled_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
        ], [
            'farm_id.required' => 'Veuillez sélectionner une exploitation.',
            'farm_id.exists' => 'L\'exploitation sélectionnée est invalide.',
            'title.required' => 'Le titre de la mission est requis.',
            'scheduled_date.required' => 'La date planifiée est requise.',
            'status.required' => 'Veuillez sélectionner un statut.',
        ]);

        $mission = Mission::create([
            'technician_id' => $technician->id,
            'farm_id' => $validated['farm_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'scheduled_date' => $validated['scheduled_date'],
            'status' => $validated['status'],
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'La mission a été créée avec succès.',
                'mission' => [
                    'id' => $mission->id,
                    'title' => $mission->title,
                    'start' => $mission->scheduled_date ? $mission->scheduled_date->format('Y-m-d') : null,
                    'status' => $mission->status,
                    'farm' => $mission->farm?->name ?? '',
                    'farm_id' => $mission->farm_id,
                    'technician' => $technician->user?->name ?? '',
                    'technician_id' => $technician->id,
                    'description' => $mission->description,
                ]
            ]);
        }

        return redirect()->back()->with('success', 'La mission a été créée avec succès.');
    }

    public function update(Request $request, $id)
    {
        $technician = \App\Models\Technician::where('user_id', Auth::id())->firstOrFail();
        $mission = Mission::where('id', $id)
            ->where('technician_id', $technician->id)
            ->firstOrFail();

        $validated = $request->validate([
            'farm_id' => ['required', 'exists:farms,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'scheduled_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
        ], [
            'farm_id.required' => 'Veuillez sélectionner une exploitation.',
            'farm_id.exists' => 'L\'exploitation sélectionnée est invalide.',
            'title.required' => 'Le titre de la mission est requis.',
            'scheduled_date.required' => 'La date planifiée est requise.',
            'status.required' => 'Veuillez sélectionner un statut.',
        ]);

        $mission->update([
            'farm_id' => $validated['farm_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'scheduled_date' => $validated['scheduled_date'],
            'status' => $validated['status'],
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'La mission a été mise à jour avec succès.',
                'mission' => [
                    'id' => $mission->id,
                    'title' => $mission->title,
                    'start' => $mission->scheduled_date ? $mission->scheduled_date->format('Y-m-d') : null,
                    'status' => $mission->status,
                    'farm' => $mission->farm?->name ?? '',
                    'farm_id' => $mission->farm_id,
                    'technician' => $technician->user?->name ?? '',
                    'technician_id' => $technician->id,
                    'description' => $mission->description,
                ]
            ]);
        }

        return redirect()->back()->with('success', 'La mission a été mise à jour avec succès.');
    }

    public function complete(Request $request, $id)
    {
        $technician = \App\Models\Technician::where('user_id', Auth::id())->firstOrFail();
        $mission = Mission::where('id', $id)
            ->where('technician_id', $technician->id)
            ->firstOrFail();

        $mission->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'La mission a été marquée comme terminée.',
            ]);
        }

        return redirect()->back()->with('success', 'La mission a été marquée comme terminée.');
    }

    public function destroy($id)
    {
        $technician = \App\Models\Technician::where('user_id', Auth::id())->firstOrFail();
        $mission = Mission::where('id', $id)
            ->where('technician_id', $technician->id)
            ->firstOrFail();

        $mission->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'La mission a été supprimée avec succès.',
            ]);
        }

        return redirect()->back()->with('success', 'La mission a été supprimée avec succès.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Data\TechnicianData;
use App\Data\TechnicianUpdateData;
use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\Technician;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Http\Request;

class TechnicianController extends Controller
{
    public function index()
    {
        return view('admin.technicians.datatable');
    }

    public function create()
    {
        return view('admin.technicians.create');
    }

    public function store(Request $request)
    {
        $data = TechnicianData::from($request);

        // Create the user
        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'password' => bcrypt($data->password),
            'role' => 'technician',
            'is_active' => true,
        ]);

        // Create the technician profile
        Technician::create([
            'user_id' => $user->id,
            'phone_secondary' => $data->phone_secondary,
            'location_base' => $data->location_base,
            'max_concurrent_missions' => $data->max_concurrent_missions,
            'is_available' => $data->is_available,
            'notes' => $data->notes,
        ]);

        return redirect()->route('admin.technicians.index')
            ->with('success', 'Le technicien a été créé avec succès.');
    }

    public function show($id)
    {
        $technician = Technician::with('user')->findOrFail($id);
        return view('admin.technicians.show', compact('technician'));
    }

    public function edit($id)
    {
        $technician = Technician::with('user')->findOrFail($id);
        return view('admin.technicians.edit', compact('technician'));
    }

    public function update(Request $request, $id)
    {
        $technician = Technician::with('user')->findOrFail($id);

        // Validate with Laravel Data
        $data = TechnicianUpdateData::from($request->all());

        // Validate email uniqueness manually (Laravel Data doesn't support dynamic ignore ID well)
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $technician->user->id,
        ]);

        // Update the user
        $user = $technician->user;
        $user->update([
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
        ]);

        // Update password if provided
        if (!empty($data->password)) {
            $user->update(['password' => bcrypt($data->password)]);
        }

        // Update the technician profile
        $technician->update([
            'phone_secondary' => $data->phone_secondary,
            'location_base' => $data->location_base,
            'max_concurrent_missions' => $data->max_concurrent_missions,
            'is_available' => $data->is_available,
            'notes' => $data->notes,
        ]);

        return redirect()->route('admin.technicians.index')
            ->with('success', 'Le technicien a été mis à jour avec succès.');
    }

    public function destroy($id)
    {
        // TODO: Implémenter la logique de suppression
        return redirect()->route('admin.technicians.index');
    }

    public function missions($id)
    {
        $technician = Technician::with('user')->findOrFail($id);
        return view('admin.technicians.missions', compact('technician'));
    }

    public function missionsJson($id)
    {
        $technician = Technician::with('user')->findOrFail($id);
        $missions = Mission::where('technician_id', $id)
            ->with('farm')
            ->with('technician.user')
            ->get()
            ->map(function ($mission) {
                return [
                    'id' => $mission->id,
                    'zone' => $mission->farm?->name ?? 'Zone',
                    'technicien' => $mission->technician?->user?->name ?? 'Technicien',
                    'date' => $mission->scheduled_date ? $mission->scheduled_date->format('Y-m-d') : '',
                    'heure' => $mission->scheduled_date ? $mission->scheduled_date->format('H:i') : '00:00',
                    'vecteur' => 'rats',
                    'traitement' => $mission->title ?? 'Traitement',
                    'statut' => $mission->status ?? 'pending',
                ];
            });

        return response()->json(['missions' => $missions]);
    }

    public function storeMission(Request $request, $id)
    {
        $technician = Technician::with('user')->findOrFail($id);

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

    public function updateMission(Request $request, $id, $mission)
    {
        $technician = Technician::with('user')->findOrFail($id);
        $mission = Mission::where('id', $mission)
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

    public function destroyMission(Request $request, $id, $mission)
    {
        $technician = Technician::with('user')->findOrFail($id);
        $mission = Mission::where('id', $mission)
            ->where('technician_id', $technician->id)
            ->firstOrFail();

        $mission->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'La mission a été supprimée avec succès.',
            ]);
        }

        return redirect()->back()->with('success', 'La mission a été supprimée avec succès.');
    }

    public function reports($id)
    {
        $technician = Technician::with('user')->findOrFail($id);
        return view('admin.technicians.reports', compact('technician'));
    }

    public function calendar()
    {
        $technicians = Technician::with('user')->get();
        $farms = Farm::all();

        $missions = Mission::with('technician.user', 'farm')
            ->get()
            ->map(function ($mission) {
                return [
                    'id' => $mission->id,
                    'title' => $mission->title,
                    'start' => $mission->scheduled_date ? $mission->scheduled_date->format('Y-m-d') : null,
                    'status' => $mission->status,
                    'farm' => $mission->farm?->name ?? '',
                    'farm_id' => $mission->farm_id,
                    'technician' => $mission->technician?->user?->name ?? '',
                    'technician_id' => $mission->technician_id,
                    'description' => $mission->description,
                ];
            });

        return view('admin.technicians.calendar', compact('technicians', 'farms', 'missions'));
    }
}

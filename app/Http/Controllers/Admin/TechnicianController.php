<?php

namespace App\Http\Controllers\Admin;

use App\Data\TechnicianData;
use App\Data\TechnicianUpdateData;
use App\Http\Controllers\Controller;
use App\Models\Technician;
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
        $user = \App\Models\User::create([
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'password' => bcrypt($data->password),
            'role' => 'technician',
            'is_active' => true,
        ]);

        // Create the technician profile
        \App\Models\Technician::create([
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

    public function reports($id)
    {
        $technician = Technician::with('user')->findOrFail($id);
        return view('admin.technicians.reports', compact('technician'));
    }
}

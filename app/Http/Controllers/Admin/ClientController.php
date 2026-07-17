<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index()
    {
        return view('admin.clients.datatable');
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8'],
            'country_of_residence' => ['nullable', 'string', 'max:255'],
            'country_of_origin' => ['nullable', 'string', 'max:255'],
            'city_of_residence' => ['nullable', 'string', 'max:255'],
            'subscription_type' => ['nullable', 'string', 'in:basic,standard,premium'],
            'subscription_expires_at' => ['nullable', 'date'],
            'total_investment' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'client',
            'is_active' => true,
        ]);

        Client::create([
            'user_id' => $user->id,
            'country_of_residence' => $validated['country_of_residence'] ?? null,
            'country_of_origin' => $validated['country_of_origin'] ?? null,
            'city_of_residence' => $validated['city_of_residence'] ?? null,
            'subscription_type' => $validated['subscription_type'] ?? 'basic',
            'subscription_expires_at' => $validated['subscription_expires_at'] ?? null,
            'total_investment' => $validated['total_investment'] ?? 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.clients.index')->with('success', 'Client créé avec succès.');
    }

    public function show(Client $client)
    {
        $client->load('user', 'farms', 'reports', 'photos', 'subscriptions');

        return view('admin.clients.show', compact('client'));
    }

    public function farms(Client $client)
    {
        $client->load(['farms.user', 'farms.photos']);

        return view('admin.clients.farms', compact('client'));
    }

    public function edit(Client $client)
    {
        $client->load('user');

        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $client->user->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'country_of_residence' => ['nullable', 'string', 'max:255'],
            'country_of_origin' => ['nullable', 'string', 'max:255'],
            'city_of_residence' => ['nullable', 'string', 'max:255'],
            'subscription_type' => ['nullable', 'string', 'in:basic,standard,premium'],
            'subscription_expires_at' => ['nullable', 'date'],
            'total_investment' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $client->user->update($userData);

        $client->update([
            'country_of_residence' => $validated['country_of_residence'],
            'country_of_origin' => $validated['country_of_origin'],
            'city_of_residence' => $validated['city_of_residence'],
            'subscription_type' => $validated['subscription_type'],
            'subscription_expires_at' => $validated['subscription_expires_at'] ?: null,
            'total_investment' => $validated['total_investment'] ?: 0,
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('admin.clients.show', $client)->with('success', 'Client mis à jour avec succès.');
    }

    public function destroy($id)
    {
        // TODO: Implémenter la logique de suppression
        return redirect()->route('admin.clients.index');
    }
}

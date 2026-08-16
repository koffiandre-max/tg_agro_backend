@extends('layouts.app')

@section('page-title', 'Modifier l\'Utilisateur')

@section('content')
@php
    $colors = ['#6366f1','#8b5cf6','#ec4899','#14b8a6','#f59e0b','#10b981','#3b82f6','#f97316'];
    $profileColor = $colors[abs(crc32($user->name ?? 'U')) % count($colors)];
@endphp


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Modifier l'utilisateurs</h1>
        <p class="mt-2 text-sm text-gray-600">Gérez les comptes utilisateurs et leurs rôles.</p>
    </div>
    <div class="flex gap-3">
            <x-ui.btn variant="ghost" href="{{ route('admin.users.index') }}" icon="arrow-left">
        Annuler
    </x-ui.btn>
    </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2">
        <x-ui.card>
            <x-slot:header>
                <h3 class="text-base font-semibold text-gray-900">Informations utilisateur</h3>
            </x-slot:header>
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nom complet <span class="text-red-600">*</span></label>
                        <input type="text" id="name" name="name" required value="{{ old('name', $user->name) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Adresse email <span class="text-red-600">*</span></label>
                        <input type="email" id="email" name="email" required value="{{ old('email', $user->email) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1.5">Rôle <span class="text-red-600">*</span></label>
                        <select id="role" name="role" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Sélectionner un rôle</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="technician" {{ old('role', $user->role) === 'technician' ? 'selected' : '' }}>Technicien</option>
                            <option value="client" {{ old('role', $user->role) === 'client' ? 'selected' : '' }}>Client</option>
                        </select>
                        @error('role')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe <span class="text-red-600">*</span></label>
                        <input type="password" id="password" name="password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Laisser vide pour ne pas changer">
                        @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <x-ui.btn variant="secondary" href="{{ route('admin.users.index') }}">Annuler</x-ui.btn>
                    <x-ui.btn type="submit" variant="primary" icon="edit">Mettre à jour</x-ui.btn>
                </div>
            </form>
        </x-ui.card>
    </div>

    <div class="space-y-6">
        <x-ui.card>
            <x-slot:header>
                <h3 class="text-base font-semibold text-gray-900">Aperçu</h3>
            </x-slot:header>
            <div class="text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl text-2xl font-bold text-white shadow-sm" style="background:{{ $profileColor }}">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </div>
                <p class="mt-3 text-sm font-medium text-gray-500">Avatar de l'utilisateur</p>
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>
                <h3 class="text-base font-semibold text-gray-900">Informations</h3>
            </x-slot:header>
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-gray-500">Rôle actuel</dt>
                    <dd class="font-medium text-gray-900 capitalize">{{ $user->role ?? '—' }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-gray-500">Statut</dt>
                    <dd class="font-medium {{ $user->is_active ? 'text-green-600' : 'text-gray-500' }}">{{ $user->is_active ? 'Actif' : 'Inactif' }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-gray-500">Inscrit le</dt>
                    <dd class="font-medium text-gray-900">{{ $user->created_at?->format('d/m/Y') ?? '—' }}</dd>
                </div>
            </dl>
        </x-ui.card>
    </div>
    </div>
</div>
@endsection

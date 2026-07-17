@extends('layouts.app')

@section('page-title', 'Nouvel Utilisateur')

@section('content')
@php
    $colors = ['#6366f1','#8b5cf6','#ec4899','#14b8a6','#f59e0b','#10b981','#3b82f6','#f97316'];
    $profileColor = $colors[abs(crc32(old('name', 'U'))) % count($colors)];
@endphp

<x-ui.page-header
    title="Nouvel Utilisateur"
    subtitle="Créez un nouveau compte utilisateur et assignez-lui un rôle."
    :breadcrumbs="[['label' => 'Tableau de bord', 'route' => 'dashboard'], ['label' => 'Utilisateurs', 'route' => 'admin.users.index'], ['label' => 'Nouveau']]"
>
    <x-slot:actions>
        <x-ui.btn variant="secondary" href="{{ route('admin.users.index') }}" icon="arrow-left">
            Annuler
        </x-ui.btn>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2">
        <x-ui.card>
            <x-slot:header>
                <h3 class="text-base font-semibold text-gray-900">Identifiants de connexion</h3>
            </x-slot:header>
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nom complet <span class="text-red-600">*</span></label>
                        <input type="text" id="name" name="name" required value="{{ old('name') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Ex: Kouassi Jean">
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Adresse email <span class="text-red-600">*</span></label>
                        <input type="email" id="email" name="email" required value="{{ old('email') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="jean.kouassi@exemple.com">
                        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="+225 01 00 00 00 00">
                        @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1.5">Rôle <span class="text-red-600">*</span></label>
                        <select id="role" name="role" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Sélectionner un rôle</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="technician" {{ old('role') === 'technician' ? 'selected' : '' }}>Technicien</option>
                            <option value="client" {{ old('role') === 'client' ? 'selected' : '' }}>Client</option>
                        </select>
                        @error('role')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe <span class="text-red-600">*</span></label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="••••••••">
                        @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <x-ui.btn variant="secondary" href="{{ route('admin.users.index') }}">Annuler</x-ui.btn>
                    <x-ui.btn type="submit" variant="primary" icon="plus">Créer l'utilisateur</x-ui.btn>
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
                    {{ strtoupper(substr(old('name', 'U'), 0, 1)) }}
                </div>
                <p class="mt-3 text-sm font-medium text-gray-500">Aperçu de l'avatar</p>
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>
                <h3 class="text-base font-semibold text-gray-900">Informations</h3>
            </x-slot:header>
            <div class="space-y-3 text-sm text-gray-600">
                <p>L'utilisateur recevra un email de bienvenue avec ses identifiants.</p>
                <p>Vous pourrez modifier son rôle et ses informations à tout moment.</p>
            </div>
        </x-ui.card>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('page-title', 'Détails de l\'Utilisateur')

@section('content')
@php
    $colors = ['#6366f1','#8b5cf6','#ec4899','#14b8a6','#f59e0b','#10b981','#3b82f6','#f97316'];
    $profileColor = $colors[abs(crc32($user->name ?? 'U')) % count($colors)];
@endphp

<x-ui.page-header
    title="Détails de l'Utilisateur"
    subtitle="Informations et activités de {{ $user->name }}"
    :breadcrumbs="[['label' => 'Tableau de bord', 'route' => 'dashboard'], ['label' => 'Utilisateurs', 'route' => 'admin.users.index'], ['label' => 'Détails']]"
>
    <x-slot:actions>
        <x-ui.btn variant="secondary" href="{{ route('admin.users.edit', $user->id) }}" icon="edit">
            Modifier
        </x-ui.btn>
        <x-ui.btn variant="ghost" href="{{ route('admin.users.index') }}" icon="arrow-left">
            Retour
        </x-ui.btn>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 space-y-6">
        <x-ui.card>
            <div class="text-center">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-2xl text-2xl font-bold text-white shadow-sm" style="background:{{ $profileColor }}">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </div>
                <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $user->name ?? 'Utilisateur' }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $user->email ?? '' }}</p>
                <div class="mt-3 flex justify-center gap-2">
                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                        {{ ucfirst($user->role ?? 'Utilisateur') }}
                    </span>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold" style="{{ $user->is_active ? 'background:#ecfdf5;color:#047857' : 'background:#f3f4f6;color:#6b7280' }}">
                        {{ $user->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
            </div>

            <div class="mt-5 grid grid-cols-2 gap-3 border-t border-gray-100 pt-5">
                <div class="rounded-xl bg-gray-50 p-3">
                    <p class="text-xs font-medium text-gray-500">Téléphone</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900 truncate">{{ $user->phone ?? '—' }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3">
                    <p class="text-xs font-medium text-gray-500">Rôle</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900 capitalize">{{ $user->role ?? '—' }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3">
                    <p class="text-xs font-medium text-gray-500">Date d'inscription</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $user->created_at?->format('d/m/Y') ?? '—' }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3">
                    <p class="text-xs font-medium text-gray-500">Dernière mise à jour</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $user->updated_at?->format('d/m/Y H:i') ?? '—' }}</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    <div class="space-y-6">
        <x-ui.card>
            <x-slot:header>
                <h3 class="text-base font-semibold text-gray-900">Activité récente</h3>
            </x-slot:header>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Connexions</p>
                        <p class="text-xs text-gray-500">Dernière activité</p>
                    </div>
                    <span class="text-xs text-gray-400">{{ $user->updated_at?->diffForHumans() ?? '—' }}</span>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>
                <h3 class="text-base font-semibold text-gray-900">Actions</h3>
            </x-slot:header>
            <div class="space-y-2">
                <x-ui.btn variant="secondary" href="{{ route('admin.users.edit', $user->id) }}" icon="edit" class="w-full">
                    Modifier l'utilisateur
                </x-ui.btn>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr ?')">
                    @csrf
                    @method('DELETE')
                    <x-ui.btn type="submit" variant="danger" icon="trash" class="w-full">
                        Supprimer
                    </x-ui.btn>
                </form>
            </div>
        </x-ui.card>
    </div>
</div>
@endsection

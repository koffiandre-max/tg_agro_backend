@extends('layouts.app')

@section('page-title', 'Détails du Rôle')

@section('content')
@php
    $colors = ['#6366f1','#8b5cf6','#ec4899','#14b8a6','#f59e0b','#10b981','#3b82f6','#f97316'];
    $roleColor = $colors[abs(crc32($role->name ?? 'R')) % count($colors)];
@endphp

<x-ui.page-header
    title="Détails du Rôle"
    subtitle="Informations et permissions du rôle {{ $role->name }}"
    :breadcrumbs="[['label' => 'Tableau de bord', 'route' => 'dashboard'], ['label' => 'Rôles et Permissions', 'route' => 'admin.roles.index'], ['label' => 'Détails']]"
>
    <x-slot:actions>
        <x-ui.btn variant="secondary" href="{{ route('admin.roles.edit', $role) }}" icon="edit">
            Modifier
        </x-ui.btn>
        <x-ui.btn variant="ghost" href="{{ route('admin.roles.index') }}" icon="arrow-left">
            Retour
        </x-ui.btn>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 space-y-6">
        <x-ui.card>
            <div class="text-center">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-2xl text-2xl font-bold text-white shadow-sm" style="background:{{ $roleColor }}">
                    {{ strtoupper(substr($role->name ?? 'R', 0, 1)) }}
                </div>
                <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $role->name }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $role->slug }}</p>
                @if($role->description)
                    <p class="mt-2 text-sm text-gray-600 bg-gray-50 rounded-lg p-3 inline-block">{{ $role->description }}</p>
                @endif
            </div>

            <div class="mt-5 grid grid-cols-2 gap-3 border-t border-gray-100 pt-5">
                <div class="rounded-xl bg-gray-50 p-3">
                    <p class="text-xs font-medium text-gray-500">Nombre de permissions</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $role->permissions->count() }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3">
                    <p class="text-xs font-medium text-gray-500">Créé le</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $role->created_at?->format('d/m/Y') ?? '—' }}</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    <div class="space-y-6">
        <x-ui.card>
            <x-slot:header>
                <h3 class="text-base font-semibold text-gray-900">Actions</h3>
            </x-slot:header>
            <div class="space-y-2">
                <x-ui.btn variant="secondary" href="{{ route('admin.roles.edit', $role) }}" icon="edit" class="w-full">
                    Modifier le rôle
                </x-ui.btn>
                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr ?')">
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

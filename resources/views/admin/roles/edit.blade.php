@extends('layouts.app')

@section('page-title', 'Modifier le Rôle')

@section('content')
@php
    $colors = ['#6366f1','#8b5cf6','#ec4899','#14b8a6','#f59e0b','#10b981','#3b82f6','#f97316'];
    $roleColor = $colors[abs(crc32($role->name ?? 'R')) % count($colors)];
@endphp

<x-ui.page-header
    title="Modifier le Rôle"
    subtitle="Modifiez les informations de {{ $role->name }}"
    :breadcrumbs="[['label' => 'Tableau de bord', 'route' => 'dashboard'], ['label' => 'Rôles et Permissions', 'route' => 'admin.roles.index'], ['label' => 'Modifier']]"
>
    <x-slot:actions>
        <x-ui.btn variant="ghost" href="{{ route('admin.roles.index') }}" icon="arrow-left">
            Annuler
        </x-ui.btn>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.card>
    <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nom du rôle <span class="text-red-600">*</span></label>
                <input type="text" id="name" name="name" required value="{{ old('name', $role->name) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1.5">Slug <span class="text-red-600">*</span></label>
                <input type="text" id="slug" name="slug" required value="{{ old('slug', $role->slug) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-y"
                          placeholder="Description du rôle...">{{ old('description', $role->description) }}</textarea>
                @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <x-ui.btn variant="secondary" href="{{ route('admin.roles.index') }}">Annuler</x-ui.btn>
            <x-ui.btn type="submit" variant="primary" icon="edit">Mettre à jour</x-ui.btn>
        </div>
    </form>
</x-ui.card>
@endsection

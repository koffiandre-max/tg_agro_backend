@extends('layouts.app')

@section('page-title', 'Nouvelle Permission')

@section('content')
<x-ui.page-header
    title="Nouvelle Permission"
    subtitle="Créez une nouvelle permission."
    :breadcrumbs="[['label' => 'Tableau de bord', 'route' => 'dashboard'], ['label' => 'Rôles et Permissions', 'route' => 'admin.permissions.index'], ['label' => 'Nouvelle']]"
>
    <x-slot:actions>
        <x-ui.btn variant="secondary" href="{{ route('admin.permissions.index') }}" icon="arrow-left">
            Annuler
        </x-ui.btn>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.card>
    <form action="{{ route('admin.permissions.store') }}" method="POST" class="space-y-5">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nom de la permission <span class="text-red-600">*</span></label>
                <input type="text" id="name" name="name" required value="{{ old('name') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1.5">Slug <span class="text-red-600">*</span></label>
                <input type="text" id="slug" name="slug" required value="{{ old('slug') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-y"
                          placeholder="Description de la permission...">{{ old('description') }}</textarea>
                @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <x-ui.btn variant="secondary" href="{{ route('admin.permissions.index') }}">Annuler</x-ui.btn>
            <x-ui.btn type="submit" variant="primary" icon="plus">Créer la permission</x-ui.btn>
        </div>
    </form>
</x-ui.card>
@endsection

@extends('layouts.app')

@section('page-title', 'Permissions')

@section('content')
<x-ui.page-header
    title="Permissions"
    subtitle="Gérez les permissions du système."
    :breadcrumbs="[['label' => 'Tableau de bord', 'route' => 'dashboard'], ['label' => 'Rôles et Permissions', 'route' => 'admin.permissions.index'], ['label' => 'Permissions']]"
>
    <x-slot:actions>
        <x-ui.btn href="{{ route('admin.permissions.create') }}" icon="plus">
            Nouvelle permission
        </x-ui.btn>
    </x-slot:actions>
</x-ui.page-header>

<livewire:permissions-table />
@endsection

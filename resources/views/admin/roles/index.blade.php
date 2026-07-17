@extends('layouts.app')

@section('page-title', 'Rôles')

@section('content')
<x-ui.page-header
    title="Rôles"
    subtitle="Gérez les rôles utilisateurs et leurs permissions associées."
    :breadcrumbs="[['label' => 'Tableau de bord', 'route' => 'dashboard'], ['label' => 'Rôles et Permissions', 'route' => 'admin.roles.index'], ['label' => 'Rôles']]"
>
    <x-slot:actions>
        <x-ui.btn href="{{ route('admin.roles.create') }}" icon="plus">
            Nouveau rôle
        </x-ui.btn>
    </x-slot:actions>
</x-ui.page-header>

<livewire:roles-table />
@endsection

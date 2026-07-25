@extends('layouts.app')

@section('page-title', 'Mes Missions')

@section('content')
<div class="space-y-6 p-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Mes Missions</h1>
    </div>

    @livewire('technician-missions-table')
</div>
@endsection

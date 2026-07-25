@extends('layouts.app')

@section('title', 'Mes Clients')

@section('content')
<div class="space-y-6 p-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Mes Clients</h1>
    </div>

    @livewire('technician-clients-table')
</div>
@endsection

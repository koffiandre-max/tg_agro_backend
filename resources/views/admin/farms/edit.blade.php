@extends('layouts.app')

@section('page-title', 'Modifier l\'Exploitation')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        {{-- Composant Livewire --}}
        @livewire('farm-form', ['farmId' => $id])
    </div>
</div>
@endsection

@extends('layouts.app')

@section('page-title', 'Modifier le Rapport de Visite')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @include('livewire.rapport-visite-form', [
            'isEdit' => true,
            'rapport' => $rapport,
            'techniciens' => $techniciens,
            'clients' => $clients,
            'farms' => $farms,
            'selectedFarmId' => $selectedFarmId ?? ($rapport->farm_id ?? null),
            'typeActiviteOptions' => $typeActiviteOptions ?? collect(\App\Enums\TypeActivite::cases())->map(fn($case) => ['value' => $case->value, 'label' => $case->label()])->toArray(),
        ])
    </div>
</div>
@endsection

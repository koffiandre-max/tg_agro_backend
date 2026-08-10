@extends('layouts.app')

@section('title', 'Mon Espace Client')
@section('page-title', 'Mon Espace')

@section('content')
<div class="space-y-6 p-6">
    {{-- Welcome Card --}}
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold mb-2">Bienvenue, {{ auth()->user()->name }} !</h2>
        <p class="text-indigo-100">Voici le résumé de vos exploitations agricoles</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Mes Exploitations</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $farms->count() }}</p>
                </div>
                <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.188-1.066A2.25 2.25 0 012.25 17.5v-11.5a2.25 2.25 0 012.25-2.25h15a2.25 2.25 0 012.25 2.25v11.5a2.25 2.25 0 01-2.25 2.25L9 18.75v-8.25z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Rapports Disponibles</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">8</p>
                </div>
                <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Photos</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">24</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Messages Non Lus</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">2</p>
                </div>
                <div class="h-12 w-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Mes Exploitations --}}
    <div class="bg-white rounded-xl p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Mes Exploitations</h2>
            <a href="{{ route('admin.portail.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">Voir tout</a>
        </div>

        @if($farms->isEmpty())
            <p class="text-sm text-gray-500">Vous n'avez aucune exploitation enregistrée pour le moment.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($farms as $farm)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-800">{{ $farm->name }}</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ $farm->location ?? '—' }}</p>
                            </div>
                            <span class="px-2 py-1 {{ $farm->statusBadgeClasses() }} text-xs rounded-full">{{ $farm->statusLabel() }}</span>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Superficie:</span>
                                <span class="font-medium text-gray-800">{{ $farm->total_area_hectares ? $farm->total_area_hectares . ' ha' : '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Culture:</span>
                                <span class="font-medium text-gray-800">{{ $farm->culture_type ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Stade:</span>
                                <span class="font-medium text-gray-800">{{ $farm->crop_stage ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $farm->crop_stage_progress ?? 0 }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Progression: {{ $farm->crop_stage_progress ?? 0 }}%</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Derniers Rapports --}}
    <div class="bg-white rounded-xl p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Derniers Rapports</h2>
            <a href="{{ route('admin.portail.reports') }}" class="text-sm text-indigo-600 hover:text-indigo-700">Voir tout</a>
        </div>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Rapport Mensuel - Janvier 2026</p>
                        <p class="text-xs text-gray-500">Ferme de Yamoussoukro</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Validé</span>
                    <button class="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Rapport d'Analyse de Sol</p>
                        <p class="text-xs text-gray-500">Ferme de Bouaké</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Validé</span>
                    <button class="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
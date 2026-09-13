@extends('layouts.app')

@section('title', 'Ajouter des photos')
@section('page-title', 'Ajouter des photos à la galerie')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <form method="POST" action="{{ route('admin.technitian.photos.store') }}" enctype="multipart/form-data" id="photoForm">
        @csrf

        @php
            // Options pour les composants x-select
            $clientOptions = $clients->mapWithKeys(fn ($client) => [$client->id => $client->code ?? ('Client #'.$client->id)])->all();
            $farmOptions = $farms->mapWithKeys(fn ($farm) => [$farm->id => trim($farm->name . ($farm->location ? ' — '.$farm->location : ''))])->all();
        @endphp

        {{-- Client --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Client
            </h3>
            <x-select name="client_id" id="client_id" :options="$clientOptions"
                      placeholder="Sélectionner un client" error="client_id" />
            @error('client_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Plantation / Ferme --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
                Plantation / Exploitation
            </h3>
            <x-select name="farm_id" id="farm_id" :options="$farmOptions"
                      placeholder="Sélectionner une exploitation" error="farm_id" />
            @error('farm_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Photos --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/>
                </svg>
                Photos
            </h3>

            <div class="flex items-center justify-center w-full">
                <label for="photos" class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-indigo-400 transition-colors">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z"/>
                        </svg>
                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Cliquez pour choisir</span> ou glissez-déposez</p>
                        <p class="text-xs text-gray-400">PNG, JPG, JPEG (max. 10 Mo par photo)</p>
                    </div>
                    <input id="photos" name="photos[]" type="file" accept="image/*" multiple class="hidden" onchange="previewPhotos(event)" />
                </label>
            </div>
            @error('photos')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('photos.*')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            {{-- Prévisualisation --}}
            <div id="previewContainer" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"></div>
        </div>

        {{-- Coordonnées GPS --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                    Coordonnées géographiques
                </h3>
                <button type="button" id="getLocationBtn" onclick="getLocation()" class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-4 py-2 text-xs font-medium text-blue-700 hover:bg-blue-100 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Obtenir ma position
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Latitude</label>
                    <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}" readonly
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Cliquez sur 'Obtenir ma position'" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Longitude</label>
                    <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}" readonly
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Cliquez sur 'Obtenir ma position'" />
                </div>
            </div>
            <p id="locationStatus" class="mt-2 text-xs text-gray-400"></p>
        </div>

        {{-- Légende commune --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                </svg>
                Légende (optionnelle)
            </h3>
            <textarea name="caption" rows="3" class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ajouter une description pour ces photos...">{{ old('caption') }}</textarea>
            @error('caption')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Bouton de soumission --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.technitian.index') }}" class="rounded-full border border-gray-200 bg-white px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Annuler
            </a>
            <button type="submit" class="rounded-full bg-indigo-600 px-6 py-3 text-sm font-medium text-white hover:bg-indigo-700 transition-colors shadow-sm">
                Enregistrer les photos
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Prévisualisation des photos
    function previewPhotos(event) {
        const container = document.getElementById('previewContainer');
        container.innerHTML = '';
        const files = event.target.files;
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative group rounded-xl overflow-hidden border border-gray-200 aspect-square';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <span class="text-white text-xs font-medium px-2 py-1 bg-black/60 rounded-lg">${file.name}</span>
                    </div>
                `;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    }

    // Géolocalisation
    function getLocation() {
        const status = document.getElementById('locationStatus');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const btn = document.getElementById('getLocationBtn');

        if (!navigator.geolocation) {
            status.textContent = 'La géolocalisation n\'est pas supportée par votre navigateur.';
            status.className = 'mt-2 text-xs text-red-500';
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Localisation...';
        status.textContent = 'Recherche de votre position...';
        status.className = 'mt-2 text-xs text-blue-500';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                latInput.value = position.coords.latitude;
                lngInput.value = position.coords.longitude;
                status.textContent = `Position obtenue : ${position.coords.latitude}, ${position.coords.longitude}`;
                status.className = 'mt-2 text-xs text-emerald-500';
                btn.disabled = false;
                btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Obtenir ma position';
            },
            function(error) {
                let msg = 'Erreur de géolocalisation.';
                if (error.code === 1) msg = 'Vous avez refusé la géolocalisation.';
                else if (error.code === 2) msg = 'Position indisponible.';
                else if (error.code === 3) msg = 'Délai de recherche dépassé.';
                status.textContent = msg;
                status.className = 'mt-2 text-xs text-red-500';
                btn.disabled = false;
                btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Obtenir ma position';
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }
</script>
@endpush
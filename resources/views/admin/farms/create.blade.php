@extends('layouts.app')

@section('page-title', 'Nouvelle Exploitation')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('admin.farms.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour à la liste
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Nouvelle Exploitation</h1>
            <p class="mt-1 text-gray-500">Créez une nouvelle exploitation agricole.</p>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.farms.store') }}" method="POST" class="bg-white border border-gray-200 rounded-lg shadow-sm">
            @csrf

            {{-- Section 1 : Informations générales --}}
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Informations générales</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nom de l'exploitation <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Ferme Kouassi">
                        @error('name') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    @php
    $users = [
        ['value' => '1', 'label' => 'Sarah Connor', 'image' => 'https://i.pravatar.cc/100?img=1', 'description' => 'Développeuse Frontend', 'badge' => 'Admin'],
        ['value' => '2', 'label' => 'John Doe', 'image' => 'https://i.pravatar.cc/100?img=2', 'description' => 'Designer UX', 'badge' => 'Membre'],
        ['value' => '3', 'label' => 'Alex Smith', 'image' => 'https://i.pravatar.cc/100?img=3', 'description' => 'Chef de projet', 'badge' => 'Membre'],
    ];
@endphp
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Propriétaire <span class="text-red-600">*</span>
                        </label>
                        <x-select 
                            name="user_id" 
                            label="Assigner à" 
                            :options="$clients->map(fn($c) => [ 'value' => $c->id, 'label' => $c->name])" 
                            placeholder="Choisir un utilisateur..." 
                            error="user_id"
                        />
                        {{-- <select id="user_id" name="user_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">Sélectionner un client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('user_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                            @endforeach
                        </select> --}}
                        @error('user_id') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Localisation <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="location" name="location" value="{{ old('location') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Abidjan, Côte d'Ivoire">
                        @error('location') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="culture_type" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Type de culture <span class="text-red-600">*</span>
                        </label>
                        <x-select 
                                name="culture_type" 
                                label="Type de culture" 
                                :options="$cultureTypeOptions" 
                                placeholder="Sélectionner" 
                                error="culture_type"
                        />
                        {{-- <select id="culture_type" name="culture_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">Sélectionner</option>
                            @foreach($cultureTypeOptions as $option)
                                <option value="{{ $option['value'] }}" {{ old('culture_type') === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                            @endforeach
                        </select> --}}
                        @error('culture_type') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="total_area_hectares" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Surface (ha) <span class="text-red-600">*</span>
                        </label>
                        <input type="number" step="0.01" min="0" id="total_area_hectares" name="total_area_hectares" value="{{ old('total_area_hectares') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="0.00">
                        @error('total_area_hectares') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Statut <span class="text-red-600">*</span>
                        </label>
                        <x-select 
                                name="status" 
                                label="Statut" 
                                :options="$farmStatusOptions" 
                                placeholder="Sélectionner" 
                                error="status"
                        />
                        @error('status') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 2 : Coordonnées GPS --}}
            <div class="px-6 py-5 border-b border-gray-100 border-t border-gray-100 bg-gray-100/50">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Coordonnées GPS</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1.5">Latitude</label>
                        <input type="number" step="0.0000001" id="latitude" name="latitude" value="{{ old('latitude') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="5.3361">
                        @error('latitude') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1.5">Longitude</label>
                        <input type="number" step="0.0000001" id="longitude" name="longitude" value="{{ old('longitude') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="-4.0202">
                        @error('longitude') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 3 : Culture --}}
            <div class="px-6 py-5 border-b border-gray-100 border-t border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Suivi de la culture</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="crop_stage" class="block text-sm font-medium text-gray-700 mb-1.5">Stade de la culture</label>
                        <x-select 
                                name="crop_stage" 
                                label="Stade de la culture" 
                                :options="$stadePhenologiqueOptions" 
                                placeholder="Sélectionner" 
                                error="crop_stage"
                        />
                        @error('crop_stage') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="crop_stage_progress" class="block text-sm font-medium text-gray-700 mb-1.5">Progression (%)</label>
                        <input type="number" min="0" max="100" id="crop_stage_progress" name="crop_stage_progress" value="{{ old('crop_stage_progress', 0) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="0">
                        @error('crop_stage_progress') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="expected_harvest_date" class="block text-sm font-medium text-gray-700 mb-1.5">Date de récolte prévue</label>
                        <input type="date" id="expected_harvest_date" name="expected_harvest_date" value="{{ old('expected_harvest_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        @error('expected_harvest_date') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="last_visit_date" class="block text-sm font-medium text-gray-700 mb-1.5">Dernière visite</label>
                        <input type="date" id="last_visit_date" name="last_visit_date" value="{{ old('last_visit_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        @error('last_visit_date') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 4 : Notes --}}
            <div class="px-6 py-5 border-b border-gray-100 border-t border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Notes</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1.5">Notes supplémentaires</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                              placeholder="Informations complémentaires...">{{ old('notes') }}</textarea>
                    @error('notes') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Section 5 : Parcelles --}}
            <div class="px-6 py-5 border-b border-gray-100 border-t border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Parcelles</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="reference_dossier" class="block text-sm font-medium text-gray-700 mb-1.5">Référence dossier</label>
                        <input type="text" id="reference_dossier" name="reference_dossier" value="{{ old('reference_dossier') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: REF-001">
                        @error('reference_dossier') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="date_declaration" class="block text-sm font-medium text-gray-700 mb-1.5">Date de déclaration</label>
                        <input type="date" id="date_declaration" name="date_declaration" value="{{ old('date_declaration') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        @error('date_declaration') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="nom_client" class="block text-sm font-medium text-gray-700 mb-1.5">Nom client</label>
                        <input type="text" id="nom_client" name="nom_client" value="{{ old('nom_client') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Nom du client">
                        @error('nom_client') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="contact" class="block text-sm font-medium text-gray-700 mb-1.5">Contact</label>
                        <input type="text" id="contact" name="contact" value="{{ old('contact') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Téléphone ou email">
                        @error('contact') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="numero_cadastral" class="block text-sm font-medium text-gray-700 mb-1.5">Numéro cadastral</label>
                        <input type="text" id="numero_cadastral" name="numero_cadastral" value="{{ old('numero_cadastral') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: CAD-12345">
                        @error('numero_cadastral') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 6 : Caractéristiques générales --}}
            <div class="px-6 py-5 border-b border-gray-100 border-t border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Caractéristiques générales</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="surface_totale" class="block text-sm font-medium text-gray-700 mb-1.5">Surface totale (ha)</label>
                        <input type="number" step="0.01" min="0" id="surface_totale" name="surface_totale" value="{{ old('surface_totale') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="0.00">
                        @error('surface_totale') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="surface_cultivable" class="block text-sm font-medium text-gray-700 mb-1.5">Surface cultivable (ha)</label>
                        <input type="number" step="0.01" min="0" id="surface_cultivable" name="surface_cultivable" value="{{ old('surface_cultivable') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="0.00">
                        @error('surface_cultivable') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="forme_parcelle" class="block text-sm font-medium text-gray-700 mb-1.5">Forme de la parcelle</label>
                        <x-select 
                                name="forme_parcelle" 
                                label="Forme de la parcelle" 
                                :options="$formeParcelleOptions" 
                                placeholder="Sélectionner" 
                                error="forme_parcelle"
                        />
                        @error('forme_parcelle') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="exposition_principale" class="block text-sm font-medium text-gray-700 mb-1.5">Exposition principale</label>
                        <x-select 
                                name="exposition_principale" 
                                label="Exposition principale" 
                                :options="$expositionParcelleOptions" 
                                placeholder="Sélectionner" 
                                error="exposition_principale"
                        />
                        @error('exposition_principale') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="pente_moyenne" class="block text-sm font-medium text-gray-700 mb-1.5">Pente moyenne</label>
                        <x-select 
                                name="pente_moyenne" 
                                label="Pente moyenne" 
                                :options="$penteMoyenneOptions" 
                                placeholder="Sélectionner" 
                                error="pente_moyenne"
                        />
                        @error('pente_moyenne') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="altitude" class="block text-sm font-medium text-gray-700 mb-1.5">Altitude (m)</label>
                        <input type="number" id="altitude" name="altitude" value="{{ old('altitude') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="0">
                        @error('altitude') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="topographie" class="block text-sm font-medium text-gray-700 mb-1.5">Topographie</label>
                        <x-select 
                                name="topographie" 
                                label="Topographie" 
                                :options="$topographieOptions" 
                                placeholder="Sélectionner" 
                                error="topographie"
                        />
                        @error('topographie') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 7 : Sols --}}
            <div class="px-6 py-5 border-b border-gray-100 border-t border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Sols</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="type_sol" class="block text-sm font-medium text-gray-700 mb-1.5">Type de sol</label>
                        <x-select 
                                name="type_sol" 
                                label="Type de sol" 
                                :options="$typeSolOptions" 
                                placeholder="Sélectionner" 
                                error="type_sol"
                        />
                        @error('type_sol') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="couleur_sol" class="block text-sm font-medium text-gray-700 mb-1.5">Couleur du sol</label>
                        <x-select 
                                name="couleur_sol" 
                                label="Couleur du sol" 
                                :options="$couleurSolOptions" 
                                placeholder="Sélectionner" 
                                error="couleur_sol"
                        />
                        @error('couleur_sol') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="profondeur_sol" class="block text-sm font-medium text-gray-700 mb-1.5">Profondeur du sol</label>
                        <input type="text" id="profondeur_sol" name="profondeur_sol" value="{{ old('profondeur_sol') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: 30 cm">
                        @error('profondeur_sol') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Présence de cailloux</label>
                        <x-choice-card name="presence_cailloux" label="" :value="old('presence_cailloux')" />
                        @error('presence_cailloux') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="commentaire_cailloux" class="block text-sm font-medium text-gray-700 mb-1.5">Commentaire cailloux</label>
                        <textarea id="commentaire_cailloux" name="commentaire_cailloux" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails...">{{ old('commentaire_cailloux') }}</textarea>
                        @error('commentaire_cailloux') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="problemes_erosion" class="block text-sm font-medium text-gray-700 mb-1.5">Problèmes d'érosion</label>
                        <x-select 
                                name="problemes_erosion" 
                                label="Problèmes d'érosion" 
                                :options="[['value' => '1', 'label' => 'Oui'], ['value' => '0', 'label' => 'Non']]" 
                                placeholder="—" 
                                error="problemes_erosion"
                        />
                        @error('problemes_erosion') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="commentaire_erosion" class="block text-sm font-medium text-gray-700 mb-1.5">Commentaire érosion</label>
                        <textarea id="commentaire_erosion" name="commentaire_erosion" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails...">{{ old('commentaire_erosion') }}</textarea>
                        @error('commentaire_erosion') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Analyse de sol réalisée</label>
                        <div x-data="{ showPrecision: false }" class="space-y-2">
                            <div class="flex flex-wrap gap-3">
                                <label class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 cursor-pointer hover:bg-gray-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700 transition-colors">
                                    <input type="radio" name="analyse_sol_realisee" value="1" class="sr-only" {{ old('analyse_sol_realisee') === '1' ? 'checked' : '' }} @change="showPrecision = false">
                                    Oui
                                </label>
                                <label class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 cursor-pointer hover:bg-gray-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700 transition-colors">
                                    <input type="radio" name="analyse_sol_realisee" value="0" class="sr-only" {{ old('analyse_sol_realisee') === '0' ? 'checked' : '' }} @change="showPrecision = false">
                                    Non
                                </label>
                                <label class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 cursor-pointer hover:bg-gray-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700 transition-colors">
                                    <input type="radio" name="analyse_sol_realisee" value="" class="sr-only" {{ old('analyse_sol_realisee', '') === '' ? 'checked' : '' }} @change="showPrecision = true">
                                    À préciser
                                </label>
                            </div>
                            <div x-show="showPrecision" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="overflow-hidden">
                                <input type="text" name="analyse_sol_realisee_precision" value="{{ old('analyse_sol_realisee_precision') }}" placeholder="Précisez..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            </div>
                        </div>
                        @error('analyse_sol_realisee') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="commentaire_analyse" class="block text-sm font-medium text-gray-700 mb-1.5">Commentaire analyse</label>
                        <textarea id="commentaire_analyse" name="commentaire_analyse" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails...">{{ old('commentaire_analyse') }}</textarea>
                        @error('commentaire_analyse') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="ph" class="block text-sm font-medium text-gray-700 mb-1.5">pH</label>
                        <input type="number" step="0.01" min="0" max="14" id="ph" name="ph" value="{{ old('ph') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="0.00">
                        @error('ph') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="source_ph" class="block text-sm font-medium text-gray-700 mb-1.5">Source pH</label>
                        <input type="text" id="source_ph" name="source_ph" value="{{ old('source_ph') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Laboratoire, Test kit...">
                        @error('source_ph') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 8 : Ressources en eau --}}
            <div class="px-6 py-5 border-b border-gray-100 border-t border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Ressources en eau</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Point d'eau à proximité</label>
                        <x-choice-card name="point_eau_proximite" label="" :value="old('point_eau_proximite')" />
                        @error('point_eau_proximite') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="commentaire_point_eau" class="block text-sm font-medium text-gray-700 mb-1.5">Commentaire point d'eau</label>
                        <textarea id="commentaire_point_eau" name="commentaire_point_eau" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails...">{{ old('commentaire_point_eau') }}</textarea>
                        @error('commentaire_point_eau') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="type_point_eau" class="block text-sm font-medium text-gray-700 mb-1.5">Type de point d'eau</label>
                        <x-select 
                                name="type_point_eau" 
                                label="Type de point d'eau" 
                                :options="$typePointEauOptions" 
                                placeholder="Sélectionner" 
                                error="type_point_eau"
                        />
                        @error('type_point_eau') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="distance_point_eau" class="block text-sm font-medium text-gray-700 mb-1.5">Distance point d'eau (m)</label>
                        <input type="number" id="distance_point_eau" name="distance_point_eau" value="{{ old('distance_point_eau') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="0">
                        @error('distance_point_eau') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Système d'irrigation</label>
                        <x-choice-card name="systeme_irrigation" label="" :value="old('systeme_irrigation')" />
                        @error('systeme_irrigation') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="commentaire_irrigation" class="block text-sm font-medium text-gray-700 mb-1.5">Commentaire irrigation</label>
                        <textarea id="commentaire_irrigation" name="commentaire_irrigation" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails...">{{ old('commentaire_irrigation') }}</textarea>
                        @error('commentaire_irrigation') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="type_irrigation" class="block text-sm font-medium text-gray-700 mb-1.5">Type d'irrigation</label>
                        <x-select 
                                name="type_irrigation" 
                                label="Type d'irrigation" 
                                :options="$typeIrrigationOptions" 
                                placeholder="Sélectionner" 
                                error="type_irrigation"
                        />
                        @error('type_irrigation') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Inondations saisonnières</label>
                        <x-choice-card name="inondations_saisonnieres" label="" :value="old('inondations_saisonnieres')" />
                        @error('inondations_saisonnieres') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="commentaire_inondations" class="block text-sm font-medium text-gray-700 mb-1.5">Commentaire inondations</label>
                        <textarea id="commentaire_inondations" name="commentaire_inondations" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails...">{{ old('commentaire_inondations') }}</textarea>
                        @error('commentaire_inondations') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="periode_secheresse" class="block text-sm font-medium text-gray-700 mb-1.5">Période de sécheresse</label>
                        <input type="text" id="periode_secheresse" name="periode_secheresse" value="{{ old('periode_secheresse') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Décembre à Février">
                        @error('periode_secheresse') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 9 : Végétation et usages --}}
            <div class="px-6 py-5 border-b border-gray-100 border-t border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Végétation et usages</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="occupation_actuelle" class="block text-sm font-medium text-gray-700 mb-1.5">Occupation actuelle</label>
                        <x-select 
                                name="occupation_actuelle" 
                                label="Occupation actuelle" 
                                :options="$occupationActuelleOptions" 
                                placeholder="Sélectionner" 
                                error="occupation_actuelle"
                        />
                        @error('occupation_actuelle') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="cultures_place" class="block text-sm font-medium text-gray-700 mb-1.5">Cultures en place</label>
                        <input type="text" id="cultures_place" name="cultures_place" value="{{ old('cultures_place') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Maïs, Manioc...">
                        @error('cultures_place') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Présence d'arbres</label>
                        <x-choice-card name="presence_arbres" label="" :value="old('presence_arbres')" />
                        @error('presence_arbres') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="commentaire_arbres" class="block text-sm font-medium text-gray-700 mb-1.5">Commentaire arbres</label>
                        <textarea id="commentaire_arbres" name="commentaire_arbres" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails...">{{ old('commentaire_arbres') }}</textarea>
                        @error('commentaire_arbres') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="especes_ligneuses" class="block text-sm font-medium text-gray-700 mb-1.5">Espèces ligneuses</label>
                        <input type="text" id="especes_ligneuses" name="especes_ligneuses" value="{{ old('especes_ligneuses') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Acajou, Iroko...">
                        @error('especes_ligneuses') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="rendement_actuel" class="block text-sm font-medium text-gray-700 mb-1.5">Rendement actuel</label>
                        <input type="text" id="rendement_actuel" name="rendement_actuel" value="{{ old('rendement_actuel') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: 2 t/ha">
                        @error('rendement_actuel') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Antécédents de traitement</label>
                        <x-choice-card name="antecedents_traitement" label="" :value="old('antecedents_traitement')" />
                        @error('antecedents_traitement') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="commentaire_traitement" class="block text-sm font-medium text-gray-700 mb-1.5">Commentaire traitement</label>
                        <textarea id="commentaire_traitement" name="commentaire_traitement" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails...">{{ old('commentaire_traitement') }}</textarea>
                        @error('commentaire_traitement') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Produits herbicides</label>
                        <x-choice-card name="produits_herbicides" label="" :value="old('produits_herbicides')" />
                        @error('produits_herbicides') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Produits pesticides</label>
                        <x-choice-card name="produits_pesticides" label="" :value="old('produits_pesticides')" />
                        @error('produits_pesticides') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Produits engrais</label>
                        <x-choice-card name="produits_engrais" label="" :value="old('produits_engrais')" />
                        @error('produits_engrais') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Autres produits</label>
                        <x-choice-card name="produits_autre" label="" :value="old('produits_autre')" />
                        @error('produits_autre') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="produits_autre_detail" class="block text-sm font-medium text-gray-700 mb-1.5">Détail autres produits</label>
                        <input type="text" id="produits_autre_detail" name="produits_autre_detail" value="{{ old('produits_autre_detail') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Préciser...">
                        @error('produits_autre_detail') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 10 : Accès et infrastructures --}}
            <div class="px-6 py-5 border-b border-gray-100 border-t border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Accès et infrastructures</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Accès carrossable</label>
                        <x-choice-card name="acces_carrossable" label="" :value="old('acces_carrossable')" />
                        @error('acces_carrossable') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="commentaire_acces" class="block text-sm font-medium text-gray-700 mb-1.5">Commentaire accès</label>
                        <textarea id="commentaire_acces" name="commentaire_acces" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails...">{{ old('commentaire_acces') }}</textarea>
                        @error('commentaire_acces') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="distance_route_principale" class="block text-sm font-medium text-gray-700 mb-1.5">Distance route principale (km)</label>
                        <input type="number" step="0.01" min="0" id="distance_route_principale" name="distance_route_principale" value="{{ old('distance_route_principale') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="0.00">
                        @error('distance_route_principale') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Clôture existante</label>
                        <x-choice-card name="cloture_existante" label="" :value="old('cloture_existante')" />
                        @error('cloture_existante') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="commentaire_cloture" class="block text-sm font-medium text-gray-700 mb-1.5">Commentaire clôture</label>
                        <textarea id="commentaire_cloture" name="commentaire_cloture" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails...">{{ old('commentaire_cloture') }}</textarea>
                        @error('commentaire_cloture') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Bâtiment / Hangar</label>
                        <x-choice-card name="batiment_hangar" label="" :value="old('batiment_hangar')" />
                        @error('batiment_hangar') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="commentaire_batiment" class="block text-sm font-medium text-gray-700 mb-1.5">Commentaire bâtiment</label>
                        <textarea id="commentaire_batiment" name="commentaire_batiment" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails...">{{ old('commentaire_batiment') }}</textarea>
                        @error('commentaire_batiment') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Électricité disponible</label>
                        <x-choice-card name="electricite_disponible" label="" :value="old('electricite_disponible')" />
                        @error('electricite_disponible') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="commentaire_electricite" class="block text-sm font-medium text-gray-700 mb-1.5">Commentaire électricité</label>
                        <textarea id="commentaire_electricite" name="commentaire_electricite" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails...">{{ old('commentaire_electricite') }}</textarea>
                        @error('commentaire_electricite') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Réseau téléphonique</label>
                        <x-choice-card name="reseau_telephonique" label="" :value="old('reseau_telephonique')" />
                        @error('reseau_telephonique') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="commentaire_reseau" class="block text-sm font-medium text-gray-700 mb-1.5">Commentaire réseau</label>
                        <textarea id="commentaire_reseau" name="commentaire_reseau" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails...">{{ old('commentaire_reseau') }}</textarea>
                        @error('commentaire_reseau') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 11 : Remarques client --}}
            <div class="px-6 py-5 border-b border-gray-100 border-t border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Remarques client</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label for="observations_libres" class="block text-sm font-medium text-gray-700 mb-1.5">Observations libres</label>
                        <textarea id="observations_libres" name="observations_libres" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Observations...">{{ old('observations_libres') }}</textarea>
                        @error('observations_libres') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="signature_date" class="block text-sm font-medium text-gray-700 mb-1.5">Date de signature</label>
                        <input type="date" id="signature_date" name="signature_date" value="{{ old('signature_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        @error('signature_date') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="signature" class="block text-sm font-medium text-gray-700 mb-1.5">Signature</label>
                        <input type="text" id="signature" name="signature" value="{{ old('signature') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Nom signataire">
                        @error('signature') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 12 : Vérifications --}}
            <div class="px-6 py-5 border-b border-gray-100 border-t border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Vérifications</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="surface_totale_declare" class="block text-sm font-medium text-gray-700 mb-1.5">Surface totale déclarée</label>
                        <input type="text" id="surface_totale_declare" name="surface_totale_declare" value="{{ old('surface_totale_declare') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: 5.00 ha">
                        @error('surface_totale_declare') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="surface_totale_constate" class="block text-sm font-medium text-gray-700 mb-1.5">Surface totale constatée</label>
                        <input type="text" id="surface_totale_constate" name="surface_totale_constate" value="{{ old('surface_totale_constate') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: 5.00 ha">
                        @error('surface_totale_constate') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Surface totale concordée</label>
                        <x-choice-card name="surface_totale_concorde" label="" :value="old('surface_totale_concorde')" />
                        @error('surface_totale_concorde') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="surface_cultivable_declare" class="block text-sm font-medium text-gray-700 mb-1.5">Surface cultivable déclarée</label>
                        <input type="text" id="surface_cultivable_declare" name="surface_cultivable_declare" value="{{ old('surface_cultivable_declare') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: 4.00 ha">
                        @error('surface_cultivable_declare') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="surface_cultivable_constate" class="block text-sm font-medium text-gray-700 mb-1.5">Surface cultivable constatée</label>
                        <input type="text" id="surface_cultivable_constate" name="surface_cultivable_constate" value="{{ old('surface_cultivable_constate') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: 4.00 ha">
                        @error('surface_cultivable_constate') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Surface cultivable concordée</label>
                        <x-choice-card name="surface_cultivable_concorde" label="" :value="old('surface_cultivable_concorde')" />
                        @error('surface_cultivable_concorde') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="exposition_declare" class="block text-sm font-medium text-gray-700 mb-1.5">Exposition déclarée</label>
                        <input type="text" id="exposition_declare" name="exposition_declare" value="{{ old('exposition_declare') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Sud">
                        @error('exposition_declare') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="exposition_constate" class="block text-sm font-medium text-gray-700 mb-1.5">Exposition constatée</label>
                        <input type="text" id="exposition_constate" name="exposition_constate" value="{{ old('exposition_constate') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Sud-Est">
                        @error('exposition_constate') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Exposition concordée</label>
                        <x-choice-card name="exposition_concorde" label="" :value="old('exposition_concorde')" />
                        @error('exposition_concorde') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="pente_declare" class="block text-sm font-medium text-gray-700 mb-1.5">Pente déclarée</label>
                        <input type="text" id="pente_declare" name="pente_declare" value="{{ old('pente_declare') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: 5%">
                        @error('pente_declare') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="pente_constate" class="block text-sm font-medium text-gray-700 mb-1.5">Pente constatée</label>
                        <input type="text" id="pente_constate" name="pente_constate" value="{{ old('pente_constate') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: 6%">
                        @error('pente_constate') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Pente concordée</label>
                        <x-choice-card name="pente_concorde" label="" :value="old('pente_concorde')" />
                        @error('pente_concorde') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="topographie_declare" class="block text-sm font-medium text-gray-700 mb-1.5">Topographie déclarée</label>
                        <input type="text" id="topographie_declare" name="topographie_declare" value="{{ old('topographie_declare') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Plat">
                        @error('topographie_declare') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="topographie_constate" class="block text-sm font-medium text-gray-700 mb-1.5">Topographie constatée</label>
                        <input type="text" id="topographie_constate" name="topographie_constate" value="{{ old('topographie_constate') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Vallonné">
                        @error('topographie_constate') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Topographie concordée</label>
                        <x-choice-card name="topographie_concorde" label="" :value="old('topographie_concorde')" />
                        @error('topographie_concorde') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 13 : Synthèse vérification --}}
            <div class="px-6 py-5 border-b border-gray-100 border-t border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Synthèse vérification</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="nombre_criteres" class="block text-sm font-medium text-gray-700 mb-1.5">Nombre de critères</label>
                        <input type="number" id="nombre_criteres" name="nombre_criteres" value="{{ old('nombre_criteres') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="0">
                        @error('nombre_criteres') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="conformes" class="block text-sm font-medium text-gray-700 mb-1.5">Conformes</label>
                        <input type="number" id="conformes" name="conformes" value="{{ old('conformes') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="0">
                        @error('conformes') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="ecarts" class="block text-sm font-medium text-gray-700 mb-1.5">Écarts</label>
                        <input type="number" id="ecarts" name="ecarts" value="{{ old('ecarts') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="0">
                        @error('ecarts') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="total" class="block text-sm font-medium text-gray-700 mb-1.5">Total</label>
                        <input type="number" id="total" name="total" value="{{ old('total') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="0">
                        @error('total') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="ecarts_significatifs" class="block text-sm font-medium text-gray-700 mb-1.5">Écarts significatifs</label>
                        <textarea id="ecarts_significatifs" name="ecarts_significatifs" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails...">{{ old('ecarts_significatifs') }}</textarea>
                        @error('ecarts_significatifs') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="recommandation" class="block text-sm font-medium text-gray-700 mb-1.5">Recommandation</label>
                        <x-select 
                                name="recommandation" 
                                label="Recommandation" 
                                :options="$recommandationOptions" 
                                placeholder="Sélectionner" 
                                error="recommandation"
                        />
                        @error('recommandation') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="verificateur_nom" class="block text-sm font-medium text-gray-700 mb-1.5">Nom du vérificateur</label>
                        <input type="text" id="verificateur_nom" name="verificateur_nom" value="{{ old('verificateur_nom') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Nom complet">
                        @error('verificateur_nom') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="verificateur_poste" class="block text-sm font-medium text-gray-700 mb-1.5">Poste du vérificateur</label>
                        <input type="text" id="verificateur_poste" name="verificateur_poste" value="{{ old('verificateur_poste') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Ingénieur agronome">
                        @error('verificateur_poste') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="verificateur_date" class="block text-sm font-medium text-gray-700 mb-1.5">Date de vérification</label>
                        <input type="date" id="verificateur_date" name="verificateur_date" value="{{ old('verificateur_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        @error('verificateur_date') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="verificateur_signature" class="block text-sm font-medium text-gray-700 mb-1.5">Signature vérificateur</label>
                        <input type="text" id="verificateur_signature" name="verificateur_signature" value="{{ old('verificateur_signature') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Nom signataire">
                        @error('verificateur_signature') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Footer actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex items-center justify-end gap-3">
                <a href="{{ route('admin.farms.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                <button type="submit"
                        class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 active:bg-blue-800 transition-colors">
                    Créer l'exploitation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

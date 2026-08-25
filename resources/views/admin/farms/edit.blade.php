@extends('layouts.app')

@section('page-title', 'Modifier l\'Exploitation')

@section('content')
<div class="min-h-screen bg-gray-100" x-data="farmWizard()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="mb-6">
            <a href="{{ route('admin.farms.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-3 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour à la liste
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Modifier l'Exploitation</h1>
            <p class="mt-1 text-gray-500">Modifiez les informations de l'exploitation agricole en remplissant les sections.</p>
        </div>

        {{-- Barre de progression --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6 px-6 py-4">
            <div class="flex items-center justify-between">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex items-center flex-1">
                        <button @click="goToStep(index)"
                                class="flex items-center gap-2 text-sm transition-colors"
                                :class="currentStep === index ? 'text-blue-600 font-semibold' : step.completed ? 'text-emerald-600' : 'text-gray-400'">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold border-2 shrink-0 transition-all"
                                  :class="currentStep === index ? 'border-blue-600 bg-blue-50 text-blue-600' : step.completed ? 'border-emerald-500 bg-emerald-50 text-emerald-600' : 'border-gray-300 bg-white text-gray-400'">
                                <span x-show="!step.completed" x-text="index + 1"></span>
                                <svg x-show="step.completed" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                            <span class="hidden sm:inline" x-text="step.label"></span>
                        </button>
                        <div x-show="index < steps.length - 1" class="flex-1 h-px mx-3 bg-gray-200"></div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.farms.update', $farm->id) }}" method="POST" class="bg-white border border-gray-200 rounded-lg shadow-sm">
            @csrf
            @method('PUT')

            {{-- Étape 1 : Informations générales --}}
            <div x-show="currentStep === 0" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Informations générales</h2>
                    <p class="text-xs text-gray-500 mt-1">Nom, propriétaire, localisation et type de culture</p>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Nom de l'exploitation <span class="text-red-600">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $farm->name) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Ferme Kouassi">
                            @error('name') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Propriétaire <span class="text-red-600">*</span>
                            </label>
                            <select id="user_id" name="user_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ (old('user_id', $farm->user_id) == $client->id) ? 'selected' : '' }}>{{ $client->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Localisation <span class="text-red-600">*</span>
                            </label>
                            <input type="text" id="location" name="location" value="{{ old('location', $farm->location) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Abidjan, Côte d'Ivoire">
                            @error('location') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Type d'exploitation <span class="text-red-600">*</span>
                            </label>
                            <x-select 
                                    name="type" 
                                    label="Type d'exploitation" 
                                    :options="$farmTypeOptions" 
                                    :value="$farm->type?->value ?? ''"
                                    placeholder="Sélectionner" 
                                    error="type"
                            />
                            @error('type') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="culture_type" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Type de culture <span class="text-red-600">*</span>
                            </label>
                            <select id="culture_type" name="culture_type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                @foreach($cultureTypeOptions as $option)
                                    <option value="{{ $option['value'] }}" {{ old('culture_type', $farm->culture_type) === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('culture_type') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="total_area_hectares" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Surface (ha) <span class="text-red-600">*</span>
                            </label>
                            <input type="number" step="0.01" min="0" id="total_area_hectares" name="total_area_hectares" value="{{ old('total_area_hectares', $farm->total_area_hectares) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0.00">
                            @error('total_area_hectares') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Statut <span class="text-red-600">*</span>
                            </label>
                            <select id="status" name="status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                @foreach($farmStatusOptions as $option)
                                    <option value="{{ $option['value'] }}" {{ old('status', $farm->status) === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('status') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Étape 2 : Coordonnées GPS & Culture --}}
            <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Coordonnées GPS & Suivi culture</h2>
                    <p class="text-xs text-gray-500 mt-1">Localisation précise et informations sur la culture</p>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1.5">Latitude</label>
                            <input type="number" step="0.0000001" id="latitude" name="latitude" value="{{ old('latitude', $farm->latitude) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="5.3361">
                            @error('latitude') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1.5">Longitude</label>
                            <input type="number" step="0.0000001" id="longitude" name="longitude" value="{{ old('longitude', $farm->longitude) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="-4.0202">
                            @error('longitude') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="crop_stage" class="block text-sm font-medium text-gray-700 mb-1.5">Stade de la culture</label>
                            <select id="crop_stage" name="crop_stage"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                @foreach($stadePhenologiqueOptions as $option)
                                    <option value="{{ $option['value'] }}" {{ old('crop_stage', $farm->crop_stage) === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('crop_stage') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="crop_stage_progress" class="block text-sm font-medium text-gray-700 mb-1.5">Progression (%)</label>
                            <input type="number" min="0" max="100" id="crop_stage_progress" name="crop_stage_progress" value="{{ old('crop_stage_progress', $farm->crop_stage_progress) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0">
                            @error('crop_stage_progress') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="expected_harvest_date" class="block text-sm font-medium text-gray-700 mb-1.5">Date de récolte prévue</label>
                            <input type="date" id="expected_harvest_date" name="expected_harvest_date" value="{{ old('expected_harvest_date', $farm->expected_harvest_date?->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            @error('expected_harvest_date') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="last_visit_date" class="block text-sm font-medium text-gray-700 mb-1.5">Dernière visite</label>
                            <input type="date" id="last_visit_date" name="last_visit_date" value="{{ old('last_visit_date', $farm->last_visit_date?->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            @error('last_visit_date') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1.5">Notes supplémentaires</label>
                        <textarea id="notes" name="notes" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Informations complémentaires...">{{ old('notes', $farm->notes) }}</textarea>
                        @error('notes') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Étape 3 : Parcelles & Caractéristiques --}}
            <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Parcelles & Caractéristiques</h2>
                    <p class="text-xs text-gray-500 mt-1">Informations cadastrales et caractéristiques générales</p>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="reference_dossier" class="block text-sm font-medium text-gray-700 mb-1.5">Référence dossier</label>
                            <input type="text" id="reference_dossier" name="reference_dossier" value="{{ old('reference_dossier', $farm->reference_dossier) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: REF-001">
                            @error('reference_dossier') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="date_declaration" class="block text-sm font-medium text-gray-700 mb-1.5">Date de déclaration</label>
                            <input type="date" id="date_declaration" name="date_declaration" value="{{ old('date_declaration', $farm->date_declaration?->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            @error('date_declaration') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="nom_client" class="block text-sm font-medium text-gray-700 mb-1.5">Nom client</label>
                            <input type="text" id="nom_client" name="nom_client" value="{{ old('nom_client', $farm->nom_client) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Nom du client">
                            @error('nom_client') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="contact" class="block text-sm font-medium text-gray-700 mb-1.5">Contact</label>
                            <input type="text" id="contact" name="contact" value="{{ old('contact', $farm->contact) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Téléphone ou email">
                            @error('contact') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="numero_cadastral" class="block text-sm font-medium text-gray-700 mb-1.5">Numéro cadastral</label>
                            <input type="text" id="numero_cadastral" name="numero_cadastral" value="{{ old('numero_cadastral', $farm->numero_cadastral) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: CAD-12345">
                            @error('numero_cadastral') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="surface_totale" class="block text-sm font-medium text-gray-700 mb-1.5">Surface totale (ha)</label>
                            <input type="number" step="0.01" min="0" id="surface_totale" name="surface_totale" value="{{ old('surface_totale', $farm->surface_totale) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0.00">
                            @error('surface_totale') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="surface_cultivable" class="block text-sm font-medium text-gray-700 mb-1.5">Surface cultivable (ha)</label>
                            <input type="number" step="0.01" min="0" id="surface_cultivable" name="surface_cultivable" value="{{ old('surface_cultivable', $farm->surface_cultivable) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0.00">
                            @error('surface_cultivable') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="forme_parcelle" class="block text-sm font-medium text-gray-700 mb-1.5">Forme de la parcelle</label>
                            <select id="forme_parcelle" name="forme_parcelle"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                @foreach($formeParcelleOptions as $option)
                                    <option value="{{ $option['value'] }}" {{ old('forme_parcelle', $farm->forme_parcelle) === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('forme_parcelle') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="exposition_principale" class="block text-sm font-medium text-gray-700 mb-1.5">Exposition principale</label>
                            <select id="exposition_principale" name="exposition_principale"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                @foreach($expositionParcelleOptions as $option)
                                    <option value="{{ $option['value'] }}" {{ old('exposition_principale', $farm->exposition_principale) === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('exposition_principale') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="pente_moyenne" class="block text-sm font-medium text-gray-700 mb-1.5">Pente moyenne</label>
                            <select id="pente_moyenne" name="pente_moyenne"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                @foreach($penteMoyenneOptions as $option)
                                    <option value="{{ $option['value'] }}" {{ old('pente_moyenne', $farm->pente_moyenne) === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('pente_moyenne') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="altitude" class="block text-sm font-medium text-gray-700 mb-1.5">Altitude (m)</label>
                            <input type="number" id="altitude" name="altitude" value="{{ old('altitude', $farm->altitude) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0">
                            @error('altitude') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="topographie" class="block text-sm font-medium text-gray-700 mb-1.5">Topographie</label>
                            <select id="topographie" name="topographie"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                @foreach($topographieOptions as $option)
                                    <option value="{{ $option['value'] }}" {{ old('topographie', $farm->topographie) === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('topographie') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Étape 4 : Sols --}}
            <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Sols</h2>
                    <p class="text-xs text-gray-500 mt-1">Type, analyse et caractéristiques du sol</p>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="type_sol" class="block text-sm font-medium text-gray-700 mb-1.5">Type de sol</label>
                            <select id="type_sol" name="type_sol"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                @foreach($typeSolOptions as $option)
                                    <option value="{{ $option['value'] }}" {{ old('type_sol', $farm->type_sol) === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('type_sol') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="couleur_sol" class="block text-sm font-medium text-gray-700 mb-1.5">Couleur du sol</label>
                            <select id="couleur_sol" name="couleur_sol"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                @foreach($couleurSolOptions as $option)
                                    <option value="{{ $option['value'] }}" {{ old('couleur_sol', $farm->couleur_sol) === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('couleur_sol') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="profondeur_sol" class="block text-sm font-medium text-gray-700 mb-1.5">Profondeur du sol</label>
                            <input type="text" id="profondeur_sol" name="profondeur_sol" value="{{ old('profondeur_sol', $farm->profondeur_sol) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: 30 cm">
                            @error('profondeur_sol') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="problemes_erosion" class="block text-sm font-medium text-gray-700 mb-1.5">Problèmes d'érosion</label>
                            <select id="problemes_erosion" name="problemes_erosion"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('problemes_erosion', $farm->problemes_erosion) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('problemes_erosion', $farm->problemes_erosion) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('problemes_erosion') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="ph" class="block text-sm font-medium text-gray-700 mb-1.5">pH</label>
                            <input type="number" step="0.01" min="0" max="14" id="ph" name="ph" value="{{ old('ph', $farm->ph) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0.00">
                            @error('ph') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="source_ph" class="block text-sm font-medium text-gray-700 mb-1.5">Source pH</label>
                            <input type="text" id="source_ph" name="source_ph" value="{{ old('source_ph', $farm->source_ph) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Laboratoire, Test kit...">
                            @error('source_ph') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Choice cards groupés 3 par ligne --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Présence de cailloux</label>
                            <select name="presence_cailloux" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('presence_cailloux', $farm->presence_cailloux) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('presence_cailloux', $farm->presence_cailloux) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('presence_cailloux') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Analyse de sol réalisée</label>
                            <div x-data="{ showPrecision: {{ old('analyse_sol_realisee', $farm->analyse_sol_realisee) === '' ? 'true' : 'false' }} }" class="space-y-2">
                                <div class="flex flex-wrap gap-3">
                                    <label class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 cursor-pointer hover:bg-gray-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700 transition-colors">
                                        <input type="radio" name="analyse_sol_realisee" value="1" class="sr-only" {{ old('analyse_sol_realisee', $farm->analyse_sol_realisee) === '1' ? 'checked' : '' }} @change="showPrecision = false">
                                        Oui
                                    </label>
                                    <label class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 cursor-pointer hover:bg-gray-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700 transition-colors">
                                        <input type="radio" name="analyse_sol_realisee" value="0" class="sr-only" {{ old('analyse_sol_realisee', $farm->analyse_sol_realisee) === '0' ? 'checked' : '' }} @change="showPrecision = false">
                                        Non
                                    </label>
                                    <label class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 cursor-pointer hover:bg-gray-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700 transition-colors">
                                        <input type="radio" name="analyse_sol_realisee" value="" class="sr-only" {{ old('analyse_sol_realisee', $farm->analyse_sol_realisee) === '' || old('analyse_sol_realisee', $farm->analyse_sol_realisee) === null ? 'checked' : '' }} @change="showPrecision = true">
                                        À préciser
                                    </label>
                                </div>
                                <div x-show="showPrecision" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="overflow-hidden">
                                    <input type="text" name="analyse_sol_realisee_precision" value="{{ old('analyse_sol_realisee_precision', $farm->analyse_sol_realisee_precision) }}" placeholder="Précisez..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                </div>
                            </div>
                            @error('analyse_sol_realisee') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Étape 5 : Ressources en eau --}}
            <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Ressources en eau</h2>
                    <p class="text-xs text-gray-500 mt-1">Points d'eau, irrigation et inondations</p>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="type_point_eau" class="block text-sm font-medium text-gray-700 mb-1.5">Type de point d'eau</label>
                            <select id="type_point_eau" name="type_point_eau"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                @foreach($typePointEauOptions as $option)
                                    <option value="{{ $option['value'] }}" {{ old('type_point_eau', $farm->type_point_eau) === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('type_point_eau') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="distance_point_eau" class="block text-sm font-medium text-gray-700 mb-1.5">Distance point d'eau (m)</label>
                            <input type="number" id="distance_point_eau" name="distance_point_eau" value="{{ old('distance_point_eau', $farm->distance_point_eau) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0">
                            @error('distance_point_eau') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="type_irrigation" class="block text-sm font-medium text-gray-700 mb-1.5">Type d'irrigation</label>
                            <select id="type_irrigation" name="type_irrigation"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                @foreach($typeIrrigationOptions as $option)
                                    <option value="{{ $option['value'] }}" {{ old('type_irrigation', $farm->type_irrigation) === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('type_irrigation') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="periode_secheresse" class="block text-sm font-medium text-gray-700 mb-1.5">Période de sécheresse</label>
                            <input type="text" id="periode_secheresse" name="periode_secheresse" value="{{ old('periode_secheresse', $farm->periode_secheresse) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Décembre à Février">
                            @error('periode_secheresse') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Choice cards groupés 3 par ligne --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Point d'eau à proximité</label>
                            <select name="point_eau_proximite" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('point_eau_proximite', $farm->point_eau_proximite) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('point_eau_proximite', $farm->point_eau_proximite) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('point_eau_proximite') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Système d'irrigation</label>
                            <select name="systeme_irrigation" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('systeme_irrigation', $farm->systeme_irrigation) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('systeme_irrigation', $farm->systeme_irrigation) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('systeme_irrigation') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Inondations saisonnières</label>
                            <select name="inondations_saisonnieres" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('inondations_saisonnieres', $farm->inondations_saisonnieres) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('inondations_saisonnieres', $farm->inondations_saisonnieres) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('inondations_saisonnieres') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Étape 6 : Végétation & Accès --}}
            <div x-show="currentStep === 5" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Végétation, Usages & Accès</h2>
                    <p class="text-xs text-gray-500 mt-1">Occupation du sol, traitements et infrastructures</p>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="occupation_actuelle" class="block text-sm font-medium text-gray-700 mb-1.5">Occupation actuelle</label>
                            <select id="occupation_actuelle" name="occupation_actuelle"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                @foreach($occupationActuelleOptions as $option)
                                    <option value="{{ $option['value'] }}" {{ old('occupation_actuelle', $farm->occupation_actuelle) === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('occupation_actuelle') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="cultures_place" class="block text-sm font-medium text-gray-700 mb-1.5">Cultures en place</label>
                            <input type="text" id="cultures_place" name="cultures_place" value="{{ old('cultures_place', $farm->cultures_place) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Maïs, Manioc...">
                            @error('cultures_place') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="especes_ligneuses" class="block text-sm font-medium text-gray-700 mb-1.5">Espèces ligneuses</label>
                            <input type="text" id="especes_ligneuses" name="especes_ligneuses" value="{{ old('especes_ligneuses', $farm->especes_ligneuses) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Acajou, Iroko...">
                            @error('especes_ligneuses') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="rendement_actuel" class="block text-sm font-medium text-gray-700 mb-1.5">Rendement actuel</label>
                            <input type="text" id="rendement_actuel" name="rendement_actuel" value="{{ old('rendement_actuel', $farm->rendement_actuel) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: 2 t/ha">
                            @error('rendement_actuel') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="distance_route_principale" class="block text-sm font-medium text-gray-700 mb-1.5">Distance route principale (km)</label>
                            <input type="number" step="0.01" min="0" id="distance_route_principale" name="distance_route_principale" value="{{ old('distance_route_principale', $farm->distance_route_principale) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0.00">
                            @error('distance_route_principale') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="produits_autre_detail" class="block text-sm font-medium text-gray-700 mb-1.5">Détail autres produits</label>
                            <input type="text" id="produits_autre_detail" name="produits_autre_detail" value="{{ old('produits_autre_detail', $farm->produits_autre_detail) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Préciser...">
                            @error('produits_autre_detail') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Choice cards groupés 3 par ligne --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Présence d'arbres</label>
                            <select name="presence_arbres" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('presence_arbres', $farm->presence_arbres) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('presence_arbres', $farm->presence_arbres) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('presence_arbres') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Antécédents de traitement</label>
                            <select name="antecedents_traitement" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('antecedents_traitement', $farm->antecedents_traitement) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('antecedents_traitement', $farm->antecedents_traitement) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('antecedents_traitement') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Produits herbicides</label>
                            <select name="produits_herbicides" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('produits_herbicides', $farm->produits_herbicides) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('produits_herbicides', $farm->produits_herbicides) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('produits_herbicides') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Produits pesticides</label>
                            <select name="produits_pesticides" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('produits_pesticides', $farm->produits_pesticides) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('produits_pesticides', $farm->produits_pesticides) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('produits_pesticides') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Produits engrais</label>
                            <select name="produits_engrais" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('produits_engrais', $farm->produits_engrais) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('produits_engrais', $farm->produits_engrais) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('produits_engrais') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Autres produits</label>
                            <select name="produits_autre" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('produits_autre', $farm->produits_autre) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('produits_autre', $farm->produits_autre) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('produits_autre') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Accès carrossable</label>
                            <select name="acces_carrossable" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('acces_carrossable', $farm->acces_carrossable) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('acces_carrossable', $farm->acces_carrossable) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('acces_carrossable') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Clôture existante</label>
                            <select name="cloture_existante" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('cloture_existante', $farm->cloture_existante) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('cloture_existante', $farm->cloture_existante) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('cloture_existante') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Bâtiment / Hangar</label>
                            <select name="batiment_hangar" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('batiment_hangar', $farm->batiment_hangar) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('batiment_hangar', $farm->batiment_hangar) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('batiment_hangar') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Électricité disponible</label>
                            <select name="electricite_disponible" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('electricite_disponible', $farm->electricite_disponible) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('electricite_disponible', $farm->electricite_disponible) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('electricite_disponible') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Réseau téléphonique</label>
                            <select name="reseau_telephonique" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('reseau_telephonique', $farm->reseau_telephonique) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('reseau_telephonique', $farm->reseau_telephonique) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('reseau_telephonique') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Étape 7 : Remarques & Vérifications --}}
            <div x-show="currentStep === 6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Remarques & Vérifications</h2>
                    <p class="text-xs text-gray-500 mt-1">Observations, vérifications et signature</p>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label for="observations_libres" class="block text-sm font-medium text-gray-700 mb-1.5">Observations libres</label>
                            <textarea id="observations_libres" name="observations_libres" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                      placeholder="Observations...">{{ old('observations_libres', $farm->observations_libres) }}</textarea>
                            @error('observations_libres') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="signature_date" class="block text-sm font-medium text-gray-700 mb-1.5">Date de signature</label>
                            <input type="date" id="signature_date" name="signature_date" value="{{ old('signature_date', $farm->signature_date?->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            @error('signature_date') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="signature" class="block text-sm font-medium text-gray-700 mb-1.5">Signature</label>
                            <input type="text" id="signature" name="signature" value="{{ old('signature', $farm->signature) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Nom signataire">
                            @error('signature') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <hr class="border-gray-200 md:col-span-2">

                        <div>
                            <label for="surface_totale_declare" class="block text-sm font-medium text-gray-700 mb-1.5">Surface totale déclarée</label>
                            <input type="text" id="surface_totale_declare" name="surface_totale_declare" value="{{ old('surface_totale_declare', $farm->surface_totale_declare) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: 5.00 ha">
                            @error('surface_totale_declare') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="surface_totale_constate" class="block text-sm font-medium text-gray-700 mb-1.5">Surface totale constatée</label>
                            <input type="text" id="surface_totale_constate" name="surface_totale_constate" value="{{ old('surface_totale_constate', $farm->surface_totale_constate) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: 5.00 ha">
                            @error('surface_totale_constate') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Surface totale concordée</label>
                            <select name="surface_totale_concorde" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('surface_totale_concorde', $farm->surface_totale_concorde) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('surface_totale_concorde', $farm->surface_totale_concorde) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('surface_totale_concorde') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="surface_cultivable_declare" class="block text-sm font-medium text-gray-700 mb-1.5">Surface cultivable déclarée</label>
                            <input type="text" id="surface_cultivable_declare" name="surface_cultivable_declare" value="{{ old('surface_cultivable_declare', $farm->surface_cultivable_declare) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: 4.00 ha">
                            @error('surface_cultivable_declare') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="surface_cultivable_constate" class="block text-sm font-medium text-gray-700 mb-1.5">Surface cultivable constatée</label>
                            <input type="text" id="surface_cultivable_constate" name="surface_cultivable_constate" value="{{ old('surface_cultivable_constate', $farm->surface_cultivable_constate) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: 4.00 ha">
                            @error('surface_cultivable_constate') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Surface cultivable concordée</label>
                            <select name="surface_cultivable_concorde" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('surface_cultivable_concorde', $farm->surface_cultivable_concorde) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('surface_cultivable_concorde', $farm->surface_cultivable_concorde) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('surface_cultivable_concorde') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="exposition_declare" class="block text-sm font-medium text-gray-700 mb-1.5">Exposition déclarée</label>
                            <input type="text" id="exposition_declare" name="exposition_declare" value="{{ old('exposition_declare', $farm->exposition_declare) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Sud">
                            @error('exposition_declare') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="exposition_constate" class="block text-sm font-medium text-gray-700 mb-1.5">Exposition constatée</label>
                            <input type="text" id="exposition_constate" name="exposition_constate" value="{{ old('exposition_constate', $farm->exposition_constate) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Sud-Est">
                            @error('exposition_constate') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Exposition concordée</label>
                            <select name="exposition_concorde" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('exposition_concorde', $farm->exposition_concorde) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('exposition_concorde', $farm->exposition_concorde) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('exposition_concorde') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="pente_declare" class="block text-sm font-medium text-gray-700 mb-1.5">Pente déclarée</label>
                            <input type="text" id="pente_declare" name="pente_declare" value="{{ old('pente_declare', $farm->pente_declare) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: 5%">
                            @error('pente_declare') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="pente_constate" class="block text-sm font-medium text-gray-700 mb-1.5">Pente constatée</label>
                            <input type="text" id="pente_constate" name="pente_constate" value="{{ old('pente_constate', $farm->pente_constate) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: 6%">
                            @error('pente_constate') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Pente concordée</label>
                            <select name="pente_concorde" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('pente_concorde', $farm->pente_concorde) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('pente_concorde', $farm->pente_concorde) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('pente_concorde') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="topographie_declare" class="block text-sm font-medium text-gray-700 mb-1.5">Topographie déclarée</label>
                            <input type="text" id="topographie_declare" name="topographie_declare" value="{{ old('topographie_declare', $farm->topographie_declare) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Plat">
                            @error('topographie_declare') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="topographie_constate" class="block text-sm font-medium text-gray-700 mb-1.5">Topographie constatée</label>
                            <input type="text" id="topographie_constate" name="topographie_constate" value="{{ old('topographie_constate', $farm->topographie_constate) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Vallonné">
                            @error('topographie_constate') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Topographie concordée</label>
                            <select name="topographie_concorde" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">—</option>
                                <option value="1" {{ old('topographie_concorde', $farm->topographie_concorde) === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('topographie_concorde', $farm->topographie_concorde) === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('topographie_concorde') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <hr class="border-gray-200">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="nombre_criteres" class="block text-sm font-medium text-gray-700 mb-1.5">Nombre de critères</label>
                            <input type="number" id="nombre_criteres" name="nombre_criteres" value="{{ old('nombre_criteres', $farm->nombre_criteres) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0">
                            @error('nombre_criteres') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="conformes" class="block text-sm font-medium text-gray-700 mb-1.5">Conformes</label>
                            <input type="number" id="conformes" name="conformes" value="{{ old('conformes', $farm->conformes) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0">
                            @error('conformes') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="ecarts" class="block text-sm font-medium text-gray-700 mb-1.5">Écarts</label>
                            <input type="number" id="ecarts" name="ecarts" value="{{ old('ecarts', $farm->ecarts) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0">
                            @error('ecarts') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="total" class="block text-sm font-medium text-gray-700 mb-1.5">Total</label>
                            <input type="number" id="total" name="total" value="{{ old('total', $farm->total) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0">
                            @error('total') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="ecarts_significatifs" class="block text-sm font-medium text-gray-700 mb-1.5">Écarts significatifs</label>
                            <textarea id="ecarts_significatifs" name="ecarts_significatifs" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                      placeholder="Détails...">{{ old('ecarts_significatifs', $farm->ecarts_significatifs) }}</textarea>
                            @error('ecarts_significatifs') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="recommandation" class="block text-sm font-medium text-gray-700 mb-1.5">Recommandation</label>
                            <select id="recommandation" name="recommandation"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                @foreach($recommandationOptions as $option)
                                    <option value="{{ $option['value'] }}" {{ old('recommandation', $farm->recommandation) === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('recommandation') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="verificateur_nom" class="block text-sm font-medium text-gray-700 mb-1.5">Nom du vérificateur</label>
                            <input type="text" id="verificateur_nom" name="verificateur_nom" value="{{ old('verificateur_nom', $farm->verificateur_nom) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Nom complet">
                            @error('verificateur_nom') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="verificateur_poste" class="block text-sm font-medium text-gray-700 mb-1.5">Poste du vérificateur</label>
                            <input type="text" id="verificateur_poste" name="verificateur_poste" value="{{ old('verificateur_poste', $farm->verificateur_poste) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Ingénieur agronome">
                            @error('verificateur_poste') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="verificateur_date" class="block text-sm font-medium text-gray-700 mb-1.5">Date de vérification</label>
                            <input type="date" id="verificateur_date" name="verificateur_date" value="{{ old('verificateur_date', $farm->verificateur_date?->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            @error('verificateur_date') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="verificateur_signature" class="block text-sm font-medium text-gray-700 mb-1.5">Signature vérificateur</label>
                            <input type="text" id="verificateur_signature" name="verificateur_signature" value="{{ old('verificateur_signature', $farm->verificateur_signature) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Nom signataire">
                            @error('verificateur_signature') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer actions avec navigation --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex items-center justify-between">
                <div>
                    <button type="button" x-show="currentStep > 0" @click="prevStep()"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Précédent
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.farms.index') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                        Annuler
                    </a>
                    <button type="button" x-show="currentStep < steps.length - 1" @click="nextStep()"
                            class="inline-flex items-center gap-1.5 px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 active:bg-blue-800 transition-colors">
                        Suivant
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <button type="submit" x-show="currentStep === steps.length - 1"
                            class="px-5 py-2 text-sm font-medium text-white bg-emerald-600 rounded-md hover:bg-emerald-700 active:bg-emerald-800 transition-colors">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            Mettre à jour
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function farmWizard() {
        return {
            currentStep: 0,
            steps: [
                { label: 'Général', completed: false },
                { label: 'GPS & Culture', completed: false },
                { label: 'Parcelles', completed: false },
                { label: 'Sols', completed: false },
                { label: 'Eau', completed: false },
                { label: 'Végétation & Accès', completed: false },
                { label: 'Vérifications', completed: false },
            ],
            nextStep() {
                if (this.currentStep < this.steps.length - 1) {
                    this.steps[this.currentStep].completed = true;
                    this.currentStep++;
                }
            },
            prevStep() {
                if (this.currentStep > 0) {
                    this.currentStep--;
                }
            },
            goToStep(index) {
                if (index <= this.currentStep || this.steps[index - 1]?.completed) {
                    this.currentStep = index;
                }
            }
        }
    }
</script>
@endsection

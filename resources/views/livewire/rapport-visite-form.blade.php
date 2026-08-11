@php
    $typeVisiteOptions = collect(\App\Enums\TypeVisite::cases())->map(fn($case) => ['value' => $case->value, 'label' => $case->label()])->toArray();
    $conditionMeteoOptions = collect(\App\Enums\ConditionMeteo::cases())->map(fn($case) => ['value' => $case->value, 'label' => $case->label()])->toArray();
    $dureeVisiteOptions = collect(\App\Enums\DureeVisite::cases())->map(fn($case) => ['value' => $case->value, 'label' => $case->label()])->toArray();
    $stadePhenologiqueOptions = collect(\App\Enums\StadePhenologique::cases())->map(fn($case) => ['value' => $case->value, 'label' => $case->label()])->toArray();
    $statutRapportOptions = collect(\App\Enums\StatutRapport::cases())->map(fn($case) => ['value' => $case->value, 'label' => $case->label()])->toArray();
    $typeActiviteOptions = collect(\App\Enums\TypeActivite::cases())->map(fn($case) => ['value' => $case->value, 'label' => $case->label()])->toArray();

    // Initialiser $rapport pour le mode création
    $rapport = $rapport ?? new \App\Models\RapportVisite();

    // Récupérer les données de la table annexe pour l'édition
    $visiteCulture = $rapport->visiteCultures->first() ?? null;
    $visiteElevage = $rapport->visiteElevage->first() ?? null;
    $visiteAutre = $rapport->visiteAutres->first() ?? null;
    $typeActiviteValue = old('type_activite', $rapport->type_activite?->value ?? 'culture');

    // Déterminer le type de technicien sélectionné (pour l'édition)
    $selectedTechnicien = $techniciens->firstWhere('id', old('technicien_id', $rapport->technicien_id ?? ($defaultTechnicienId ?? '')));
    $selectedTypeTechnicien = $selectedTechnicien?->type_technicien?->value ?? ($selectedTechnicien?->type_technicien ?? '');
    $isAdmin = $selectedTechnicien?->role === 'admin';
@endphp

<div class="min-h-screen bg-gray-100" x-data="rapportWizard()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="mb-6">
            <a href="{{ route('admin.rapports-visite.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-3 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour à la liste
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $isEdit ? 'Modifier le Rapport de Visite' : 'Nouveau Rapport de Visite' }}</h1>
            <p class="mt-1 text-gray-500">{{ $isEdit ? 'Modifiez les informations du rapport de visite.' : 'Créez un nouveau rapport de visite terrain.' }}</p>
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
        <form action="{{ $isEdit ? route('admin.rapports-visite.update', $rapport->id) : route('admin.rapports-visite.store') }}" 
              method="POST"
              class="bg-white border border-gray-200 rounded-lg shadow-sm">
            
            @if($isEdit)
                @method('PUT')
            @endif
            
            @csrf

            {{-- Erreurs globales --}}
            @if ($errors->any())
                <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Il y a des erreurs dans le formulaire</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Étape 1 : Informations générales --}}
            <div x-show="currentStep === 0" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Étape 1 - Informations générales</h2>
                    <p class="text-xs text-gray-500 mt-1">Technicien, date, client et localisation</p>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="technicien_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Technicien <span class="text-red-600">*</span>
                            </label>
                            <select id="technicien_id" name="technicien_id" required
                                    x-model="technicienId"
                                    @change="updateTypeActiviteFromTechnicien()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner un technicien</option>
                                @foreach($techniciens as $t)
                                    <option value="{{ $t->id }}" 
                                            data-type-technicien="{{ $t->type_technicien?->value ?? $t->type_technicien ?? '' }}"
                                            data-role="{{ $t->role }}"
                                            {{ old('technicien_id', $rapport->technicien_id ?? ($defaultTechnicienId ?? '')) == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }}
                                        @if($t->type_technicien)
                                            ({{ $t->type_technicien instanceof \App\Enums\TypeTechnicien ? $t->type_technicien->label() : $t->type_technicien }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('technicien_id') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="date_visite" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Date de visite <span class="text-red-600">*</span>
                            </label>
                            <input type="date" id="date_visite" name="date_visite" required
                                   value="{{ old('date_visite', $rapport->date_visite ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            @error('date_visite') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Client <span class="text-red-600">*</span>
                            </label>
                            <x-select 
                                name="client_id" 
                                label="Client" 
                                :options="$clients->map(fn($c) => ['value' => $c->id, 'label' => $c->user?->name ?? $c->nom ?? 'Client #' . $c->id])" 
                                :value="$rapport->client_id ?? ''"
                                placeholder="Sélectionner un client" 
                                error="client_id"
                            />
                            @error('client_id') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="localisation_parcelle" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Localisation de la parcelle <span class="text-red-600">*</span>
                            </label>
                            <input type="text" id="localisation_parcelle" name="localisation_parcelle" required
                                   value="{{ old('localisation_parcelle', $rapport->localisation_parcelle ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Village de Kpouèbo, parcelle 12">
                            @error('localisation_parcelle') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="type_visite" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Type de visite <span class="text-red-600">*</span>
                            </label>
                            <x-select 
                                name="type_visite" 
                                label="Type de visite" 
                                :options="$typeVisiteOptions" 
                                :value="$rapport->type_visite?->value ?? ''"
                                placeholder="Sélectionner" 
                                error="type_visite"
                            />
                            @error('type_visite') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div x-show="canChooseActivite" class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Type de rapport / activité <span class="text-red-600">*</span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                {{-- Carte Culture --}}
                                <label class="relative flex flex-col items-center gap-2 rounded-xl border-2 bg-white px-4 py-5 text-sm font-medium text-gray-700 cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition-all has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700">
                                    <input type="radio" name="type_activite" value="culture" x-model="typeActivite"
                                           class="sr-only">
                                    <span class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 text-green-600">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21c3 0 3-1.5 3-3V7a1 1 0 011-1h10a1 1 0 011 1v11c0 1.5 0 3 3 3M6 10h12" />
                                        </svg>
                                    </span>
                                    <span class="font-semibold">Culture</span>
                                    <span class="text-xs text-gray-500 text-center">Cultures, sol, ravageurs, récoltes</span>
                                </label>

                                {{-- Carte Élevage --}}
                                <label class="relative flex flex-col items-center gap-2 rounded-xl border-2 bg-white px-4 py-5 text-sm font-medium text-gray-700 cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition-all has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700">
                                    <input type="radio" name="type_activite" value="elevage" x-model="typeActivite"
                                           class="sr-only">
                                    <span class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 text-blue-600">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-7 4 7M8 9h8M8 9a4 4 0 108 0M6 9h12v7a2 2 0 01-2 2H8a2 2 0 01-2-2V9z" />
                                        </svg>
                                    </span>
                                    <span class="font-semibold">Élevage</span>
                                    <span class="text-xs text-gray-500 text-center">Animaux, santé, soins, performances</span>
                                </label>

                                {{-- Carte Autre --}}
                                <label class="relative flex flex-col items-center gap-2 rounded-xl border-2 bg-white px-4 py-5 text-sm font-medium text-gray-700 cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition-all has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700">
                                    <input type="radio" name="type_activite" value="autre" x-model="typeActivite"
                                           class="sr-only">
                                    <span class="flex items-center justify-center w-12 h-12 rounded-full bg-purple-100 text-purple-600">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                    </span>
                                    <span class="font-semibold">Autre</span>
                                    <span class="text-xs text-gray-500 text-center">Formation, suivi, divers</span>
                                </label>
                            </div>
                            @error('type_activite') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div x-show="!canChooseActivite" class="md:col-span-2">
                            <div class="rounded-md bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-700">
                                <strong>Type d'activité :</strong> 
                                <span x-text="typeActivite === 'culture' ? 'Culture' : (typeActivite === 'elevage' ? 'Élevage' : 'Autre')"></span>
                                <p class="text-xs text-blue-600 mt-1">Ce technicien est spécialisé dans ce type d'activité.</p>
                            </div>
                            <input type="hidden" name="type_activite" :value="typeActivite">
                        </div>
                        <div>
                            <label for="conditions_meteo" class="block text-sm font-medium text-gray-700 mb-1.5">Conditions météo</label>
                            <x-select 
                                name="conditions_meteo" 
                                label="Conditions météo" 
                                :options="$conditionMeteoOptions" 
                                :value="$rapport->conditions_meteo?->value ?? ''"
                                placeholder="Sélectionner" 
                                error="conditions_meteo"
                            />
                            @error('conditions_meteo') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="superficie_visitee_ha" class="block text-sm font-medium text-gray-700 mb-1.5">Superficie visitée (ha)</label>
                            <input type="number" step="0.01" min="0" id="superficie_visitee_ha" name="superficie_visitee_ha"
                                   value="{{ old('superficie_visitee_ha', $rapport->superficie_visitee_ha ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0.00">
                            @error('superficie_visitee_ha') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="duree_visite" class="block text-sm font-medium text-gray-700 mb-1.5">Durée de visite</label>
                            <x-select 
                                name="duree_visite" 
                                label="Durée de visite" 
                                :options="$dureeVisiteOptions" 
                                :value="$rapport->duree_visite?->value ?? ''"
                                placeholder="Sélectionner" 
                                error="duree_visite"
                            />
                            @error('duree_visite') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="gps_latitude" class="block text-sm font-medium text-gray-700 mb-1.5">Latitude</label>
                            <input type="text" id="gps_latitude" name="gps_latitude"
                                   value="{{ old('gps_latitude', $rapport->gps_latitude ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: 5.3361">
                            @error('gps_latitude') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="gps_longitude" class="block text-sm font-medium text-gray-700 mb-1.5">Longitude</label>
                            <input type="text" id="gps_longitude" name="gps_longitude"
                                   value="{{ old('gps_longitude', $rapport->gps_longitude ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: -4.0202">
                            @error('gps_longitude') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Étape 2 : Cultures (visible si type_activite = culture) --}}
            <div x-show="currentStep === 1 && typeActivite === 'culture'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Étape 2 - Cultures</h2>
                    <p class="text-xs text-gray-500 mt-1">Types de cultures, état végétatif, ravageurs et sol</p>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Types de cultures présentes</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach(['Cacao', 'Anacarde (noix de cajou)', 'Hévéa', 'Café', 'Plantain / banane', 'Manioc', 'Maïs', 'Maraîchage', 'Riz'] as $culture)
                                <label class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 cursor-pointer hover:bg-gray-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700 transition-colors">
                                    <input type="checkbox" name="cultures_presentes[]" value="{{ $culture }}" 
                                           {{ in_array($culture, old('cultures_presentes', $visiteCulture->cultures_presentes ?? [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                                    {{ $culture }}
                                </label>
                            @endforeach
                        </div>
                        @error('cultures_presentes') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="culture_autre_precision" class="block text-sm font-medium text-gray-700 mb-1.5">Autre culture (précision)</label>
                        <input type="text" id="culture_autre_precision" name="culture_autre_precision"
                               value="{{ old('culture_autre_precision', $visiteCulture->culture_autre_precision ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Précisez si 'Autre'">
                        @error('culture_autre_precision') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="stade_phenologique" class="block text-sm font-medium text-gray-700 mb-1.5">Stade phénologique</label>
                            <x-select 
                                name="stade_phenologique" 
                                label="Stade phénologique" 
                                :options="$stadePhenologiqueOptions" 
                                :value="$visiteCulture->stade_phenologique?->value ?? ''"
                                placeholder="Sélectionner" 
                                error="stade_phenologique"
                            />
                            @error('stade_phenologique') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="avancement_cycle_pourcent" class="block text-sm font-medium text-gray-700 mb-1.5">Avancement du cycle (%)</label>
                            <input type="number" min="0" max="100" id="avancement_cycle_pourcent" name="avancement_cycle_pourcent"
                                   value="{{ old('avancement_cycle_pourcent', $visiteCulture->avancement_cycle_pourcent ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0-100">
                            @error('avancement_cycle_pourcent') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="etat_couvert_vegetal" class="block text-sm font-medium text-gray-700 mb-1.5">État général du couvert végétal (1-5)</label>
                            <input type="number" min="1" max="5" id="etat_couvert_vegetal" name="etat_couvert_vegetal"
                                   value="{{ old('etat_couvert_vegetal', $visiteCulture->etat_couvert_vegetal ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="1-5">
                            @error('etat_couvert_vegetal') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="ravageur_autre_precision" class="block text-sm font-medium text-gray-700 mb-1.5">Autre ravageur/maladie (précision)</label>
                            <input type="text" id="ravageur_autre_precision" name="ravageur_autre_precision"
                                   value="{{ old('ravageur_autre_precision', $visiteCulture->ravageur_autre_precision ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Précisez si 'Autre'">
                            @error('ravageur_autre_precision') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="niveau_infestation" class="block text-sm font-medium text-gray-700 mb-1.5">Niveau d'infestation (1-5)</label>
                            <input type="number" min="1" max="5" id="niveau_infestation" name="niveau_infestation"
                                   value="{{ old('niveau_infestation', $visiteCulture->niveau_infestation ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="1-5">
                            @error('niveau_infestation') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="etat_hydrique_sol" class="block text-sm font-medium text-gray-700 mb-1.5">État hydrique du sol</label>
                            <input type="text" id="etat_hydrique_sol" name="etat_hydrique_sol"
                                   value="{{ old('etat_hydrique_sol', $visiteCulture->etat_hydrique_sol ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Humide, sec, détrempé...">
                            @error('etat_hydrique_sol') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="irrigation_en_place" class="block text-sm font-medium text-gray-700 mb-1.5">Irrigation en place</label>
                            <select id="irrigation_en_place" name="irrigation_en_place"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                <option value="1" {{ old('irrigation_en_place', $visiteCulture->irrigation_en_place ?? '') == '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ old('irrigation_en_place', $visiteCulture->irrigation_en_place ?? '') === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                            @error('irrigation_en_place') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="etat_structure_sol" class="block text-sm font-medium text-gray-700 mb-1.5">Structure du sol</label>
                            <input type="text" id="etat_structure_sol" name="etat_structure_sol"
                                   value="{{ old('etat_structure_sol', $visiteCulture->etat_structure_sol ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Meuble, compact, sableux...">
                            @error('etat_structure_sol') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="ph_sol" class="block text-sm font-medium text-gray-700 mb-1.5">pH du sol</label>
                            <input type="number" step="0.1" min="0" max="14" id="ph_sol" name="ph_sol"
                                   value="{{ old('ph_sol', $visiteCulture->ph_sol ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0.0 - 14.0">
                            @error('ph_sol') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="intrants_utilises" class="block text-sm font-medium text-gray-700 mb-1.5">Intrants utilisés</label>
                            <input type="text" id="intrants_utilises" name="intrants_utilises"
                                   value="{{ old('intrants_utilises', $visiteCulture->intrants_utilises ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Engrais NPK, insecticide...">
                            @error('intrants_utilises') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="estimation_recolte_kg" class="block text-sm font-medium text-gray-700 mb-1.5">Estimation récolte (kg)</label>
                            <input type="number" step="0.01" min="0" id="estimation_recolte_kg" name="estimation_recolte_kg"
                                   value="{{ old('estimation_recolte_kg', $visiteCulture->estimation_recolte_kg ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0.00">
                            @error('estimation_recolte_kg') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="date_estimee_recolte" class="block text-sm font-medium text-gray-700 mb-1.5">Date estimée de récolte</label>
                            <input type="date" id="date_estimee_recolte" name="date_estimee_recolte"
                                   value="{{ old('date_estimee_recolte', $visiteCulture->date_estimee_recolte ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            @error('date_estimee_recolte') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label for="observations_ravageurs" class="block text-sm font-medium text-gray-700 mb-1.5">Observations sur les ravageurs/maladies</label>
                        <textarea id="observations_ravageurs" name="observations_ravageurs" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails des observations...">{{ old('observations_ravageurs', $visiteCulture->observations_ravageurs ?? '') }}</textarea>
                        @error('observations_ravageurs') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Étape 2 : Élevage (visible si type_activite = elevage) --}}
            <div x-show="currentStep === 1 && typeActivite === 'elevage'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Étape 2 - Élevage</h2>
                    <p class="text-xs text-gray-500 mt-1">Animaux, santé, soins et performances</p>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Types d'animaux présents</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach(['Bovins', 'Ovins', 'Caprins', 'Porcins', 'Volaille', 'Lapins', 'Pisciculture'] as $animal)
                                <label class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 cursor-pointer hover:bg-gray-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700 transition-colors">
                                    <input type="checkbox" name="animaux_presents[]" value="{{ $animal }}"
                                           {{ in_array($animal, old('animaux_presents', $visiteElevage->animaux_presents ?? [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                                    {{ $animal }}
                                </label>
                            @endforeach
                        </div>
                        @error('animaux_presents') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="animal_autre_precision" class="block text-sm font-medium text-gray-700 mb-1.5">Autre animal (précision)</label>
                        <input type="text" id="animal_autre_precision" name="animal_autre_precision"
                               value="{{ old('animal_autre_precision', $visiteElevage->animal_autre_precision ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Précisez si 'Autre'">
                        @error('animal_autre_precision') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="effectif_total" class="block text-sm font-medium text-gray-700 mb-1.5">Effectif total</label>
                            <input type="number" min="0" id="effectif_total" name="effectif_total"
                                   value="{{ old('effectif_total', $visiteElevage->effectif_total ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0">
                            @error('effectif_total') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="mortalite_constatee" class="block text-sm font-medium text-gray-700 mb-1.5">Mortalité constatée</label>
                            <input type="number" min="0" id="mortalite_constatee" name="mortalite_constatee"
                                   value="{{ old('mortalite_constatee', $visiteElevage->mortalite_constatee ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0">
                            @error('mortalite_constatee') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="naissances_depuis_derniere_visite" class="block text-sm font-medium text-gray-700 mb-1.5">Naissances depuis dernière visite</label>
                            <input type="number" min="0" id="naissances_depuis_derniere_visite" name="naissances_depuis_derniere_visite"
                                   value="{{ old('naissances_depuis_derniere_visite', $visiteElevage->naissances_depuis_derniere_visite ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0">
                            @error('naissances_depuis_derniere_visite') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="ventes_abattages_depuis_derniere_visite" class="block text-sm font-medium text-gray-700 mb-1.5">Ventes/abattages depuis dernière visite</label>
                            <input type="number" min="0" id="ventes_abattages_depuis_derniere_visite" name="ventes_abattages_depuis_derniere_visite"
                                   value="{{ old('ventes_abattages_depuis_derniere_visite', $visiteElevage->ventes_abattages_depuis_derniere_visite ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0">
                            @error('ventes_abattages_depuis_derniere_visite') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="etat_corporel_general" class="block text-sm font-medium text-gray-700 mb-1.5">État corporel général (1-5)</label>
                            <input type="number" min="1" max="5" id="etat_corporel_general" name="etat_corporel_general"
                                   value="{{ old('etat_corporel_general', $visiteElevage->etat_corporel_general ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="1-5">
                            @error('etat_corporel_general') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="signe_autre_precision" class="block text-sm font-medium text-gray-700 mb-1.5">Autre signe clinique (précision)</label>
                            <input type="text" id="signe_autre_precision" name="signe_autre_precision"
                                   value="{{ old('signe_autre_precision', $visiteElevage->signe_autre_precision ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Précisez si 'Autre'">
                            @error('signe_autre_precision') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="produits_administres" class="block text-sm font-medium text-gray-700 mb-1.5">Produits administrés</label>
                            <input type="text" id="produits_administres" name="produits_administres"
                                   value="{{ old('produits_administres', $visiteElevage->produits_administres ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Vaccins, antibiotiques...">
                            @error('produits_administres') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="etat_alimentation" class="block text-sm font-medium text-gray-700 mb-1.5">État de l'alimentation</label>
                            <input type="text" id="etat_alimentation" name="etat_alimentation"
                                   value="{{ old('etat_alimentation', $visiteElevage->etat_alimentation ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Bon, moyen, insuffisant...">
                            @error('etat_alimentation') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="eau_abreuvement" class="block text-sm font-medium text-gray-700 mb-1.5">Eau d'abreuvement</label>
                            <input type="text" id="eau_abreuvement" name="eau_abreuvement"
                                   value="{{ old('eau_abreuvement', $visiteElevage->eau_abreuvement ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="Ex: Propre, insuffisante...">
                            @error('eau_abreuvement') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="etat_batiments_enclos" class="block text-sm font-medium text-gray-700 mb-1.5">État bâtiments/enclos (1-5)</label>
                            <input type="number" min="1" max="5" id="etat_batiments_enclos" name="etat_batiments_enclos"
                                   value="{{ old('etat_batiments_enclos', $visiteElevage->etat_batiments_enclos ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="1-5">
                            @error('etat_batiments_enclos') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="production_laitiere_l_j" class="block text-sm font-medium text-gray-700 mb-1.5">Production laitière (L/j)</label>
                            <input type="number" step="0.01" min="0" id="production_laitiere_l_j" name="production_laitiere_l_j"
                                   value="{{ old('production_laitiere_l_j', $visiteElevage->production_laitiere_l_j ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0.00">
                            @error('production_laitiere_l_j') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="production_oeufs_nb_j" class="block text-sm font-medium text-gray-700 mb-1.5">Production œufs (nb/j)</label>
                            <input type="number" min="0" id="production_oeufs_nb_j" name="production_oeufs_nb_j"
                                   value="{{ old('production_oeufs_nb_j', $visiteElevage->production_oeufs_nb_j ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0">
                            @error('production_oeufs_nb_j') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="gain_poids_kg_mois" class="block text-sm font-medium text-gray-700 mb-1.5">Gain de poids (kg/mois)</label>
                            <input type="number" step="0.01" min="0" id="gain_poids_kg_mois" name="gain_poids_kg_mois"
                                   value="{{ old('gain_poids_kg_mois', $visiteElevage->gain_poids_kg_mois ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0.00">
                            @error('gain_poids_kg_mois') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label for="observations_sanitaires" class="block text-sm font-medium text-gray-700 mb-1.5">Observations sanitaires</label>
                        <textarea id="observations_sanitaires" name="observations_sanitaires" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails des observations sanitaires...">{{ old('observations_sanitaires', $visiteElevage->observations_sanitaires ?? '') }}</textarea>
                        @error('observations_sanitaires') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Étape 2 : Autre (visible si type_activite = autre) --}}
            <div x-show="currentStep === 1 && typeActivite === 'autre'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Étape 2 - Autre activité</h2>
                    <p class="text-xs text-gray-500 mt-1">Détails de l'activité réalisée</p>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div>
                        <label for="type_autre" class="block text-sm font-medium text-gray-700 mb-1.5">Type d'activité</label>
                        <input type="text" id="type_autre" name="type_autre"
                               value="{{ old('type_autre', $visiteAutre->type_autre ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Formation, suivi administratif, visite de courtoisie...">
                        @error('type_autre') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="description_activite" class="block text-sm font-medium text-gray-700 mb-1.5">Description de l'activité</label>
                        <textarea id="description_activite" name="description_activite" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Décrivez l'activité réalisée...">{{ old('description_activite', $visiteAutre->description_activite ?? '') }}</textarea>
                        @error('description_activite') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="observations_specifiques" class="block text-sm font-medium text-gray-700 mb-1.5">Observations spécifiques</label>
                        <textarea id="observations_specifiques" name="observations_specifiques" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Observations particulières...">{{ old('observations_specifiques', $visiteAutre->observations_specifiques ?? '') }}</textarea>
                        @error('observations_specifiques') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Étape 3 : Observations finales --}}
            <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Étape 3 - Observations finales</h2>
                    <p class="text-xs text-gray-500 mt-1">Résumé, alertes et prochaine visite</p>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div class="md:col-span-2">
                        <label for="resume_visite" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Résumé de la visite <span class="text-red-600">*</span>
                        </label>
                        <textarea id="resume_visite" name="resume_visite" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Résumé des observations et activités de la visite...">{{ old('resume_visite', $rapport->resume_visite ?? '') }}</textarea>
                        @error('resume_visite') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="niveau_alerte" class="block text-sm font-medium text-gray-700 mb-1.5">Niveau d'alerte</label>
                        <x-select 
                            name="niveau_alerte" 
                            label="Niveau d'alerte" 
                            :options="[['value' => 'aucune', 'label' => 'Aucune'], ['value' => 'faible', 'label' => 'Faible'], ['value' => 'moderee', 'label' => 'Modérée'], ['value' => 'urgente', 'label' => 'Urgente']]" 
                            :value="$rapport->niveau_alerte ?? ''"
                            placeholder="Sélectionner" 
                            error="niveau_alerte"
                        />
                        @error('niveau_alerte') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="description_alerte" class="block text-sm font-medium text-gray-700 mb-1.5">Description de l'alerte</label>
                        <textarea id="description_alerte" name="description_alerte" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails de l'alerte si nécessaire...">{{ old('description_alerte', $rapport->description_alerte ?? '') }}</textarea>
                        @error('description_alerte') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="prochaine_visite_date" class="block text-sm font-medium text-gray-700 mb-1.5">Prochaine visite (date)</label>
                        <input type="date" id="prochaine_visite_date" name="prochaine_visite_date"
                               value="{{ old('prochaine_visite_date', $rapport->prochaine_visite_date ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        @error('prochaine_visite_date') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="prochaine_visite_raison" class="block text-sm font-medium text-gray-700 mb-1.5">Prochaine visite (raison)</label>
                        <input type="text" id="prochaine_visite_raison" name="prochaine_visite_raison"
                               value="{{ old('prochaine_visite_raison', $rapport->prochaine_visite_raison ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Raison de la prochaine visite">
                        @error('prochaine_visite_raison') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Étape 4 : Résumé & Envoi --}}
            <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Étape 4 - Résumé & Envoi</h2>
                    <p class="text-xs text-gray-500 mt-1">Notes, messages et statut du rapport</p>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div>
                        <label for="note_interne" class="block text-sm font-medium text-gray-700 mb-1.5">Note interne (Admin uniquement)</label>
                        <textarea id="note_interne" name="note_interne" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Notes internes visibles uniquement par l'administrateur...">{{ old('note_interne', $rapport->note_interne ?? '') }}</textarea>
                        @error('note_interne') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="message_client" class="block text-sm font-medium text-gray-700 mb-1.5">Message au client</label>
                        <textarea id="message_client" name="message_client" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Message visible par le client après validation...">{{ old('message_client', $rapport->message_client ?? '') }}</textarea>
                        @error('message_client') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                        <x-select 
                            name="statut" 
                            label="Statut" 
                            :options="$statutRapportOptions" 
                            :value="$rapport->statut?->value ?? ''"
                            placeholder="Sélectionner" 
                            error="statut"
                        />
                        @error('statut') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
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
                    <a href="{{ route('admin.rapports-visite.index') }}"
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
                            {{ $isEdit ? 'Mettre à jour' : 'Créer le rapport' }}
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function rapportWizard() {
        return {
            currentStep: 0,
            typeActivite: '{{ $typeActiviteValue }}',
            technicienId: '{{ old('technicien_id', $rapport->technicien_id ?? ($defaultTechnicienId ?? '')) }}',
            canChooseActivite: true,
            steps: [
                { label: 'Informations générales', completed: false },
                { label: 'Activité', completed: false },
                { label: 'Observations', completed: false },
                { label: 'Résumé & Envoi', completed: false },
            ],
            init() {
                // Déterminer si le technicien peut choisir l'activité
                this.updateTypeActiviteFromTechnicien();
            },
            updateTypeActiviteFromTechnicien() {
                const select = document.getElementById('technicien_id');
                if (!select) return;
                
                const selectedOption = select.options[select.selectedIndex];
                if (!selectedOption) return;
                
                const typeTechnicien = selectedOption.dataset.typeTechnicien || '';
                const role = selectedOption.dataset.role || '';
                
                // Admin peut tout faire
                if (role === 'admin') {
                    this.canChooseActivite = true;
                    return;
                }
                
                // Technicien spécialisé
                if (typeTechnicien === 'culture') {
                    this.canChooseActivite = false;
                    this.typeActivite = 'culture';
                } else if (typeTechnicien === 'elevage') {
                    this.canChooseActivite = false;
                    this.typeActivite = 'elevage';
                } else if (typeTechnicien === 'les_deux') {
                    this.canChooseActivite = true;
                } else {
                    // Pas de type défini, on laisse choisir
                    this.canChooseActivite = true;
                }
            },
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

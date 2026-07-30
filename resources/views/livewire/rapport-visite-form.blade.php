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
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner un technicien</option>
                                @foreach($techniciens as $technicien)
                                    <option value="{{ $technicien->id }}" {{ (old('technicien_id', $rapport->technicien_id ?? '') == $technicien->id) ? 'selected' : '' }}>{{ $technicien->name }}</option>
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
                            <select id="client_id" name="client_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner un client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ (old('client_id', $rapport->client_id ?? '') == $client->id) ? 'selected' : '' }}>{{ $client->user?->name ?? $client->nom ?? 'Client #' . $client->id }}</option>
                                @endforeach
                            </select>
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
                            <select id="type_visite" name="type_visite" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                @foreach(\App\Enums\RapportVisiteType::cases() as $option)
                                    <option value="{{ $option->value }}" {{ (old('type_visite', $rapport->type_visite ?? '') === $option->value) ? 'selected' : '' }}>{{ $option->label() }}</option>
                                @endforeach
                            </select>
                            @error('type_visite') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="conditions_meteo" class="block text-sm font-medium text-gray-700 mb-1.5">Conditions météo</label>
                            <select id="conditions_meteo" name="conditions_meteo"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                @foreach(\App\Enums\ConditionMeteo::cases() as $option)
                                    <option value="{{ $option->value }}" {{ (old('conditions_meteo', $rapport->conditions_meteo ?? '') == $option->value) ? 'selected' : '' }}>{{ $option->label() }}</option>
                                @endforeach
                            </select>
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
                            <select id="duree_visite" name="duree_visite"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                @foreach(\App\Enums\DureeVisite::cases() as $option)
                                    <option value="{{ $option->value }}" {{ (old('duree_visite', $rapport->duree_visite ?? '') == $option->value) ? 'selected' : '' }}>{{ $option->label() }}</option>
                                @endforeach
                            </select>
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

            {{-- Étape 2 : Cultures --}}
            <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
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
                                           {{ in_array($culture, old('cultures_presentes', $rapport->cultures_presentes ?? [])) ? 'checked' : '' }}
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
                               value="{{ old('culture_autre_precision', $rapport->culture_autre_precision ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Précisez si 'Autre'">
                        @error('culture_autre_precision') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="stade_phenologique" class="block text-sm font-medium text-gray-700 mb-1.5">Stade phénologique</label>
                            <select id="stade_phenologique" name="stade_phenologique"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <option value="">Sélectionner</option>
                                @foreach(\App\Enums\StadePhenologique::cases() as $option)
                                    <option value="{{ $option->value }}" {{ (old('stade_phenologique', $rapport->stade_phenologique ?? '') == $option->value) ? 'selected' : '' }}>{{ $option->label() }}</option>
                                @endforeach
                            </select>
                            @error('stade_phenologique') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="avancement_cycle_pourcent" class="block text-sm font-medium text-gray-700 mb-1.5">Avancement du cycle (%)</label>
                            <input type="number" min="0" max="100" id="avancement_cycle_pourcent" name="avancement_cycle_pourcent"
                                   value="{{ old('avancement_cycle_pourcent', $rapport->avancement_cycle_pourcent ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0-100">
                            @error('avancement_cycle_pourcent') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="etat_couvert_vegetal" class="block text-sm font-medium text-gray-700 mb-1.5">État général du couvert végétal (1-5)</label>
                            <input type="number" min="1" max="5" id="etat_couvert_vegetal" name="etat_couvert_vegetal"
                                   value="{{ old('etat_couvert_vegetal', $rapport->etat_couvert_vegetal ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="1-5">
                            @error('etat_couvert_vegetal') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Étape 3 : Élevage --}}
            <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Étape 3 - Élevage</h2>
                    <p class="text-xs text-gray-500 mt-1">Animaux, santé, soins et performances</p>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Types d'animaux présents</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach(['Bovins', 'Ovins', 'Caprins', 'Porcins', 'Volaille', 'Lapins', 'Pisciculture'] as $animal)
                                <label class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 cursor-pointer hover:bg-gray-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700 transition-colors">
                                    <input type="checkbox" name="animaux_presents[]" value="{{ $animal }}"
                                           {{ in_array($animal, old('animaux_presents', $rapport->animaux_presents ?? [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                                    {{ $animal }}
                                </label>
                            @endforeach
                        </div>
                        @error('animaux_presents') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="effectif_total" class="block text-sm font-medium text-gray-700 mb-1.5">Effectif total</label>
                            <input type="number" min="0" id="effectif_total" name="effectif_total"
                                   value="{{ old('effectif_total', $rapport->effectif_total ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0">
                            @error('effectif_total') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="mortalite_constatee" class="block text-sm font-medium text-gray-700 mb-1.5">Mortalité constatée</label>
                            <input type="number" min="0" id="mortalite_constatee" name="mortalite_constatee"
                                   value="{{ old('mortalite_constatee', $rapport->mortalite_constatee ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                                   placeholder="0">
                            @error('mortalite_constatee') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Étape 4 : Observations finales --}}
            <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Étape 4 - Observations finales</h2>
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
                        <select id="niveau_alerte" name="niveau_alerte"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            @foreach(['aucune', 'faible', 'moderee', 'urgente'] as $niveau)
                                <option value="{{ $niveau }}" {{ (old('niveau_alerte', $rapport->niveau_alerte ?? 'aucune') == $niveau) ? 'selected' : '' }}>
                                    {{ ucfirst($niveau) }}
                                </option>
                            @endforeach
                        </select>
                        @error('niveau_alerte') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="description_alerte" class="block text-sm font-medium text-gray-700 mb-1.5">Description de l'alerte</label>
                        <textarea id="description_alerte" name="description_alerte" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Détails de l'alerte si nécessaire...">{{ old('description_alerte', $rapport->description_alerte ?? '') }}</textarea>
                        @error('description_alerte') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Étape 5 : Résumé & Envoi --}}
            <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Étape 5 - Résumé & Envoi</h2>
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
                        <select id="statut" name="statut"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            @foreach(['brouillon', 'en_attente_validation', 'valide', 'rejete'] as $statut)
                                <option value="{{ $statut }}" {{ (old('statut', $rapport->statut ?? 'brouillon') == $statut) ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $statut)) }}
                                </option>
                            @endforeach
                        </select>
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
            steps: [
                { label: 'Informations générales', completed: false },
                { label: 'Cultures', completed: false },
                { label: 'Élevage', completed: false },
                { label: 'Observations', completed: false },
                { label: 'Résumé & Envoi', completed: false },
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
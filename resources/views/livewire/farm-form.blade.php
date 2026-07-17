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
            <h1 class="text-2xl font-bold text-gray-900">{{ $isEdit ? 'Modifier l\'Exploitation' : 'Nouvelle Exploitation' }}</h1>
            <p class="mt-1 text-gray-500">{{ $isEdit ? 'Modifiez les informations de l\'exploitation agricole.' : 'Créez une nouvelle exploitation agricole.' }}</p>
        </div>

        {{-- Form --}}
        <form wire:submit="submit" class="bg-white border border-gray-200 rounded-lg shadow-sm">

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
                        <input type="text" id="name" wire:model="name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Ferme Kouassi">
                        @error('name') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Propriétaire <span class="text-red-600">*</span>
                        </label>
                        <select id="user_id" wire:model="user_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">Sélectionner un client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ $user_id == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                            @endforeach
                        </select>
                        @error('user_id') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Localisation <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="location" wire:model="location" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Abidjan, Côte d'Ivoire">
                        @error('location') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="culture_type" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Type de culture <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="culture_type" wire:model="culture_type" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Maïs, Manioc, Riz...">
                        @error('culture_type') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="total_area_hectares" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Surface (ha) <span class="text-red-600">*</span>
                        </label>
                        <input type="number" step="0.01" min="0" id="total_area_hectares" wire:model="total_area_hectares" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="0.00">
                        @error('total_area_hectares') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Statut <span class="text-red-600">*</span>
                        </label>
                        <select id="status" wire:model="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactif</option>
                            <option value="fallow" {{ $status === 'fallow' ? 'selected' : '' }}>En jachère</option>
                        </select>
                        @error('status') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 2 : Coordonnées GPS --}}
            <div class="px-6 py-5 border-b border-gray-100 border-t border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Coordonnées GPS</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1.5">Latitude</label>
                        <input type="number" step="0.0000001" id="latitude" wire:model="latitude"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="5.3361">
                        @error('latitude') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1.5">Longitude</label>
                        <input type="number" step="0.0000001" id="longitude" wire:model="longitude"
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
                        <input type="text" id="crop_stage" wire:model="crop_stage"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Semis, Croissance, Récolte...">
                        @error('crop_stage') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="crop_stage_progress" class="block text-sm font-medium text-gray-700 mb-1.5">Progression (%)</label>
                        <input type="number" min="0" max="100" id="crop_stage_progress" wire:model="crop_stage_progress"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="0">
                        @error('crop_stage_progress') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="expected_harvest_date" class="block text-sm font-medium text-gray-700 mb-1.5">Date de récolte prévue</label>
                        <input type="date" id="expected_harvest_date" wire:model="expected_harvest_date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        @error('expected_harvest_date') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="last_visit_date" class="block text-sm font-medium text-gray-700 mb-1.5">Dernière visite</label>
                        <input type="date" id="last_visit_date" wire:model="last_visit_date"
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
                    <textarea id="notes" wire:model="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                              placeholder="Informations complémentaires..."></textarea>
                    @error('notes') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Footer actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex items-center justify-end gap-3">
                <a href="{{ route('admin.farms.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed"
                        class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 active:bg-blue-800 transition-colors">
                    <span wire:loading.remove>{{ $isEdit ? 'Mettre à jour' : 'Créer l\'exploitation' }}</span>
                    <span wire:loading>{{ $isEdit ? 'Mise à jour...' : 'Création...' }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
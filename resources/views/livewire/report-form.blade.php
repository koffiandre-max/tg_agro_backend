<div class="min-h-screen bg-gray-100 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ url()->previous() }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Nouveau Rapport</h1>
            <p class="mt-1 text-gray-500">Créez un nouveau rapport pour une exploitation.</p>
        </div>

        {{-- Affichage des erreurs de validation --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Le formulaire contient des erreurs</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="bg-white border border-gray-200 rounded-lg shadow-sm">
            @csrf

            {{-- Section 1 : Informations générales --}}
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Informations générales</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Titre <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Rapport d'inspection mensuel">
                        @error('title') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Type <span class="text-red-600">*</span>
                        </label>
                        <select id="type" name="type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">Sélectionner un type</option>
                            <option value="inspection" {{ old('type') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                            <option value="diagnostic" {{ old('type') == 'diagnostic' ? 'selected' : '' }}>Diagnostic</option>
                            <option value="suivi" {{ old('type') == 'suivi' ? 'selected' : '' }}>Suivi</option>
                            <option value="monthly" {{ old('type') == 'monthly' ? 'selected' : '' }}>Mensuel</option>
                            <option value="soil_analysis" {{ old('type') == 'soil_analysis' ? 'selected' : '' }}>Analyse de sol</option>
                            <option value="harvest" {{ old('type') == 'harvest' ? 'selected' : '' }}>Récolte</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('type') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="farm_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Exploitation <span class="text-red-600">*</span>
                        </label>
                        <select id="farm_id" name="farm_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">Sélectionner une exploitation</option>
                            @foreach($farms as $farm)
                                <option value="{{ $farm->id }}" {{ old('farm_id') == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                            @endforeach
                        </select>
                        @error('farm_id') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Client <span class="text-red-600">*</span>
                        </label>
                        <select id="client_id" name="client_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">Sélectionner un client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->user->name ?? 'Client #'.$client->id }}</option>
                            @endforeach
                        </select>
                        @error('client_id') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 2 : Fichier --}}
            <div class="px-6 py-5 border-b border-gray-100 border-t border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Fichier</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Fichier PDF <span class="text-red-600">*</span>
                        </label>
                        <input type="file" id="file" name="file" accept=".pdf" required
                               class="block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100
                                      border border-gray-300 rounded-md
                                      focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        @error('file') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Notes
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                              placeholder="Informations complémentaires...">{{ old('notes') }}</textarea>
                    @error('notes') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Footer actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex items-center justify-end gap-3">
                <button type="button" onclick="window.history.back()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                    Annuler
                </button>
                <button type="submit"
                        class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 active:bg-blue-800 transition-colors">
                    Créer le rapport
                </button>
            </div>
        </form>
    </div>
</div>
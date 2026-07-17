<div class="bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('admin.clients.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour à la liste
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Nouveau Client</h1>
            <p class="mt-1 text-gray-500">Créez un nouveau compte client et ses informations.</p>
        </div>

        {{-- Form --}}
        <form wire:submit="submit" class="bg-white border border-gray-200 rounded-lg shadow-sm">

            {{-- Section 1 : Identifiants de connexion --}}
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Identifiants de connexion</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nom complet <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="name" wire:model="name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Kouassi Jean">
                        @error('name') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Adresse email <span class="text-red-600">*</span>
                        </label>
                        <input type="email" id="email" wire:model="email" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="jean.kouassi@exemple.com">
                        @error('email') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
                        <input type="tel" id="phone" wire:model="phone"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="+225 01 00 00 00 00">
                        @error('phone') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Mot de passe <span class="text-red-600">*</span>
                        </label>
                        <input type="password" id="password" wire:model="password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="••••••••">
                        @error('password') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Confirmer le mot de passe <span class="text-red-600">*</span>
                        </label>
                        <input type="password" id="password_confirmation" wire:model="password_confirmation" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="••••••••">
                        @error('password_confirmation') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 2 : Informations client --}}
            <div class="px-6 py-5 border-b border-gray-100 border-t border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Informations client</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="country_of_residence" class="block text-sm font-medium text-gray-700 mb-1.5">Pays de résidence</label>
                        <input type="text" id="country_of_residence" wire:model="country_of_residence"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Côte d'Ivoire">
                        @error('country_of_residence') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="country_of_origin" class="block text-sm font-medium text-gray-700 mb-1.5">Pays d'origine</label>
                        <input type="text" id="country_of_origin" wire:model="country_of_origin"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Mali">
                        @error('country_of_origin') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="city_of_residence" class="block text-sm font-medium text-gray-700 mb-1.5">Ville de résidence</label>
                        <input type="text" id="city_of_residence" wire:model="city_of_residence"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Abidjan">
                        @error('city_of_residence') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="subscription_type" class="block text-sm font-medium text-gray-700 mb-1.5">Type d'abonnement</label>
                        <select id="subscription_type" wire:model="subscription_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="basic">Basique</option>
                            <option value="standard">Standard</option>
                            <option value="premium">Premium</option>
                        </select>
                        @error('subscription_type') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="subscription_expires_at" class="block text-sm font-medium text-gray-700 mb-1.5">Expiration abonnement</label>
                        <input type="date" id="subscription_expires_at" wire:model="subscription_expires_at"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        @error('subscription_expires_at') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="total_investment" class="block text-sm font-medium text-gray-700 mb-1.5">Investissement total (€)</label>
                        <input type="number" step="0.01" min="0" id="total_investment" wire:model="total_investment"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="0.00">
                        @error('total_investment') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1.5">Notes</label>
                        <textarea id="notes" wire:model="notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 resize-y"
                                  placeholder="Informations complémentaires..."></textarea>
                        @error('notes') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Footer actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex items-center justify-end gap-3">
                <a href="{{ route('admin.clients.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed"
                        class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 active:bg-blue-800 transition-colors">
                    <span wire:loading.remove>Créer le client</span>
                    <span wire:loading>Création en cours...</span>
                </button>
            </div>
        </form>
    </div>
</div>

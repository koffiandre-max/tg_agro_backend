<div class=" bg-gray-50 min-h-screen" x-data="{ openFilter: null }" @click.away="openFilter = null">

    {{-- Barre de recherche + filtres --}}
    <div class="flex flex-wrap items-center gap-3 mb-4">

        {{-- Recherche --}}
        <div class="relative flex-1 min-w-[240px]">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.4 4.4a7.5 7.5 0 0012.25 12.25z" />
            </svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Rechercher par nom, email, pays, code..."
                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
        </div>

        {{-- Recherche par code --}}
        <div class="relative min-w-[160px]">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-4m0 0l4-4m-4 4l-4 4m4-4l4 4M3 4h18M4 4h16v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
            </svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="code"
                placeholder="Code client..."
                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
        </div>

        {{-- Filtre Abonnement --}}
        <div class="relative">
            <button @click="openFilter = openFilter === 'subscription' ? null : 'subscription'"
                    class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white hover:bg-gray-50">
                Abonnement <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="openFilter === 'subscription'" x-cloak class="absolute z-20 mt-1 w-40 bg-white border border-gray-200 rounded-lg shadow-lg py-1">
                @foreach(['' => 'Tous', 'basic' => 'Basic', 'standard' => 'Standard', 'premium' => 'Premium'] as $value => $label)
                    <button wire:click="$set('subscriptionType', '{{ $value }}')" @click="openFilter = null"
                            class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-50 {{ $subscriptionType === $value ? 'text-indigo-600 font-medium' : 'text-gray-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Filtre Pays de Résidence --}}
        <div class="relative">
            <button @click="openFilter = openFilter === 'country' ? null : 'country'"
                    class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white hover:bg-gray-50">
                Pays <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="openFilter === 'country'" x-cloak class="absolute z-20 mt-1 w-48 max-h-64 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-lg py-1">
                <button wire:click="$set('countryOfResidence', '')" @click="openFilter = null"
                        class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-50 {{ $countryOfResidence === '' ? 'text-indigo-600 font-medium' : 'text-gray-700' }}">
                    Tous
                </button>
                @foreach($countries as $country)
                    <button wire:click="$set('countryOfResidence', '{{ $country }}')" @click="openFilter = null"
                            class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-50 {{ $countryOfResidence === $country ? 'text-indigo-600 font-medium' : 'text-gray-700' }}">
                        {{ $country }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Réinitialiser --}}
        @if($search || $subscriptionType || $countryOfResidence)
            <button wire:click="resetFilters" class="text-sm text-indigo-600 hover:underline">Réinitialiser</button>
        @endif
    </div>

    {{-- Tableau --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/60 text-left text-gray-500">
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('user_id')">
                            <div class="flex items-center gap-1">Client @include('livewire.partials.sort-icon', ['field' => 'user_id'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('code')">
                            <div class="flex items-center gap-1">Code @include('livewire.partials.sort-icon', ['field' => 'code'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('country_of_residence')">
                            <div class="flex items-center gap-1">Pays résidence @include('livewire.partials.sort-icon', ['field' => 'country_of_residence'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('country_of_origin')">
                            <div class="flex items-center gap-1">Pays origine @include('livewire.partials.sort-icon', ['field' => 'country_of_origin'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('subscription_type')">
                            <div class="flex items-center gap-1">Abonnement @include('livewire.partials.sort-icon', ['field' => 'subscription_type'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('subscription_expires_at')">
                            <div class="flex items-center gap-1">Expire le @include('livewire.partials.sort-icon', ['field' => 'subscription_expires_at'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('total_investment')">
                            <div class="flex items-center gap-1">Investissement @include('livewire.partials.sort-icon', ['field' => 'total_investment'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($clients as $client)
                        <tr class="hover:bg-gray-50/60" wire:key="client-{{ $client->id }}">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold text-sm ring-2 ring-indigo-200">
                                        {{ strtoupper(substr($client->user->name ?? 'C', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-700">{{ $client->user->name ?? 'Inconnu' }}</p>
                                        <p class="text-xs text-gray-400">{{ $client->user->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $client->code ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $client->country_of_residence ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $client->country_of_origin ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $subscriptionColors = [
                                        'basic' => 'bg-gray-100 text-gray-700',
                                        'standard' => 'bg-blue-100 text-blue-700',
                                        'premium' => 'bg-amber-100 text-amber-700',
                                    ];
                                    $subColor = $subscriptionColors[$client->subscription_type] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $subColor }}">
                                    {{ ucfirst($client->subscription_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                {{ $client->subscription_expires_at?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ number_format($client->total_investment, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                    <div x-show="open" x-cloak class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10" style="display: none;">
                                        <div class="py-1">
                                            <a href="{{ route('admin.clients.edit', $client->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Modifier
                                                </div>
                                            </a>
                                            <a href="{{ route('admin.clients.show', $client->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Voir détails
                                                </div>
                                            </a>
                                            <a href="{{ route('admin.clients.farms', $client->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                    </svg>
                                                    Voir fermes
                                                </div>
                                            </a>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <button wire:click="deleteClient({{ $client->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer ce client ?" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Supprimer
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-gray-400">
                                Aucun client ne correspond à vos critères.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer : compteur + pagination + rows per page --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-t border-gray-100 text-sm text-gray-500">
            <div>
                Affichage {{ $clients->firstItem() ?? 0 }}-{{ $clients->lastItem() ?? 0 }} sur {{ $clients->total() }}
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span>Lignes par page</span>
                    <select wire:model.live="perPage" class="border border-gray-200 rounded px-2 py-1 text-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                {{ $clients->links() }}
            </div>
        </div>
    </div>
</div>
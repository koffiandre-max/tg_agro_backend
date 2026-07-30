<div class="bg-gray-50" x-data="{ openFilter: null }" @click.away="openFilter = null">

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
                placeholder="Rechercher par localisation, type, technicien, client..."
                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
        </div>

        {{-- Filtre Statut --}}
        <div class="relative">
            <button @click="openFilter = openFilter === 'statut' ? null : 'statut'"
                    class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white hover:bg-gray-50">
                Statut <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="openFilter === 'statut'" x-cloak class="absolute z-20 mt-1 w-56 bg-white border border-gray-200 rounded-lg shadow-lg py-1">
                <button wire:click="$set('statut', '')" @click="openFilter = null"
                        class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-50 {{ $statut === '' ? 'text-indigo-600 font-medium' : 'text-gray-700' }}">
                    Tous
                </button>
                @foreach($statuts as $statutOption)
                    <button wire:click="$set('statut', '{{ $statutOption['value'] }}')" @click="openFilter = null"
                            class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-50 {{ $statut === $statutOption['value'] ? 'text-indigo-600 font-medium' : 'text-gray-700' }}">
                        {{ $statutOption['label'] }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Filtre Date Range --}}
        <div class="relative">
            <button @click="openFilter = openFilter === 'date' ? null : 'date'"
                    class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                Date de visite
            </button>
            <div x-show="openFilter === 'date'" x-cloak class="absolute z-20 mt-1 w-64 bg-white border border-gray-200 rounded-lg shadow-lg p-3 space-y-2">
                <label class="block text-xs text-gray-500">Du</label>
                <input type="date" wire:model.live="dateFrom" class="w-full text-sm border border-gray-200 rounded px-2 py-1">
                <label class="block text-xs text-gray-500">Au</label>
                <input type="date" wire:model.live="dateTo" class="w-full text-sm border border-gray-200 rounded px-2 py-1">
            </div>
        </div>

        {{-- Réinitialiser --}}
        @if($search || $statut || $typeVisite || $technicien || $client || $dateFrom || $dateTo)
            <button wire:click="resetFilters" class="text-sm text-indigo-600 hover:underline">Réinitialiser</button>
        @endif
    </div>

    {{-- Tableau --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/60 text-left text-gray-500">
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('date_visite')">
                            <div class="flex items-center gap-1">Date visite @include('livewire.partials.sort-icon', ['field' => 'date_visite'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium">Localisation</th>
                        <th class="px-4 py-3 font-medium">Type de visite</th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('statut')">
                            <div class="flex items-center gap-1">Statut @include('livewire.partials.sort-icon', ['field' => 'statut'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium">Technicien</th>
                        <th class="px-4 py-3 font-medium">Client</th>
                        <th class="px-4 py-3 font-medium text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rapports as $rapport)
                        <tr class="hover:bg-gray-50/60" wire:key="rapport-{{ $rapport->id }}">
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                {{ $rapport->date_visite?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $rapport->localisation_parcelle }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $rapport->type_visite instanceof App\Enums\RapportVisiteType ? $rapport->type_visite->label() : $rapport->type_visite }}
                            </td>
                            <td class="px-4 py-3">
                                @if($rapport->statut)
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($rapport->statut->value === 'Validé') bg-green-100 text-green-700
                                        @elseif($rapport->statut->value === 'Rejeté') bg-red-100 text-red-700
                                        @elseif($rapport->statut->value === 'En attente de validation') bg-amber-100 text-amber-700
                                        @else bg-gray-100 text-gray-700 @endif">
                                        {{ $rapport->statut->label() }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $rapport->technicien?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $rapport->client?->user?->name ?? $rapport->client?->nom ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.rapports-visite.show', $rapport->id) }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="Voir détails">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.rapports-visite.edit', $rapport->id) }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="Modifier">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button wire:click="deleteRapport({{ $rapport->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer ce rapport ?" class="p-2 hover:bg-red-50 rounded-lg transition-colors" title="Supprimer">
                                        <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                                Aucun rapport de visite ne correspond à vos critères.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer : compteur + pagination + rows per page --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-t border-gray-100 text-sm text-gray-500">
            <div>
                Affichage {{ $rapports->firstItem() ?? 0 }}-{{ $rapports->lastItem() ?? 0 }} sur {{ $rapports->total() }}
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

                {{ $rapports->links() }}
            </div>
        </div>
    </div>
</div>
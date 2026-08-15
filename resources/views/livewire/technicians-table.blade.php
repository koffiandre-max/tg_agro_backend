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
                placeholder="Rechercher par nom, email, localisation..."
                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
        </div>

        {{-- Filtre Disponibilité --}}
        <div class="relative">
            <button @click="openFilter = openFilter === 'availability' ? null : 'availability'"
                    class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white hover:bg-gray-50">
                Disponibilité <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="openFilter === 'availability'" x-cloak class="absolute z-20 mt-1 w-40 bg-white border border-gray-200 rounded-lg shadow-lg py-1">
                @foreach(['' => 'Tous', 'available' => 'Disponible', 'unavailable' => 'Indisponible'] as $value => $label)
                    <button wire:click="$set('availability', '{{ $value }}')" @click="openFilter = null"
                            class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-50 {{ $availability === $value ? 'text-indigo-600 font-medium' : 'text-gray-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Réinitialiser --}}
        @if($search || $availability)
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
                            <div class="flex items-center gap-1">Technicien @include('livewire.partials.sort-icon', ['field' => 'user_id'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium">Type</th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('location_base')">
                            <div class="flex items-center gap-1">Base @include('livewire.partials.sort-icon', ['field' => 'location_base'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('is_available')">
                            <div class="flex items-center gap-1">Disponibilité @include('livewire.partials.sort-icon', ['field' => 'is_available'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('current_workload')">
                            <div class="flex items-center gap-1">Charge @include('livewire.partials.sort-icon', ['field' => 'current_workload'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('max_concurrent_missions')">
                            <div class="flex items-center gap-1">Max missions @include('livewire.partials.sort-icon', ['field' => 'max_concurrent_missions'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($technicians as $technician)
                        <tr class="hover:bg-gray-50/60" wire:key="technician-{{ $technician->id }}">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold text-sm ring-2 ring-indigo-200">
                                        {{ strtoupper(substr($technician->user->name ?? 'T', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-700">{{ $technician->user->name ?? 'Inconnu' }}</p>
                                        <p class="text-xs text-gray-400">{{ $technician->user->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($technician->user->type_technicien)
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($technician->user->type_technicien->value === 'culture') bg-green-100 text-green-700
                                        @elseif($technician->user->type_technicien->value === 'elevage') bg-blue-100 text-blue-700
                                        @else bg-purple-100 text-purple-700 @endif">
                                        {{ $technician->user->type_technicien->label() }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">Non défini</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $technician->location_base ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($technician->is_available)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Disponible
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Indisponible
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @php
                                        $loadPercent = min(($technician->current_workload / max($technician->max_concurrent_missions, 1)) * 100, 100);
                                        $loadColor = $loadPercent < 40 ? 'bg-green-500' : ($loadPercent < 70 ? 'bg-amber-500' : 'bg-red-500');
                                    @endphp
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 max-w-[80px]">
                                        <div class="{{ $loadColor }} h-2 rounded-full" style="width: {{ $loadPercent }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600">{{ $technician->current_workload }}/{{ $technician->max_concurrent_missions }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $technician->max_concurrent_missions }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                    <div x-show="open" x-cloak class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10" style="display: none;">
                                        <div class="py-1">
                                            <a href="{{ route('admin.technicians.edit', $technician->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Modifier
                                                </div>
                                            </a>
                                            <button onclick="window.location.href='{{ route('admin.technicians.detail', $technician->id) }}'" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7 -1.274 4.057-5.064 7 -9.542 7 -4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Voir détails
                                                </div>
                                            </button>
                                            <button onclick="window.location.href='{{ route('admin.technicians.missions', $technician->id) }}'" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                    </svg>
                                                    Missions ({{ $technician->missions_count }})
                                                </div>
                                            </button>
                                            <button onclick="window.location.href='{{ route('admin.technicians.reports', $technician->id) }}'" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Rapports
                                                </div>
                                            </button>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <button wire:click="deleteTechnician({{ $technician->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer ce technicien ?" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
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
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                                Aucun technicien ne correspond à vos critères.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer : compteur + pagination + rows per page --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-t border-gray-100 text-sm text-gray-500">
            <div>
                Affichage {{ $technicians->firstItem() ?? 0 }}-{{ $technicians->lastItem() ?? 0 }} sur {{ $technicians->total() }}
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

                {{ $technicians->links() }}
            </div>
        </div>
    </div>
</div>
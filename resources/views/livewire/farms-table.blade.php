<div class=" bg-gray-50" x-data="{ openFilter: null }" @click.away="openFilter = null">

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
                placeholder="Rechercher par nom, localisation, culture..."
                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
        </div>

        {{-- Filtre Statut --}}
        <div class="relative">
            <button @click="openFilter = openFilter === 'status' ? null : 'status'"
                    class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white hover:bg-gray-50">
                Statut <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="openFilter === 'status'" x-cloak class="absolute z-20 mt-1 w-40 bg-white border border-gray-200 rounded-lg shadow-lg py-1">
                @foreach(['' => 'Tous', 'active' => 'Active', 'inactive' => 'Inactive', 'fallow' => 'En jachère'] as $value => $label)
                    <button wire:click="$set('status', '{{ $value }}')" @click="openFilter = null"
                            class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-50 {{ $status === $value ? 'text-indigo-600 font-medium' : 'text-gray-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Filtre Type de Culture --}}
        <div class="relative">
            <button @click="openFilter = openFilter === 'cultureType' ? null : 'cultureType'"
                    class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white hover:bg-gray-50">
                Culture <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="openFilter === 'cultureType'" x-cloak class="absolute z-20 mt-1 w-48 max-h-64 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-lg py-1">
                <button wire:click="$set('cultureType', '')" @click="openFilter = null"
                        class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-50 {{ $cultureType === '' ? 'text-indigo-600 font-medium' : 'text-gray-700' }}">
                    Tous
                </button>
                @foreach($cultureTypes as $type)
                    <button wire:click="$set('cultureType', '{{ $type }}')" @click="openFilter = null"
                            class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-50 {{ $cultureType === $type ? 'text-indigo-600 font-medium' : 'text-gray-700' }}">
                        {{ $type }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Filtre Client --}}
        <div class="relative">
            <button @click="openFilter = openFilter === 'client' ? null : 'client'"
                    class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white hover:bg-gray-50">
                Client <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="openFilter === 'client'" x-cloak class="absolute z-20 mt-1 w-48 max-h-64 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-lg py-1">
                <button wire:click="$set('client', '')" @click="openFilter = null"
                        class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-50 {{ $client === '' ? 'text-indigo-600 font-medium' : 'text-gray-700' }}">
                    Tous
                </button>
                @foreach($clients as $id => $name)
                    <button wire:click="$set('client', '{{ $name }}')" @click="openFilter = null"
                            class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-50 {{ $client === $name ? 'text-indigo-600 font-medium' : 'text-gray-700' }}">
                        {{ $name }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Filtre Date Range --}}
        <div class="relative">
            <button @click="openFilter = openFilter === 'date' ? null : 'date'"
                    class="flex items-center gap-2 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                Date
            </button>
            <div x-show="openFilter === 'date'" x-cloak class="absolute z-20 mt-1 w-64 bg-white border border-gray-200 rounded-lg shadow-lg p-3 space-y-2">
                <label class="block text-xs text-gray-500">Du</label>
                <input type="date" wire:model.live="dateFrom" class="w-full text-sm border border-gray-200 rounded px-2 py-1">
                <label class="block text-xs text-gray-500">Au</label>
                <input type="date" wire:model.live="dateTo" class="w-full text-sm border border-gray-200 rounded px-2 py-1">
            </div>
        </div>

        {{-- Réinitialiser --}}
        @if($search || $status || $cultureType || $client || $dateFrom || $dateTo)
            <button wire:click="resetFilters" class="text-sm text-indigo-600 hover:underline">Réinitialiser</button>
        @endif
    </div>

    {{-- Tableau --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/60 text-left text-gray-500">
                        <th class="px-4 py-3 font-medium">Réf. dossier</th>
                        <th class="px-4 py-3 font-medium">Numéro cadastral</th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('total_area_hectares')">
                            <div class="flex items-center gap-1">Surface (ha) @include('livewire.partials.sort-icon', ['field' => 'total_area_hectares'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('status')">
                            <div class="flex items-center gap-1">Statut @include('livewire.partials.sort-icon', ['field' => 'status'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('crop_stage_progress')">
                            <div class="flex items-center gap-1">Progression @include('livewire.partials.sort-icon', ['field' => 'crop_stage_progress'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('expected_harvest_date')">
                            <div class="flex items-center gap-1">Récolte prévue @include('livewire.partials.sort-icon', ['field' => 'expected_harvest_date'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium">Client</th>
                        <th class="px-4 py-3 font-medium text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($farms as $farm)
                        <tr class="hover:bg-gray-50/60" wire:key="farm-{{ $farm->id }}">
                            <td class="px-4 py-3 text-gray-600">{{ $farm->reference_dossier ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $farm->numero_cadastral ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ number_format($farm->total_area_hectares, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $farm->statusBadgeClasses() }}">
                                    {{ $farm->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 max-w-[100px]">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $farm->crop_stage_progress }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600">{{ $farm->crop_stage_progress }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                {{ $farm->expected_harvest_date?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $farm->user->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                    <div x-show="open" x-cloak class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10" style="display: none;">
                                        <div class="py-1">
                                            <a href="{{ route('admin.farms.edit', $farm->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Modifier
                                                </div>
                                            </a>
                                            <a href="{{ route('admin.farms.show', $farm->id) }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5 9.542-7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Voir détails
                                                </div>
                                            </a>
                                            <a href="{{ route('admin.gallery.index', ['farm_id' => $farm->id]) }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <div class="flex items-center justify-between gap-2">
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/>
                                                        </svg>
                                                        Images
                                                    </div>
                                                    <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                        {{ $farm->photos_count }}
                                                    </span>
                                                </div>
                                            </a>
                                            <button wire:click="assignTechnician({{ $farm->id }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    Assigner technicien
                                                </div>
                                            </button>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <button wire:click="deleteFarm({{ $farm->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer cette exploitation ?" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
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
                                Aucune exploitation ne correspond à vos critères.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer : compteur + pagination + rows per page --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-t border-gray-100 text-sm text-gray-500">
            <div>
                Affichage {{ $farms->firstItem() ?? 0 }}-{{ $farms->lastItem() ?? 0 }} sur {{ $farms->total() }}
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

                {{ $farms->links() }}
            </div>
        </div>
    </div>

    {{-- Modal d'assignation de technicien --}}
    <div x-data="{ showAssign: false }"
         x-show="showAssign"
         @open-assign-modal.window="showAssign = true"
         @close-assign-modal.window="showAssign = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display: none;">
        <div class="absolute inset-0 bg-black/50" @click="showAssign = false"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden" @click.stop>
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Assigner un technicien</h3>
                <button type="button" @click="showAssign = false" wire:click="closeAssignModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-6 py-4 space-y-4">
                <p class="text-sm text-gray-600">
                    Exploitation : <span class="font-medium text-gray-900">{{ $selectedFarmName }}</span>
                </p>

                <div>
                    <p class="block text-sm font-medium text-gray-700 mb-2">Choisir un technicien</p>

                    <div class="max-h-72 overflow-y-auto space-y-2 pr-1">
                        {{-- Aucun --}}
                        <label class="flex items-center gap-3 rounded-xl border px-4 py-3 cursor-pointer transition-colors
                                {{ is_null($selectedTechnicianId) ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:bg-gray-50' }}">
                            <input type="radio" name="technician_choice" value="" wire:model.live="selectedTechnicianId" class="sr-only">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900">Aucun</p>
                                <p class="text-xs text-gray-500">Retirer l'assignation</p>
                            </div>
                        </label>

                        @foreach($technicians as $technician)
                            @php
                                $tName = $technician->user->name ?? 'Technicien #' . $technician->id;
                                $tPhone = $technician->user && $technician->user->phone ? $technician->user->phone : null;
                                $tType = $technician->type_technicien?->label();
                            @endphp
                            <label class="flex items-center gap-3 rounded-xl border px-4 py-3 cursor-pointer transition-colors
                                    {{ $selectedTechnicianId == $technician->id ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                <input type="radio" name="technician_choice" value="{{ $technician->id }}" wire:model.live="selectedTechnicianId" class="sr-only">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">
                                    {{ strtoupper(substr($tName, 0, 1)) }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-gray-900">{{ $tName }}</p>
                                    <p class="truncate text-xs text-gray-500">
                                        @if($tType)<span class="capitalize">{{ $tType }}</span>@endif
                                        @if($tType && $tPhone) · @endif
                                        {{ $tPhone ?? '' }}
                                    </p>
                                </div>
                                @if($selectedTechnicianId == $technician->id)
                                    <svg class="size-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" @click="showAssign = false" wire:click="closeAssignModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Annuler
                </button>
                <button type="button" wire:click="assignTechnicianToFarm" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                    Assigner
                </button>
            </div>
        </div>
    </div>
</div>

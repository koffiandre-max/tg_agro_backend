<div class="bg-gray-50" x-data="{ openFilter: null, showDelete: false, deleteId: null, deleteTitle: '' }" @click.away="openFilter = null">

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
                placeholder="Rechercher par titre, description, exploitation..."
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
                @foreach(['' => 'Tous', 'pending' => 'En attente', 'in_progress' => 'En cours', 'completed' => 'Terminée', 'cancelled' => 'Annulée'] as $value => $label)
                    <button wire:click="$set('status', '{{ $value }}')" @click="openFilter = null"
                            class="block w-full text-left px-3 py-1.5 text-sm hover:bg-gray-50 {{ $status === $value ? 'text-indigo-600 font-medium' : 'text-gray-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Réinitialiser --}}
        @if($search || $status)
            <button wire:click="resetFilters" class="text-sm text-indigo-600 hover:underline">Réinitialiser</button>
        @endif
    </div>

    {{-- Tableau --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/60 text-left text-gray-500">
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('title')">
                            <div class="flex items-center gap-1">Titre @include('livewire.partials.sort-icon', ['field' => 'title'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium">Technicien</th>
                        <th class="px-4 py-3 font-medium">Exploitation</th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('scheduled_date')">
                            <div class="flex items-center gap-1">Date @include('livewire.partials.sort-icon', ['field' => 'scheduled_date'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium cursor-pointer select-none" wire:click="sortBy('status')">
                            <div class="flex items-center gap-1">Statut @include('livewire.partials.sort-icon', ['field' => 'status'])</div>
                        </th>
                        <th class="px-4 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($missions as $mission)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.technitian.missions.show', $mission->id) }}" class="font-medium text-gray-900 hover:text-indigo-600">
                                    {{ $mission->title }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $mission->technician?->user?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $mission->farm?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $mission->scheduled_date?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($mission->status === 'completed')
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">
                                        Terminée
                                    </span>
                                @elseif($mission->status === 'in_progress')
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700">
                                        En cours
                                    </span>
                                @elseif($mission->status === 'pending')
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700">
                                        En attente
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700">
                                        Annulée
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.technitian.missions.show', $mission->id) }}"
                                       class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="Voir">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7 -1.274 4.057-5.064 7 -9.542 7 -4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    @if($isAdmin)
                                        <a href="{{ route('admin.technitian.missions.edit', $mission->id) }}"
                                           class="p-2 hover:bg-indigo-50 rounded-lg transition-colors" title="Modifier">
                                            <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <button type="button" @click="showDelete = true; deleteId = {{ $mission->id }}; deleteTitle = {{ json_encode($mission->title) }};"
                                            class="p-2 hover:bg-red-50 rounded-lg transition-colors" title="Supprimer">
                                            <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                                Aucune mission ne correspond à vos critères.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer : compteur + pagination + rows per page --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-t border-gray-100 text-sm text-gray-500">
            <div>
                Affichage {{ $missions->firstItem() ?? 0 }}-{{ $missions->lastItem() ?? 0 }} sur {{ $missions->total() }}
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

                {{ $missions->links() }}
            </div>
        </div>
    </div>

    {{-- Modal de confirmation de suppression --}}
    <div x-cloak x-show="showDelete" @click.self="showDelete = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-sm rounded-2xl bg-white shadow-xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5 20a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v15z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Supprimer la mission</h3>
                    <p class="text-sm text-gray-500">Cette action est irréversible.</p>
                </div>
            </div>
            <p class="text-sm text-gray-700 mb-6">
                Êtes-vous sûr de vouloir supprimer la mission
                <span class="font-medium" x-text="deleteTitle"></span> ?
            </p>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showDelete = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Annuler
                </button>
                <button type="button" @click="showDelete = false; $wire.call('deleteMission', deleteId)"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>


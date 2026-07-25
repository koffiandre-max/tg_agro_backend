<div class="bg-slate-50 relative" x-data="{ openFilter: null }" @click.away="openFilter = null">

    {{-- Bandeau des Actions de Masse (S'affiche uniquement si des lignes sont sélectionnées) --}}
    @if(isset($selected) && count($selected) > 0)
        <div class="flex items-center gap-3 px-4 py-3 bg-indigo-50 border border-indigo-100 rounded-xl mb-4 animate-fadeIn">
            <span class="text-xs font-bold text-indigo-900 uppercase tracking-wider">
                {{ count($selected) }} rapport(s) sélectionné(s)
            </span>
            <div class="flex items-center gap-2 ml-auto">
                @if(auth()->user()->role === 'admin')
                    <button wire:click="bulkValidate" class="inline-flex items-center justify-center px-3.5 py-1.5 text-xs font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                        Valider la sélection
                    </button>
                    <button wire:click="bulkReject" wire:confirm="Êtes-vous sûr de vouloir rejeter les rapports sélectionnés ?" class="inline-flex items-center justify-center px-3.5 py-1.5 text-xs font-bold text-red-700 bg-red-50 border border-red-200/60 rounded-lg hover:bg-red-100 transition-colors">
                        Rejeter la sélection
                    </button>
                @endif
            </div>
        </div>
    @endif

    {{-- Barre de recherche + filtres + actions --}}
    <div class="flex flex-wrap items-center gap-3 mb-5">

        {{-- Recherche --}}
        <div class="relative flex-1 min-w-[280px]">
            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.4 4.4a7.5 7.5 0 0012.25 12.25z" />
            </svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Rechercher par titre, exploitation, client..."
                class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-slate-900 placeholder:text-slate-400"
            >
        </div>

        {{-- Filtre Statut --}}
        <div class="relative">
            <button @click="openFilter = openFilter === 'status' ? null : 'status'"
                    class="flex items-center gap-2 px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-semibold transition-colors">
                Statut 
                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openFilter === 'status' ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="openFilter === 'status'" x-cloak class="absolute z-20 mt-1.5 w-44 bg-white border border-slate-200 rounded-xl shadow-lg py-1 right-0 sm:left-0 origin-top-right">
                @foreach(['' => 'Tous', 'pending' => 'En attente', 'validated' => 'Validé', 'rejected' => 'Rejeté'] as $value => $label)
                    <button wire:click="$set('status', '{{ $value }}')" @click="openFilter = null"
                            class="block w-full text-left px-3.5 py-2 text-sm hover:bg-slate-50 transition-colors {{ $status === $value ? 'text-indigo-600 font-bold' : 'text-slate-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Filtre Type --}}
        <div class="relative">
            <button @click="openFilter = openFilter === 'type' ? null : 'type'"
                    class="flex items-center gap-2 px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-semibold transition-colors">
                Type 
                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openFilter === 'type' ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="openFilter === 'type'" x-cloak class="absolute z-20 mt-1.5 w-52 max-h-64 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-lg py-1 right-0 sm:left-0 origin-top-right">
                <button wire:click="$set('type', '')" @click="openFilter = null"
                        class="block w-full text-left px-3.5 py-2 text-sm hover:bg-slate-50 transition-colors {{ $type === '' ? 'text-indigo-600 font-bold' : 'text-slate-700' }}">
                    Tous
                </button>
                @foreach($reportTypes as $reportType)
                    <button wire:click="$set('type', '{{ $reportType }}')" @click="openFilter = null"
                            class="block w-full text-left px-3.5 py-2 text-sm hover:bg-slate-50 transition-colors {{ $type === $reportType ? 'text-indigo-600 font-bold' : 'text-slate-700' }}">
                        {{ $reportType }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Filtre Période (Date Range) --}}
        <div class="relative">
            <button @click="openFilter = openFilter === 'date' ? null : 'date'"
                    class="flex items-center gap-2 px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-semibold transition-colors">
                Période 
                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openFilter === 'date' ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="openFilter === 'date'" x-cloak class="absolute z-20 mt-1.5 w-48 bg-white border border-slate-200 rounded-xl shadow-lg py-1 right-0 sm:left-0 origin-top-right">
                @foreach(['' => 'Toutes', 'today' => 'Aujourd\'hui', 'week' => 'Cette semaine', 'month' => 'Ce mois-ci', 'year' => 'Cette année'] as $value => $label)
                    <button wire:click="$set('dateRange', '{{ $value }}')" @click="openFilter = null"
                            class="block w-full text-left px-3.5 py-2 text-sm hover:bg-slate-50 transition-colors {{ ($dateRange ?? '') === $value ? 'text-indigo-600 font-bold' : 'text-slate-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Bouton Exporter --}}
        <button wire:click="export" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold transition-colors shadow-sm">
            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Exporter
        </button>

        {{-- Réinitialiser --}}
        @if($search || $status || $type || ($dateRange ?? ''))
            <button wire:click="resetFilters" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
                Réinitialiser les filtres
            </button>
        @endif
    </div>

    {{-- Conteneur du Tableau --}}
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm relative">
        
        {{-- Voile de chargement Livewire --}}
        <div wire:loading class="absolute inset-0 bg-white/60 backdrop-blur-[1px] z-10 flex items-center justify-center animate-fadeIn">
            <div class="flex flex-col items-center gap-2">
                <svg class="animate-spin h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-xs font-semibold text-slate-500">Mise à jour en cours...</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/60 text-slate-500 font-semibold">
                        {{-- Checkbox globale pour actions en masse --}}
                        <th class="px-4 py-3.5 w-10">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20 h-4 w-4">
                        </th>
                        {{-- Colonnes avec mise en surbrillance de l'ordre de tri --}}
                        <th class="px-4 py-3.5 font-bold cursor-pointer select-none {{ $sortField === 'title' ? 'text-indigo-600 bg-slate-50/85' : '' }}" wire:click="sortBy('title')">
                            <div class="flex items-center gap-1">Titre @include('livewire.partials.sort-icon', ['field' => 'title'])</div>
                        </th>
                        <th class="px-4 py-3.5 font-bold cursor-pointer select-none {{ $sortField === 'type' ? 'text-indigo-600 bg-slate-50/85' : '' }}" wire:click="sortBy('type')">
                            <div class="flex items-center gap-1">Type @include('livewire.partials.sort-icon', ['field' => 'type'])</div>
                        </th>
                        <th class="px-4 py-3.5 font-bold">Exploitation</th>
                        <th class="px-4 py-3.5 font-bold">Client</th>
                        <th class="px-4 py-3.5 font-bold">Technicien</th>
                        <th class="px-4 py-3.5 font-bold cursor-pointer select-none {{ $sortField === 'status' ? 'text-indigo-600 bg-slate-50/85' : '' }}" wire:click="sortBy('status')">
                            <div class="flex items-center gap-1">Statut @include('livewire.partials.sort-icon', ['field' => 'status'])</div>
                        </th>
                        <th class="px-4 py-3.5 font-bold cursor-pointer select-none {{ $sortField === 'created_at' ? 'text-indigo-600 bg-slate-50/85' : '' }}" wire:click="sortBy('created_at')">
                            <div class="flex items-center gap-1">Date @include('livewire.partials.sort-icon', ['field' => 'created_at'])</div>
                        </th>
                        <th class="px-4 py-3.5 font-bold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reports as $report)
                        <tr class="hover:bg-slate-50/40 transition-colors" wire:key="report-{{ $report->id }}">
                            {{-- Checkbox de sélection individuelle --}}
                            <td class="px-4 py-3 w-10">
                                <input type="checkbox" wire:model.live="selected" value="{{ $report->id }}" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20 h-4 w-4">
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-800">{{ $report->title }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 capitalize">
                                    {{ $report->type }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600 font-medium">{{ $report->farm?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600 font-medium">{{ $report->client?->user?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600 font-medium">{{ $report->technician?->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($report->status === 'validated')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200/50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Validé
                                    </span>
                                @elseif($report->status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 border border-rose-200/50 px-2.5 py-0.5 text-xs font-semibold text-rose-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                        Rejeté
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200/50 px-2.5 py-0.5 text-xs font-semibold text-amber-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        En attente
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500 font-medium whitespace-nowrap">
                                {{ $report->created_at?->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false" class="p-2 hover:bg-slate-100 rounded-lg transition-colors inline-flex">
                                        <svg class="w-4.5 h-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                    <div x-show="open" x-cloak class="absolute right-0 mt-1.5 w-48 bg-white border border-slate-200 rounded-xl shadow-lg z-10 origin-top-right py-1" style="display: none;">
                                        <a href="{{ route('admin.reports.show', $report->id) }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Voir détails
                                        </a>
                                        @if($report->status === 'pending' && auth()->user()->role === "admin")
                                            <button wire:click="validateReport({{ $report->id }})" class="flex items-center gap-2 w-full text-left px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                                                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Valider le rapport
                                            </button>
                                            <div class="border-t border-slate-100 my-1"></div>
                                            <button wire:click="rejectReport({{ $report->id }})" wire:confirm="Êtes-vous sûr de vouloir rejeter ce rapport ?" class="flex items-center gap-2 w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50/50 transition-colors">
                                                <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Rejeter
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- Empty State Graphique --}}
                        <tr>
                            <td colspan="9" class="px-4 py-16 text-center">
                                <div class="max-w-sm mx-auto flex flex-col items-center justify-center text-slate-400">
                                    <div class="p-3.5 bg-slate-50 rounded-full border border-slate-100/50 text-slate-400 mb-3 shrink-0">
                                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.008 1.24l.885 1.77a2.25 2.25 0 002.007 1.24h1.98a2.25 2.25 0 002.007-1.24l.885-1.77a2.25 2.25 0 012.007-1.24h3.86m-18 0h18a2 2 0 012 2v3a2 2 0 01-2 2H2.25a2 2 0 01-2-2v-3a2 2 0 012-2zm0-5.25h18A2.25 2.25 0 0022.5 6V4.5A2.25 2.25 0 0020.25 2.25h-16.5A2.25 2.25 0 001.5 4.5V6a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-semibold text-slate-900">Aucun rapport trouvé</h4>
                                    <p class="text-xs text-slate-500 mt-1 max-w-[280px]">Aucun document ne correspond à vos filtres de recherche ou critères sélectionnés.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pied du tableau (pagination, lignes par page, compteurs) --}}
        <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4 border-t border-slate-100 text-sm text-slate-500 font-semibold bg-slate-50/50">
            <div>
                Affichage <span class="text-slate-900">{{ $reports->firstItem() ?? 0 }}</span> à <span class="text-slate-900">{{ $reports->lastItem() ?? 0 }}</span> sur <span class="text-slate-900">{{ $reports->total() }}</span> rapports
            </div>

            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2 text-xs">
                    <span>Lignes par page</span>
                    <select wire:model.live="perPage" class="border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs bg-white text-slate-700 font-semibold focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <div>
                    {{ $reports->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
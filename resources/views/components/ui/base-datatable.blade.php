{{-- resources/views/components/datatable.blade.php --}}
@props([
    'id' => 'datatable',
    'title' => 'Data Table',
    'description' => null,
    'data' => [],
    'columns' => [],
    'actions' => false,
    'searchable' => true,
    'filterable' => true,
    'advancedFilterable' => false,
    'exportable' => true,
    'perPage' => 10,
    'showImages' => false,
    'actionButtons' => [],
    'filters' => [],
    'modalSize' => '2xl',
    'enableRowClick' => false,
    'peekType' => null,
    'peekActions' => [],
    'topFilters' => [],
    'rowStatusField' => null,
    'rowDeadlineField' => null,
    'rowDeadlineSkipStatuses' => [],
    'bulkActions' => [],
    'rowKey' => 'id',
    'printable' => true,
    'printUrl' => null,
    'importable' => true,
    'importUrl' => null,
])

@php
    // Filter columns by feature — remove columns whose 'feature' key is not active for this business
    $_dtBid = session('selected_business_id') ?: auth()->user()?->business_id;
    $columns = array_values(array_filter($columns, function($col) use ($_dtBid) {
        if (!isset($col['feature'])) return true;
        return $_dtBid && \App\Core\ModuleRegistry::isEnabled($col['feature'], $_dtBid);
    }));

    // Convertir les données en JSON pour Alpine.js
    $dataJson = json_encode($data);
    $columnsJson = json_encode($columns);
    $actionButtonsJson = json_encode($actionButtons);
    $perPageInit = $perPage;
    $filtersJson = json_encode($filters);

    // Extraire les options uniques pour les colonnes filtrables
    $filterOptions = [];
    foreach ($columns as $column) {
        if (isset($column['filterable']) && $column['filterable']) {
            if (($column['type'] ?? '') === 'select' && isset($column['options'])) {
                $filterOptions[$column['key']] = $column['options'];
            }
        }
    }
    $filterOptionsJson = json_encode($filterOptions);
    $bulkActionsJson   = json_encode($bulkActions);

    // Resolve import URL: explicit prop → auto-derive from current path + /import/csv
    $resolvedImportUrl = $importUrl ?? (rtrim(request()->url(), '/') . '/import/csv');
@endphp

<div x-data="dataTable({{ $dataJson }}, {{ $columnsJson }}, {{ $perPageInit }}, {{ $actionButtonsJson }}, {{ $filtersJson }}, {{ $filterOptionsJson }}, {{ json_encode($rowStatusField) }}, {{ json_encode($rowDeadlineField) }}, {{ json_encode($rowDeadlineSkipStatuses) }}, {{ $bulkActionsJson }}, {{ json_encode($rowKey) }})" x-init="init()"
    x-on:show-row-details.window="showRowDetails($event.detail)"
    x-on:datatable-refresh.window="window.location.reload()">

    {{-- ===== TOP FILTER BAR ===== --}}
    @if(count($topFilters) > 0)
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-4 border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="flex items-center gap-2 px-5 py-2.5 bg-gray-50 dark:bg-gray-900/40 border-b border-gray-100 dark:border-gray-700">
            <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Filtres avancés</span>
        </div>
        <div class="flex flex-wrap items-end gap-3 px-5 py-4">

            @foreach($topFilters as $filter)
                @php
                    $fKey   = $filter['key'];
                    $fLabel = $filter['label'] ?? $fKey;
                    $fType  = $filter['type'] ?? 'text';
                @endphp

                @if($fType === 'daterange')
                    {{-- Intervalle de dates --}}
                    <div class="flex items-end gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                {{ $fLabel }} — Du
                            </label>
                            <input type="date"
                                :value="dateFilters['{{ $fKey }}']?.from ?? ''"
                                 @change="
                                    if (!dateFilters['{{ $fKey }}']) dateFilters['{{ $fKey }}'] = {from:'',to:''};
                                    dateFilters['{{ $fKey }}'].from = $event.target.value;
                                    filterData();
                                "
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Au</label>
                            <input type="date"
                                :value="dateFilters['{{ $fKey }}']?.to ?? ''"
                                @change="
                                    if (!dateFilters['{{ $fKey }}']) dateFilters['{{ $fKey }}'] = {from:'',to:''};
                                    dateFilters['{{ $fKey }}'].to = $event.target.value;
                                    filterData();
                                "
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                    </div>

                @elseif($fType === 'select')
                    {{-- Select --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ $fLabel }}</label>
                        <select
                            x-model="activeFilters['{{ $fKey }}']"
                            @change="filterData()"
                            class="px-3 py-2 border  border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Tous</option>
                            @if(isset($filter['valueField']))
                                {{-- Collection Eloquent ou tableau d'objets/arrays --}}
                                @php
                                    $vField = $filter['valueField'];
                                    $lField = $filter['labelField'] ?? $vField;
                                @endphp
                                @foreach($filter['options'] ?? [] as $opt)
                                    @php
                                        $oVal = is_array($opt) ? ($opt[$vField] ?? '') : ($opt->{$vField} ?? '');
                                        $oLabel = is_array($opt) ? ($opt[$lField] ?? $oVal) : ($opt->{$lField} ?? $oVal);
                                    @endphp
                                    <option value="{{ $oVal }}">{{ $oLabel }}</option>
                                @endforeach
                            @else
                                {{-- Tableau associatif classique: ['val' => 'Label'] --}}
                                @foreach($filter['options'] ?? [] as $optVal => $optLabel)
                                    <option value="{{ $optVal }}">{{ $optLabel }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                @else
                    {{-- Texte --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ $fLabel }}</label>
                        <input type="text"
                            x-model="activeFilters['{{ $fKey }}']"
                            @input.debounce.300ms="filterData()"
                            placeholder="{{ $filter['placeholder'] ?? 'Rechercher...' }}"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                @endif
            @endforeach

            {{-- Badges filtres actifs + reset --}}
            <div class="flex flex-wrap items-center gap-1.5 self-end pb-1.5 ml-auto">
                <template x-for="(val, key) in dateFilters" :key="'df_'+key">
                    <template x-if="val && (val.from || val.to)">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300">
                            <span x-text="(val.from||'∞')+' → '+(val.to||'∞')"></span>
                            <button @click="dateFilters[key]={from:'',to:''}; filterData()" class="ml-0.5 hover:text-indigo-900">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </span>
                    </template>
                </template>
                <template x-for="(val, key) in activeFilters" :key="'af_'+key">
                    <template x-if="val">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300">
                            <span x-text="val"></span>
                            <button @click="activeFilters[key]=''; filterData()" class="ml-0.5 hover:text-emerald-900">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </span>
                    </template>
                </template>

                <button
                    @click="dateFilters = {}; activeFilters = {}; filterData()"
                    x-show="Object.values(dateFilters).some(v=>v&&(v.from||v.to)) || Object.values(activeFilters).some(v=>v)"
                    x-cloak
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Réinitialiser
                </button>
            </div>

        </div>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">

    <!-- Header with Title, Search, and Actions -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
            <!-- Title and Description -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $title }}</h3>
                @if ($description)
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
                @endif
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-2">
                @if ($exportable)
                    <!-- Export Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export
                            <svg class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                            class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-10">
                            <div class="py-1">
                                <button @click="exportCSV(); open = false"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <svg class="inline mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Export CSV
                                </button>
                                <button @click="exportExcel(); open = false"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <svg class="inline mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Export Excel
                                </button>
                                <button @click="printTable(); open = false"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <svg class="inline mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    Imprimer
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Print Button --}}
                @if($printable)
                @if($printUrl)
                <a href="{{ $printUrl }}" target="_blank"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Imprimer
                </a>
                @else
                <button type="button" @click="printTable()"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Imprimer
                </button>
                @endif
                @endif

                {{-- Import Button --}}
                @if($importable)
                <button type="button" @click="$dispatch('open-import-modal-{{ $id }}')"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Importer
                </button>
                @endif

                {{-- Column Visibility Picker --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                        title="Colonnes visibles">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                        </svg>
                        <span class="ml-1.5 hidden sm:inline">Colonnes</span>
                        <template x-if="hiddenColumns.length > 0">
                            <span class="ml-1.5 inline-flex items-center justify-center w-4 h-4 text-xs font-bold bg-indigo-500 text-white rounded-full" x-text="hiddenColumns.length"></span>
                        </template>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute right-0 mt-2 w-52 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-20 py-2">
                        <div class="px-3 pb-2 mb-1 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Colonnes</span>
                            <button @click="hiddenColumns = []" x-show="hiddenColumns.length > 0"
                                class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">Tout afficher</button>
                        </div>
</div>
                        <template x-for="col in columns" :key="col.key">
                            <label class="flex items-center gap-2.5 px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                <input type="checkbox"
                                    :checked="!hiddenColumns.includes(col.key)"
                                    @change="toggleColumn(col.key)"
                                    class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300" x-text="col.label"></span>
                            </label>
                        </template>
                    </div>
                </div>

                {{ $slot }}
            </div>
        </div>
        <div class="mt-4 flex flex-col sm:flex-row gap-3">
            @if ($searchable)
                <!-- Search Input -->
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input x-model="search" @input.debounce.300ms="filterData()" type="text"
                            placeholder="Rechercher..."
                            data-filter-input
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm">
                    </div>
                </div>
            @endif

            @if ($advancedFilterable)
                <!-- Advanced Filter Button -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="inline-flex items-center px-4 py-2 border border-indigo-300 dark:border-indigo-600 rounded-lg text-sm font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Filtres avancés
                        <span x-show="advancedFiltersCount > 0"
                            class="ml-2 px-2 py-0.5 text-xs font-semibold bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded-full"
                            x-text="advancedFiltersCount"></span>
                    </button>

                    <!-- Advanced Filter Panel -->
                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute right-0 mt-2 w-80 rounded-lg shadow-lg bg-white dark:bg-gray-200 ring-1 ring-gray-200 ring-opacity-5 z-50">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Filtres avancés</h4>
                                <div class="flex space-x-2">
                                    <button @click="clearAdvancedFilters()"
                                        class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                                        Réinitialiser
                                    </button>
                                    <button @click="open = false"
                                        class="text-xs text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Filtres par colonne - Version simplifiée avec seulement des selects -->
                            <div class="space-y-4">

                                <!-- Date d'inscription -->
                                <template x-if="columns.find(c => c.key === 'created_at')">
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Période
                                            d'inscription</label>
                                        <select x-model="activeFilters.date_period" @change="applyDatePeriodFilter()"
                                            id="select-slim"
                                            class="block w-full px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                            <option value="">Toutes périodes</option>
                                            <option value="today">Aujourd'hui</option>
                                            <option value="yesterday">Hier</option>
                                            <option value="last_7_days">7 derniers jours</option>
                                            <option value="last_30_days">30 derniers jours</option>
                                            <option value="this_month">Ce mois</option>
                                            <option value="last_month">Mois dernier</option>
                                        </select>
                                    </div>
                                </template>

                                <!-- Filtres personnalisés dynamiques -->
                                <template x-for="(filterGroup, groupName) in customFilterGroups"
                                    :key="groupName">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1"
                                            x-text="groupName"></label>
                                        <select x-model="activeFilters[filterGroup.key]" @change="filterData()"
                                            id="select-slim"
                                            class="block w-full px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                            <option value="">Tous</option>
                                            <template x-for="option in filterGroup.options" :key="option.value">
                                                <option :value="option.value" x-text="option.label"></option>
                                            </template>
                                        </select>
                                    </div>
                                </template>
                            </div>

                            <!-- Badge des filtres actifs -->
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600"
                                x-show="activeFilterTags.length > 0">
                                <h5 class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Filtres appliqués
                                </h5>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="tag in activeFilterTags" :key="tag.key">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                                            <span x-text="tag.label"></span>: <span class="ml-1 font-semibold"
                                                x-text="tag.value"></span>
                                            <button @click="removeFilter(tag.key)" class="ml-1 hover:text-indigo-600">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($filterable && !$advancedFilterable)
                <!-- Simple Filter Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filtres
                        <span x-show="Object.keys(activeFilters).length > 0"
                            class="ml-2 px-2 py-0.5 text-xs font-semibold bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded-full"
                            x-text="Object.keys(activeFilters).length"></span>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute right-0 mt-2 w-64 rounded-lg shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-10">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Filtres</h4>
                                <button @click="clearFilters()"
                                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                                    Réinitialiser
                                </button>
                            </div>
                            <div class="space-y-3">
                                <template x-for="column in filterableColumns" :key="column.key">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1"
                                            x-text="column.label"></label>
                                        <input type="text" x-model="activeFilters[column.key]"
                                            @input.debounce.300ms="filterData()"
                                            class="block w-full px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Bouton sauvegarder vue -->
            {{-- <button @click="saveCurrentView()"
                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg border border-dashed border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:border-indigo-400 hover:text-indigo-600 transition-colors"
                title="Sauvegarder la vue actuelle">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
                <span class="hidden sm:inline">Vue</span>
            </button> --}}

            <!-- Per Page Selector -->
            <select x-model="perPage" @change="currentPage = 1; filterData()"
                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                <option value="10">10 par page</option>
                <option value="25">25 par page</option>
                <option value="50">50 par page</option>
                <option value="100">100 par page</option>
            </select>
        </div>

        <!-- Saved Views Pills -->
        <div x-show="savedViews.length > 0" class="flex flex-wrap items-center gap-1.5 mt-2.5">
            <span class="text-xs text-gray-400 shrink-0 font-medium">Vues :</span>
            <template x-for="(v, idx) in savedViews" :key="idx">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700 group">
                    <span @click="applyView(v)" x-text="v.name" class="cursor-pointer hover:underline"></span>
                    <button @click.stop="deleteView(idx)"
                        class="ml-0.5 text-indigo-300 hover:text-red-500 transition-colors leading-none"
                        title="Supprimer cette vue">&times;</button>
                </span>
            </template>
        </div>

        <!-- Active Filters Bar -->
        <div class="mt-3" x-show="activeFilterTags.length > 0">
            <div class="flex flex-wrap gap-2">
                <template x-for="tag in activeFilterTags" :key="tag.key">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                        <span x-text="tag.label"></span>:
                        <span class="ml-1 font-semibold" x-text="tag.value"></span>
                        <button @click="removeFilter(tag.key)" class="ml-2 hover:text-indigo-600">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </span>
                </template>
                <button @click="clearFilters()"
                    class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    Tout effacer
                </button>
            </div>
        </div>
    </div>

    {{-- ===== BULK ACTIONS TOOLBAR ===== --}}
    @if(count($bulkActions) > 0)
    <div x-show="selectedRows.length > 0" x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="px-6 py-3 bg-indigo-50 dark:bg-indigo-900/30 border-b border-indigo-200 dark:border-indigo-700 flex items-center gap-3 flex-wrap">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">
                <span x-text="selectedRows.length"></span>&nbsp;sélectionné(s)
            </span>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @foreach($bulkActions as $bulkAction)
            @php
                $bClass = match($bulkAction['class'] ?? 'default') {
                    'danger'  => 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white',
                    'warning' => 'bg-amber-500 hover:bg-amber-600 focus:ring-amber-500 text-white',
                    'success' => 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500 text-white',
                    default   => 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 text-white',
                };
            @endphp
            <button type="button"
                @click="executeBulkAction({{ json_encode($bulkAction) }})"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 {{ $bClass }}">
                @if(!empty($bulkAction['icon']))
                <i class="{{ $bulkAction['icon'] }}"></i>
                @endif
                {{ $bulkAction['label'] }}
            </button>
            @endforeach
        </div>
        <button type="button" @click="selectedRows = []"
            class="ml-auto text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 underline">
            Désélectionner tout
        </button>
    </div>
    @endif

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    @if(count($bulkActions) > 0)
                    <th class="pl-5 pr-2 py-3 w-10">
                        <input type="checkbox"
                            :checked="selectedRows.length > 0 && selectedRows.length === paginatedData.length"
                            :indeterminate.prop="selectedRows.length > 0 && selectedRows.length < paginatedData.length"
                            @change="toggleSelectAll()"
                            class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                    </th>
                    @endif
                    <template x-for="column in visibleColumns" :key="column.key">
                        <th @click="column.sortable ? sort(column.key) : null"
                            :class="[
                                'px-6 py-3 text-left text-xs font-medium uppercase tracking-wider',
                                column.sortable ? 'cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800' : '',
                                sortColumn === column.key ? 'text-indigo-600 dark:text-indigo-400' :
                                'text-gray-500 dark:text-gray-400'
                            ]">
                            <div class="flex items-center space-x-1">
                                <span x-text="column.label"></span>
                                <template x-if="column.sortable">
                                    <svg class="h-4 w-4"
                                        :class="sortColumn === column.key ? 'text-indigo-600 dark:text-indigo-400' :
                                            'text-gray-400'"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            :d="sortColumn === column.key && sortDirection === 'asc' ?
                                                'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'" />
                                    </svg>
                                </template>
                            </div>
                        </th>
                    </template>
                    @if ($actions && !$peekType)
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-800 uppercase tracking-wider">
                            Actions
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <template x-if="paginatedData.length === 0 && !isLoading">
                    <tr>
                        <td :colspan="visibleColumns.length + {{ ($actions && !$peekType) ? 1 : 0 }} + (bulkActions.length > 0 ? 1 : 0)" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Aucune donnée disponible</p>
                                <button @click="clearFilters()"
                                    class="mt-2 text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                                    Réinitialiser les filtres
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>

                <template x-if="isLoading">
                    <tr>
                        <td :colspan="visibleColumns.length + {{ ($actions && !$peekType) ? 1 : 0 }} + (bulkActions.length > 0 ? 1 : 0)" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="animate-spin h-12 w-12 text-indigo-600 mb-3"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Chargement des données...</p>
                            </div>
                        </td>
                    </tr>
                </template>

                <template x-for="(row, rowIndex) in paginatedData" :key="rowIndex">
                    <tr :class="['cursor-pointer transition-colors', getRowClass(row), selectedRows.includes(row['{{ $rowKey }}']) ? 'bg-indigo-50/60 dark:bg-indigo-900/20' : '']"
                        @click="{{ $peekType ? 'openPeek(\'' . $peekType . '\', row)' : ($enableRowClick ? 'showRowDetails(row)' : '') }}">
                        @if(count($bulkActions) > 0)
                        <td class="pl-5 pr-2 py-4 w-10" @click.stop>
                            <input type="checkbox"
                                :checked="selectedRows.includes(row['{{ $rowKey }}'])"
                                @change="toggleRow(row['{{ $rowKey }}'])"
                                class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        </td>
                        @endif
                        <template x-for="column in visibleColumns" :key="column.key">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300{{ $peekType ? ' cursor-pointer' : '' }}"
                                @click.stop="{{ $peekType ? 'openPeek(\'' . $peekType . '\', row)' : '' }}">
                                <template x-if="column.type === 'image'">
                                    <img :src="row[column.key]" :alt="row.name || 'Image'"
                                        class="h-10 w-10 rounded-full object-cover">
                                </template>

                                <template x-if="column.type === 'badge'">
                                    <span :class="getBadgeClass(column.colorKey ? row[column.colorKey] : row[column.key], column.badgeColors)"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold whitespace-nowrap">
                                        <span x-show="column.showDot"
                                            :class="getBadgeDotClass(column.colorKey ? row[column.colorKey] : row[column.key], column.badgeColors)"
                                            class="w-1.5 h-1.5 rounded-full flex-shrink-0"></span>
                                        <span x-text="column.badgeLabels ? (column.badgeLabels[row[column.key]] ?? row[column.key]) : row[column.key]"></span>
                                    </span>
                                </template>

                                <template x-if="column.type === 'voting-toggle'">
                                    <div x-data="{
                                        showConfirmModal: false,
                                        currentRow: null,
                                        loading: false
                                    }">
                                        <!-- Bouton principal -->
                                        <button @click.stop="currentRow = row; showConfirmModal = true"
                                            :class="[
                                                'inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium transition-colors shadow-sm',
                                                (row.is_voting == '1' || row.is_voting === true || row.is_voting ===
                                                    1) ?
                                                'bg-green-600 text-white hover:bg-green-700' :
                                                'bg-yellow-500 text-white hover:bg-yellow-600'
                                            ]">
                                            <template
                                                x-if="row.is_voting == '1' || row.is_voting === true || row.is_voting === 1">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </template>
                                            <template
                                                x-if="!(row.is_voting == '1' || row.is_voting === true || row.is_voting === 1)">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </template>
                                            <span
                                                x-text="(row.is_voting == '1' || row.is_voting === true || row.is_voting === 1) ? 'Marquer non voté' : 'Marquer voté'"></span>
                                        </button>

                                        <!-- Modal de confirmation -->
                                        <div x-show="showConfirmModal"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                            class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
                                            style="display: none;" @keydown.escape.window="showConfirmModal = false">

                                            <div
                                                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6 border border-gray-200 dark:border-gray-700 pointer-events-auto">
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                                    <span
                                                        x-text="(currentRow && (currentRow.is_voting == '1' || currentRow.is_voting === true || currentRow.is_voting === 1)) ? 'Annuler le vote ?' : 'Confirmer le vote ?'"></span>
                                                </h3>

                                                <p class="text-gray-600 dark:text-gray-400 mb-6" x-show="currentRow">
                                                    <span
                                                        x-text="(currentRow && (currentRow.is_voting == '1' || currentRow.is_voting === true || currentRow.is_voting === 1)) 
                        ? `Voulez-vous vraiment marquer ${currentRow.name} comme n'ayant pas voté ?` 
                        : `Confirmez-vous que ${currentRow.name} a voté ?`"></span>
                                                </p>

                                                <div class="flex justify-end space-x-3">
                                                    <button @click="showConfirmModal = false" :disabled="loading"
                                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50">
                                                        Annuler
                                                    </button>
                                                    <button @click="updateVotingStatus(currentRow)"
                                                        :disabled="loading"
                                                        :class="[
                                                            'px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors',
                                                            (currentRow && (currentRow.is_voting == '1' || currentRow
                                                                .is_voting === true || currentRow.is_voting === 1)) ?
                                                            'bg-yellow-600 hover:bg-yellow-700' :
                                                            'bg-green-600 hover:bg-green-700',
                                                            loading ? 'opacity-50 cursor-not-allowed' : ''
                                                        ]">
                                                        <template x-if="loading">
                                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12"
                                                                    cy="12" r="10" stroke="currentColor"
                                                                    stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor"
                                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                </path>
                                                            </svg>
                                                        </template>
                                                        <span
                                                            x-text="(currentRow && (currentRow.is_voting == '1' || currentRow.is_voting === true || currentRow.is_voting === 1)) ? 'Oui, annuler' : 'Oui, confirmer'"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="column.type === 'date'">
                                    <span x-text="formatDate(row[column.key])"></span>
                                </template>

                                <template x-if="column.type === 'bulle'">
                                    <div class="flex items-center">
                                        <template x-if="row[column.key] && row[column.key].length > 0">
                                            <div class="flex -space-x-2">
                                                <template x-for="(m, mIdx) in (row[column.key] || []).slice(0, 5)" :key="mIdx">
                                                    <div class="w-7 h-7 rounded-full bg-indigo-100 border-2 border-white flex items-center justify-center text-xs font-bold text-indigo-700 ring-1 ring-indigo-200"
                                                         :title="m.name"
                                                         x-text="m.initials">
                                                    </div>
                                                </template>
                                                <template x-if="row[column.key].length > 5">
                                                    <div class="w-7 h-7 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-xs font-semibold text-gray-500 ring-1 ring-gray-300"
                                                         :title="'+' + (row[column.key].length - 5) + ' autres'"
                                                         x-text="'+' + (row[column.key].length - 5)">
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!row[column.key] || row[column.key].length === 0">
                                            <span class="text-xs text-gray-400">—</span>
                                        </template>
                                    </div>
                                </template>

                                <template
                                    x-if="!column.type || (column.type !== 'image' && column.type !== 'badge' && column.type !== 'date' && column.type !== 'bulle' && column.type !== 'voting-toggle')">
                                    <span x-text="row[column.key]"></span>
                                </template>
                            </td>
                        </template>
@if ($actions && !$peekType)
                            <td class="px-2 py-0.5 whitespace-nowrap text-right">
                                <div class="relative inline-block text-left" x-data="{
                                    open: false,
                                    position: 'bottom',
                                    checkPosition() {
                                        this.$nextTick(() => {
                                            const buttonRect = this.$refs.button.getBoundingClientRect();
                                            const dropdownHeight = 200;
                                            const spaceBelow = window.innerHeight - buttonRect.bottom;
                                            const spaceAbove = buttonRect.top;
                                            this.position = (spaceBelow < dropdownHeight && spaceAbove > spaceBelow) ? 'top' : 'bottom';
                                        });
                                    }
                                }"
                                    @keydown.escape.window="open = false">

                                    <button x-ref="button" @click="open = !open; checkPosition()" type="button"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wider transition-all duration-200 rounded border
                       bg-white text-gray-600 border-gray-300 hover:bg-gray-50
                       dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700
                       focus:outline-none focus:ring-1 focus:ring-emerald-500 shadow-sm"
                                        :class="{ 'bg-gray-100 dark:bg-gray-700': open }">
                                        <span>Actions</span>
                                        <svg class="w-3 h-3" :class="{ 'rotate-180': open }" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div x-show="open" x-cloak @click.away="open = false"
                                        x-transition:enter="transition ease-out duration-75"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        :class="{
                                            'bottom-full mb-1 origin-bottom-right': position === 'top',
                                            'top-full mt-1 origin-top-right': position === 'bottom'
                                        }"
                                        class="absolute right-0 w-40 rounded border shadow-lg z-50
                            bg-white border-gray-200 dark:bg-gray-900 dark:border-gray-700">

                                        <div class="py-0.5 divide-y divide-gray-100 dark:divide-gray-800">
                                            <template x-for="(action, index) in getActionsForRow(row)"
                                                :key="index">
                                                <div class="block">
                                                    <template x-if="action.divider">
                                                        <div
                                                            class="my-0.5 border-t border-gray-100 dark:border-gray-800">
                                                        </div>
                                                    </template>

                                                    <template x-if="!action.divider">
                                                        <a :href="action.href || '#'" :id="action.id"
                                                            @click.prevent=" 
                                            if (action.onclick) { executeAction(action.onclick, row); } 
                                            else if (action.href && action.href !== '#') { window.location.href = action.href; }
                                            open = false;
                                   "
                                                            :class="[
                                                                'group flex items-center px-2 py-1 text-[11px] font-medium transition-colors',
                                                                'text-gray-700 hover:text-white',
                                                                'dark:text-gray-400 dark:hover:text-white',
                                                                action.class ? action.class :
                                                                'hover:bg-emerald-600 dark:hover:bg-emerald-500'
                                                            ]"
                                                            role="menuitem">

                                                            <i :class="action.icon"
                                                                class="mr-2 w-3.5 text-center text-gray-400 group-hover:text-white opacity-80"></i>

                                                            <span x-text="action.label"
                                                                class="truncate uppercase tracking-tight"></span>
</a>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </td>
                    </tr>
                </template>
            </tbody>
            </table>
        </div>

        {{-- end datatable card --}}
    <div class="bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-700"
        x-show="!isLoading && filteredData.length > 0">
        <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
            <!-- Results Info -->
            <div class="text-sm text-gray-700 dark:text-gray-300">
                Affichage de <span class="font-medium" x-text="((currentPage - 1) * perPage) + 1"></span> à
                <span class="font-medium" x-text="Math.min(currentPage * perPage, filteredData.length)"></span> sur
                <span class="font-medium" x-text="filteredData.length"></span> résultats
            </div>

            <!-- Pagination Controls -->
            <div class="flex items-center space-x-2">
                <button @click="currentPage--; filterData()" :disabled="currentPage === 1"
                    :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300">
                    Précédent
                </button>

                <template x-for="page in getVisiblePages()" :key="page">
                    <button @click="currentPage = page; filterData()"
                        :class="currentPage === page ? 'bg-indigo-600 text-white border-indigo-600' :
                            'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="px-3 py-1 border rounded-lg text-sm font-medium min-w-8" x-text="page"></button>
                </template>

                <button @click="currentPage++; filterData()" :disabled="currentPage === totalPages"
                    :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' :
                        'hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300">
                    Suivant
                </button>
</div>
        </div>
    </div>{{-- end datatable card --}}

    <!-- Modal -->
<div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
    role="dialog" aria-modal="true" @keydown.escape.window="showModal = false">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="closeModal()"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="showModal" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 sm:mx-0 sm:h-10 sm:w-10"
                        x-show="!modalConfirm">
                        <svg class="h-6 w-6 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"
                        x-show="modalConfirm">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" x-text="modalTitle">
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-300" x-html="modalContent"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button x-show="modalConfirm" @click="executeModalAction()" type="button"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Confirmer
                </button>
                <button @click="closeModal()" type="button"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    <span x-text="modalConfirm ? 'Annuler' : 'Fermer'"></span>
                </button>
            </div>
        </div>
    </div>
</div>{{-- end modal --}}

@if($importable)
{{-- ===== IMPORT MODAL ===== --}}
<div x-data="{
    open: false,
    file: null,
    fileName: '',
    uploading: false,
    errors: [],
    init() {
        this.$el.addEventListener('open-import-modal-{{ $id }}', () => { this.open = true; this.file = null; this.fileName = ''; this.errors = []; });
    },
    pickFile(e) {
        this.file = e.target.files[0] || null;
        this.fileName = this.file ? this.file.name : '';
        this.errors = [];
    },
    async submit() {
        if (!this.file) return;
        this.uploading = true;
        this.errors = [];
        const fd = new FormData();
        fd.append('file', this.file);
        fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
        try {
            const resp = await axios.post('{{ $resolvedImportUrl }}', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
            const msg = resp.data?.message || 'Import effectué avec succès';
            if (typeof NotificationManager !== 'undefined') NotificationManager.show(msg, 'success');
            this.open = false;
            setTimeout(() => window.location.reload(), 700);
        } catch(e) {
            const data = e.response?.data;
            if (data?.errors) {
                this.errors = Array.isArray(data.errors) ? data.errors : Object.values(data.errors).flat();
            } else {
                this.errors = [data?.message || 'Une erreur est survenue lors de l\'import.'];
            }
        } finally {
            this.uploading = false;
        }
    }
}"
    x-show="open" x-cloak
    @keydown.escape.window="open = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-gray-900/50" @click="open = false"></div>

    {{-- Modal panel --}}
    <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md"
        @click.stop
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Importer un fichier CSV</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Format accepté : .csv (séparateur point-virgule)</p>
                </div>
            </div>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5 space-y-4">

            {{-- Drop zone --}}
            <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed rounded-xl cursor-pointer transition-colors"
                :class="fileName ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20'">
                <template x-if="!fileName">
                    <div class="flex flex-col items-center gap-2 text-gray-400">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span class="text-sm font-medium">Cliquer pour choisir un fichier</span>
                        <span class="text-xs">ou glisser-déposer ici</span>
                    </div>
                </template>
                <template x-if="fileName">
                    <div class="flex flex-col items-center gap-2 text-indigo-600 dark:text-indigo-400">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-semibold" x-text="fileName"></span>
                        <span class="text-xs text-gray-400">Cliquer pour changer de fichier</span>
                    </div>
                </template>
                <input type="file" accept=".csv,text/csv" class="hidden" @change="pickFile($event)">
            </label>

            {{-- Errors --}}
            <template x-if="errors.length > 0">
                <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 p-3">
                    <p class="text-xs font-semibold text-red-700 dark:text-red-400 mb-1">Erreurs détectées :</p>
                    <ul class="text-xs text-red-600 dark:text-red-300 space-y-0.5 list-disc list-inside">
                        <template x-for="(err, i) in errors" :key="i">
                            <li x-text="err"></li>
                        </template>
                    </ul>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            <button type="button" @click="open = false"
                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                Annuler
            </button>
            <button type="button" @click="submit()" :disabled="!file || uploading"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <template x-if="uploading">
                    <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
                <span x-text="uploading ? 'Import en cours...' : 'Importer'"></span>
            </button>
        </div>
    </div>
</div>
@endif
</div>{{-- end modal --}}
</div>{{-- end x-data wrapper --}}

<script>
    function dataTable(initialData, columns, perPageInit, customActions, advancedFilters, filterOptions, rowStatusField, rowDeadlineField, rowDeadlineSkipStatuses, bulkActionsConfig, rowKeyConfig) {
        return {
            // Données
            data: Array.isArray(initialData) ? initialData : [],
            columns: Array.isArray(columns) ? columns : [],
            customActions: Array.isArray(customActions) ? customActions : [],
            advancedFilters: Array.isArray(advancedFilters) ? advancedFilters : [],
            filterOptions: filterOptions || {},
            rowStatusField: rowStatusField || null,
            rowDeadlineField: rowDeadlineField || null,
            rowDeadlineSkipStatuses: Array.isArray(rowDeadlineSkipStatuses) ? rowDeadlineSkipStatuses : [],
            bulkActions: Array.isArray(bulkActionsConfig) ? bulkActionsConfig : [],
            rowKey: rowKeyConfig || 'id',
            selectedRows: [],
            hiddenColumns: [],
            filteredData: [],
            paginatedData: [],

            // État
            search: '',
            activeFilters: {},
            dateFilters: {},
            savedViews: [],
            rangeFilters: {},
            quickFilters: [],
            activeQuickFilters: [],
            sortColumn: '',
            sortDirection: 'asc',
            currentPage: 1,
            perPage: perPageInit,
            totalPages: 1,
            isLoading: true,
            customFilterGroups: {},

            // Modal properties
            showModal: false,
            modalTitle: '',
            modalContent: '',
            modalConfirm: false,
            modalCallback: null,
            selectedRow: null,
            modalSize: '{{ $modalSize }}',

            // Computed properties
            get filterableColumns() {
                return this.columns.filter(col => col.filterable !== false);
            },

            get visibleColumns() {
                return this.columns.filter(col => !this.hiddenColumns.includes(col.key));
            },

            get advancedFiltersCount() {
                let count = Object.keys(this.activeFilters).filter(key => this.activeFilters[key]).length;
                count += Object.keys(this.dateFilters).filter(key => this.dateFilters[key].from || this.dateFilters[
                    key].to).length;
                count += Object.keys(this.rangeFilters).filter(key => this.rangeFilters[key].min || this
                    .rangeFilters[key].max).length;
                return count + this.activeQuickFilters.length;
            },

            get activeFilterTags() {
                const tags = [];

                // Filtres textuels
                Object.keys(this.activeFilters).forEach(key => {
                    if (this.activeFilters[key]) {
                        const column = this.columns.find(col => col.key === key);
                        tags.push({
                            key: `filter_${key}`,
                            label: column?.label || key,
                            value: this.activeFilters[key]
                        });
                    }
                });

                // Filtres date
                Object.keys(this.dateFilters).forEach(key => {
                    const filter = this.dateFilters[key];
                    if (filter.from || filter.to) {
                        const column = this.columns.find(col => col.key === key);
                        let value = '';
                        if (filter.from && filter.to) {
                            value = `${filter.from} - ${filter.to}`;
                        } else if (filter.from) {
                            value = `≥ ${filter.from}`;
                        } else if (filter.to) {
                            value = `≤ ${filter.to}`;
                        }
                        tags.push({
                            key: `date_${key}`,
                            label: column?.label || key,
                            value: value
                        });
                    }
                });

                // Filtres range
                Object.keys(this.rangeFilters).forEach(key => {
                    const filter = this.rangeFilters[key];
                    if (filter.min || filter.max) {
                        const column = this.columns.find(col => col.key === key);
                        let value = '';
                        if (filter.min && filter.max) {
                            value = `${filter.min} - ${filter.max}`;
                        } else if (filter.min) {
                            value = `≥ ${filter.min}`;
                        } else if (filter.max) {
                            value = `≤ ${filter.max}`;
                        }
                        tags.push({
                            key: `range_${key}`,
                            label: column?.label || key,
                            value: value
                        });
                    }
                });

                return tags;
            },

            init() {
                console.log('Initializing Advanced DataTable with:', this.data.length, 'items');

                this.generateFilterOptions();

                // Initialiser les filtres personnalisés
                this.initializeCustomFilters();

                this.filteredData = [...this.data];
                this.isLoading = false;
                this.filterData();

                // Charger les vues sauvegardées
                this.initSavedViews();

                // Initialiser les filtres
                this.filterableColumns.forEach(col => {
                    if (col.filterType === 'date-range' || col.type === 'date') {
                        if (!this.dateFilters[col.key]) {
                            this.dateFilters[col.key] = {
                                from: '',
                                to: ''
                            };
                        }
                    }
                    if (col.filterType === 'range') {
                        if (!this.rangeFilters[col.key]) {
                            this.rangeFilters[col.key] = {
                                min: '',
                                max: ''
                            };
                        }
                    }
                });

                // Initialiser les filtres rapides
                this.quickFilters = this.advancedFilters.filter(f => f.quickFilter) || [];

                this.filteredData = [...this.data];
                this.isLoading = false;
                this.filterData();
            },

            getFilterOptions(columnKey) {
                if (this.filterOptions[columnKey]) {
                    return this.filterOptions[columnKey];
                }

                // Extraire les options uniques des données
                const uniqueValues = [...new Set(this.data.map(item => item[columnKey]))].filter(v => v != null);
                return uniqueValues.map(value => ({
                    value: value,
                    label: value
                }));
            },

            updatePaginatedData() {
                const start = (this.currentPage - 1) * this.perPage;
                const end = start + this.perPage;
                this.paginatedData = this.filteredData.slice(start, end);
            },

            sort(column) {
                if (this.sortColumn === column) {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortColumn = column;
                    this.sortDirection = 'asc';
                }
                this.filterData();
            },

            applyQuickFilter(filter) {
                const index = this.activeQuickFilters.indexOf(filter.key);
                if (index > -1) {
                    this.activeQuickFilters.splice(index, 1);
                } else {
                    this.activeQuickFilters.push(filter.key);
                }
                this.filterData();
            },

            removeFilter(filterKey) {
                if (filterKey.startsWith('filter_')) {
                    const key = filterKey.replace('filter_', '');
                    this.activeFilters[key] = '';
                } else if (filterKey.startsWith('date_')) {
                    const key = filterKey.replace('date_', '');
                    this.dateFilters[key] = {
                        from: '',
                        to: ''
                    };
                } else if (filterKey.startsWith('range_')) {
                    const key = filterKey.replace('range_', '');
                    this.rangeFilters[key] = {
                        min: '',
                        max: ''
                    };
                }
                this.filterData();
            },

            clearFilters() {
                this.activeFilters = {};
                this.dateFilters = {};
                this.rangeFilters = {};
                this.activeQuickFilters = [];
                this.search = '';
                this.filterData();
            },

            clearAdvancedFilters() {
                this.clearFilters();
                // Réinitialiser les structures de filtres
                this.filterableColumns.forEach(col => {
                    if (col.filterType === 'date-range' || col.type === 'date') {
                        this.dateFilters[col.key] = {
                            from: '',
                            to: ''
                        };
                    }
                    if (col.filterType === 'range') {
                        this.rangeFilters[col.key] = {
                            min: '',
                            max: ''
                        };
                    }
                });
            },

            getBadgeClass(value, colors) {
                if (!colors) return 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                return colors[value] || 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
            },

            getBadgeDotClass(value, colors) {
                if (!colors || !colors[value]) return 'bg-gray-400';
                const cls = colors[value];
                const match = cls.match(/text-(\w+)-\d+/);
                return match ? `bg-${match[1]}-500` : 'bg-gray-400';
            },

            getRowAccentClass(status) {
                const map = {
                    // Invoice / Quote statuses
                    'overdue':   'border-l-4 border-red-400 bg-red-50/40 dark:bg-red-900/10',
                    'paid':      'border-l-4 border-emerald-400 bg-emerald-50/30 dark:bg-emerald-900/10',
                    'partial':   'border-l-4 border-amber-400 bg-amber-50/30 dark:bg-amber-900/10',
                    'sent':      'border-l-4 border-emerald-300',
                    'draft':     'border-l-4 border-gray-300',
                    'cancelled': 'border-l-4 border-gray-200 opacity-60',
                    'declined':  'border-l-4 border-red-300 opacity-75',
                    'approved':  'border-l-4 border-emerald-400',
                    'pending':   'border-l-4 border-amber-300',
                    // Project statuses
                    'active':    'border-l-4 border-emerald-400',
                    'planning':  'border-l-4 border-emerald-300',
                    'on_hold':   'border-l-4 border-amber-400',
                    'completed': 'border-l-4 border-indigo-400 opacity-80',
                    // Interventions staus
                    'completed': 'border-l-4 border-emerald-400',
                    'cancelled': 'border-l-4 border-gray-200 opacity-60',
                };
                return map[status] || map[String(status).toLowerCase()] || '';
            },

            getRowClass(row) {
                // 1. Deadline overrides when date is past and status is not excluded
                if (this.rowDeadlineField) {
                    const dateVal = row[this.rowDeadlineField];
                    if (dateVal) {
                        const d = new Date(dateVal);
                        const isPast = !isNaN(d.getTime()) && d < new Date();
                        const currentStatus = this.rowStatusField ? row[this.rowStatusField] : null;
                        const skip = currentStatus && this.rowDeadlineSkipStatuses.includes(currentStatus);
                        if (isPast && !skip) {
                            return 'border-l-4 border-red-400 bg-red-50/50 dark:bg-red-900/10 hover:bg-red-50 dark:hover:bg-red-900/20';
                        }
                    }
                }
                // 2. Status accent
                if (this.rowStatusField) {
                    const accent = this.getRowAccentClass(row[this.rowStatusField]);
                    return (accent || '') + ' hover:bg-gray-50 dark:hover:bg-gray-800';
                }
                return 'hover:bg-gray-50 dark:hover:bg-gray-800';
            },

            getButtonHref(column, row) {
                if (typeof column.href === 'function') {
                    return column.href(row);
                } else if (column.href) {
                    return column.href.replace(/{(\w+)}/g, (match, key) => row[key] || '');
                }
                return '#';
            },

            getButtonLabel(column, row) {
                if (typeof column.label === 'function') {
                    return column.label(row);
                } else if (column.label) {
                    return column.label;
                }
                return row[column.key] || 'Lien';
            },

            getButtonClass(column) {
                const baseClasses =
                    'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-colors';

                const typeClasses = {
                    'primary': 'bg-indigo-600 text-white hover:bg-indigo-700',
                    'secondary': 'bg-gray-200 text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300',
                    'success': 'bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-900 dark:text-green-200',
                    'danger': 'bg-red-100 text-red-800 hover:bg-red-200 dark:bg-red-900 dark:text-red-200',
                    'warning': 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200 dark:bg-yellow-900 dark:text-yellow-200',
                    'info': 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200 dark:bg-emerald-900 dark:text-emerald-200'
                };

                return `${baseClasses} ${typeClasses[column.buttonType || 'primary']}`;
            },

            formatDate(dateString) {
                if (!dateString) return '';
                try {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('fr-FR', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                } catch (e) {
                    return dateString;
                }
            },

            getVisiblePages() {
                const pages = [];
                const maxVisible = 5;
                let start = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
                let end = Math.min(this.totalPages, start + maxVisible - 1);

                if (end - start + 1 < maxVisible) {
                    start = Math.max(1, end - maxVisible + 1);
                }

                for (let i = start; i <= end; i++) {
                    pages.push(i);
                }
                return pages;
            },

            getActionsForRow(row) {
                if (this.customActions && this.customActions.length > 0) {
                    return this.customActions.map(action => {
                        const processedAction = {
                            ...action
                        };

                        if (action.href) {
                            processedAction.href = this.replacePlaceholders(action.href, row);
                        }

                        return processedAction;
                    });
                }

                return [{
                        label: 'Voir',
                        icon: 'fas fa-eye',
                        type: 'info',
                        onclick: 'viewRow'
                    },
                    {
                        label: 'Modifier',
                        icon: 'fas fa-edit',
                        type: 'default',
                        onclick: 'editRow'
                    },
                    {
                        divider: true
                    },
                    {
                        label: 'Supprimer',
                        icon: 'fas fa-trash',
                        type: 'danger',
                        onclick: 'deleteRow'
                    }
                ];
            },

            replacePlaceholders(template, row) {
                return template.replace(/{(\w+)}/g, (match, key) => {
                    return row[key] || match;
                });
            },

            executeAction(actionName, row) {
                // 1. Vérifier si c'est une fonction locale à l'objet Alpine
                if (typeof this[actionName] === 'function') {
                    this[actionName](row);
                }
                // 2. Vérifier si c'est une fonction globale (window.votreFonction)
                else if (typeof window[actionName] === 'function') {
                    window[actionName](row);
                }
                // 3. Si actionName est déjà la référence d'une fonction
                else if (typeof actionName === 'function') {
                    actionName(row);
                } else {
                    console.warn('Action non trouvée:', actionName);
                }
            },

            getActionClass(type) {
                const classes = {
                    'info': 'text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20',
                    'success': 'text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20',
                    'warning': 'text-yellow-600 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20',
                    'danger': 'text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20',
                    'default': 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600'
                };
                return classes[type] || classes['default'];
            },

            exportCSV() {
                const headers = this.columns.map(col => `"${col.label}"`).join(',');
                const rows = this.filteredData.map(row =>
                    this.columns.map(col => {
                        let value = row[col.key] || '';
                        if (typeof value === 'string') {
                            value = `"${value.replace(/"/g, '""')}"`;
                        }
                        return value;
                    }).join(',')
                ).join('\n');

                const csv = headers + '\n' + rows;
                const blob = new Blob([csv], {
                    type: 'text/csv;charset=utf-8;'
                });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', 'export.csv');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },

            exportExcel() {
                this.exportCSV();
            },

            printTable() {
                window.print();
            },

            viewRow(row) {
                this.openModal('Détails',
                    `<pre class="text-sm whitespace-pre-wrap">${JSON.stringify(row, null, 2)}</pre>`);
            },

            showRowDetails(row) {
                this.selectedRow = row;
                this.showModal = true;
            },

            closeModal() {
                this.showModal = false;
                this.selectedRow = null;
            },

            editRow(row) {
                if (typeof NotificationManager !== 'undefined') {
                    NotificationManager.show('Fonction de modification à implémenter', 'info');
                }
                console.log('Edit row:', row);
            },

            deleteRow(row) {
                this.openConfirmModal(
                    'Confirmer la suppression',
                    'Êtes-vous sûr de vouloir supprimer cet élément ?',
                    () => {
                        if (typeof NotificationManager !== 'undefined') {
                            NotificationManager.show('Élément supprimé', 'success');
                        }
                        console.log('Delete row:', row);
                    }
                );
            },
            generateFilterOptions() {
                // Générer automatiquement les options distinctes pour chaque colonne
                this.columns.forEach(column => {
                    if (column.filterable && !this.filterOptions[column.key]) {
                        const uniqueValues = [...new Set(this.data.map(item => item[column.key]).filter(v =>
                            v != null && v !== ''))];
                        this.filterOptions[column.key] = uniqueValues.map(value => ({
                            value: value,
                            label: value
                        }));
                    }
                });
            },

            initializeCustomFilters() {
                // Définir des groupes de filtres personnalisés
                this.customFilterGroups = {
                    'Commune': {
                        key: 'commune',
                        options: this.getFilterOptions('commune') || []
                    },
                    'Lieu de vote': {
                        key: 'lieu_de_vote',
                        options: this.getFilterOptions('lieu_de_vote') || []
                    },
                    'Statut d\'electeur': {
                        key: 'status',
                        options: this.getFilterOptions('status') || []
                    },
                    'Statut Vote': {
                        key: 'is_voting',
                        options: this.getFilterOptions('is_voting') || []
                    }
                };
            },

            getFilterOptions(columnKey) {
                // Retourner les options pour une colonne spécifique
                if (this.filterOptions && this.filterOptions[columnKey]) {
                    return this.filterOptions[columnKey];
                }

                // Extraire les options uniques des données
                const uniqueValues = [...new Set(this.data.map(item => item[columnKey]).filter(v => v != null && v !==
                    ''))];
                return uniqueValues.map(value => ({
                    value: value,
                    label: value
                }));
            },

            applyDatePeriodFilter() {
                if (!this.activeFilters.date_period) {
                    this.dateFilters.created_at = {
                        from: '',
                        to: ''
                    };
                    this.filterData();
                    return;
                }

                const today = new Date();
                let fromDate = new Date();
                let toDate = new Date();

                switch (this.activeFilters.date_period) {
                    case 'today':
                        fromDate.setHours(0, 0, 0, 0);
                        toDate.setHours(23, 59, 59, 999);
                        break;
                    case 'yesterday':
                        fromDate.setDate(today.getDate() - 1);
                        fromDate.setHours(0, 0, 0, 0);
                        toDate.setDate(today.getDate() - 1);
                        toDate.setHours(23, 59, 59, 999);
                        break;
                    case 'last_7_days':
                        fromDate.setDate(today.getDate() - 7);
                        fromDate.setHours(0, 0, 0, 0);
                        toDate.setHours(23, 59, 59, 999);
                        break;
                    case 'last_30_days':
                        fromDate.setDate(today.getDate() - 30);
                        fromDate.setHours(0, 0, 0, 0);
                        toDate.setHours(23, 59, 59, 999);
                        break;
                    case 'this_month':
                        fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
                        toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0, 23, 59, 59, 999);
                        break;
                    case 'last_month':
                        fromDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                        toDate = new Date(today.getFullYear(), today.getMonth(), 0, 23, 59, 59, 999);
                        break;
                }

                // Formater les dates au format YYYY-MM-DD pour les inputs date
                this.dateFilters.created_at = {
                    from: fromDate.toISOString().split('T')[0],
                    to: toDate.toISOString().split('T')[0]
                };

                this.filterData();
            },

            /* ── Saved Views ──────────────────────────────────────────── */
            initSavedViews() {
                try {
                    const stored = localStorage.getItem('sv_{{ $id }}');
                    this.savedViews = stored ? JSON.parse(stored) : [];
                } catch(e) { this.savedViews = []; }
            },
            saveCurrentView() {
                const name = prompt('Nom de la vue :');
                if (!name || !name.trim()) return;
                this.savedViews.push({
                    name: name.trim(),
                    search: this.search,
                    activeFilters: JSON.parse(JSON.stringify(this.activeFilters)),
                    dateFilters: JSON.parse(JSON.stringify(this.dateFilters)),
                });
                try { localStorage.setItem('sv_{{ $id }}', JSON.stringify(this.savedViews)); } catch(e) {}
            },
            applyView(view) {
                this.search = view.search || '';
                this.activeFilters = JSON.parse(JSON.stringify(view.activeFilters || {}));
                this.dateFilters = JSON.parse(JSON.stringify(view.dateFilters || {}));
                this.currentPage = 1;
                this.filterData();
            },
            deleteView(idx) {
                this.savedViews.splice(idx, 1);
                try { localStorage.setItem('sv_{{ $id }}', JSON.stringify(this.savedViews)); } catch(e) {}
            },

            /* ── Bulk Selection ──────────────────────────────────────────── */
            toggleSelectAll() {
                if (this.selectedRows.length === this.paginatedData.length && this.paginatedData.length > 0) {
                    this.selectedRows = [];
                } else {
                    this.selectedRows = this.paginatedData.map(row => row[this.rowKey]).filter(id => id != null);
                }
            },

            toggleRow(id) {
                const idx = this.selectedRows.indexOf(id);
                if (idx > -1) {
                    this.selectedRows.splice(idx, 1);
                } else {
                    this.selectedRows.push(id);
                }
            },

            async executeBulkAction(action) {
                if (this.selectedRows.length === 0) return;

                const doExec = async () => {
                    if (action.url) {
                        try {
                            const resp = await axios.post(action.url, {
                                ids: this.selectedRows,
                                action: action.action || ''
                            });
                            const msg = resp.data?.message || 'Action effectuée avec succès';
                            if (typeof NotificationManager !== 'undefined') {
                                NotificationManager.show(msg, 'success');
                            }
                            this.selectedRows = [];
                            if (action.reload !== false) {
                                setTimeout(() => window.location.reload(), 700);
                            }
                        } catch (e) {
                            const msg = e.response?.data?.message || 'Une erreur est survenue';
                            if (typeof NotificationManager !== 'undefined') {
                                NotificationManager.show(msg, 'error');
                            }
                        }
                    } else if (action.action && typeof window[action.action] === 'function') {
                        window[action.action](this.selectedRows);
                        this.selectedRows = [];
                    }
                };

                if (action.confirm) {
                    const label = action.label || 'cette action';
                    const count = this.selectedRows.length;
                    if (typeof confirmDelete === 'function') {
                        confirmDelete(
                            `Confirmer "${label}" sur ${count} élément(s) ?`,
                            doExec
                        );
                    } else if (confirm(`Confirmer "${label}" sur ${count} élément(s) ?`)) {
                        await doExec();
                    }
                } else {
                    await doExec();
                }
            },

            /* ── Column Visibility ───────────────────────────────────────── */
            toggleColumn(key) {
                const idx = this.hiddenColumns.indexOf(key);
                if (idx > -1) {
                    this.hiddenColumns.splice(idx, 1);
                } else {
                    // Don't hide if it's the last visible column
                    if (this.visibleColumns.length > 1) {
                        this.hiddenColumns.push(key);
                    }
                }
            },

            filterData() {
                this.isLoading = true;

                setTimeout(() => {
                    let filtered = [...this.data];

                    // Recherche globale
                    if (this.search.trim()) {
                        filtered = filtered.filter(row => {
                            return this.columns.some(column => {
                                const value = row[column.key];
                                return value && value.toString().toLowerCase().includes(this
                                    .search.toLowerCase());
                            });
                        });
                    }

                    // Filtres select
                    Object.keys(this.activeFilters).forEach(key => {
                        const filterValue = this.activeFilters[key];
                        if (filterValue && filterValue.trim() && key !== 'date_period') {
                            filtered = filtered.filter(row => {
                                const value = row[key];
                                return value && value.toString() === filterValue;
                            });
                        }
                    });

                    // Filtres date (pour la période)
                    Object.keys(this.dateFilters).forEach(key => {
                        const filter = this.dateFilters[key];
                        if (filter.from || filter.to) {
                            filtered = filtered.filter(row => {
                                const value = row[key];
                                if (!value) return false;

                                const dateValue = new Date(value);
                                let isValid = true;

                                if (filter.from) {
                                    const fromDate = new Date(filter.from);
                                    isValid = isValid && dateValue >= fromDate;
                                }
                                if (filter.to) {
                                    const toDate = new Date(filter.to);
                                    isValid = isValid && dateValue <= toDate;
                                }

                                return isValid;
                            });
                        }
                    });

                    // Filtres rapides
                    this.activeQuickFilters.forEach(filterKey => {
                        const filter = this.quickFilters.find(f => f.key === filterKey);
                        if (filter && filter.condition) {
                            if (typeof filter.condition === 'function') {
                                filtered = filtered.filter(row => filter.condition(row));
                            } else if (typeof this[filter.condition] === 'function') {
                                filtered = filtered.filter(row => this[filter.condition](row));
                            }
                        }
                    });

                    // Tri
                    if (this.sortColumn) {
                        filtered.sort((a, b) => {
                            let aVal = a[this.sortColumn];
                            let bVal = b[this.sortColumn];

                            // Gestion des dates
                            if (this.columns.find(col => col.key === this.sortColumn)?.type ===
                                'date') {
                                aVal = aVal ? new Date(aVal).getTime() : 0;
                                bVal = bVal ? new Date(bVal).getTime() : 0;
                            } else if (typeof aVal === 'string') {
                                aVal = aVal.toLowerCase();
                                bVal = bVal.toLowerCase();
                            }

                            if (this.sortDirection === 'asc') {
                                return aVal > bVal ? 1 : -1;
                            } else {
                                return aVal < bVal ? 1 : -1;
                            }
                        });
                    }

                    this.filteredData = filtered;
                    this.totalPages = Math.ceil(filtered.length / this.perPage) || 1;

                    if (this.currentPage > this.totalPages) {
                        this.currentPage = this.totalPages;
                    }

                    this.updatePaginatedData();
                    this.isLoading = false;
                }, 100);
            },

            openModal(title, content) {
                this.modalTitle = title;
                this.modalContent = content;
                this.modalConfirm = false;
                this.showModal = true;
            },

            openConfirmModal(title, content, callback) {
                this.modalTitle = title;
                this.modalContent = content;
                this.modalConfirm = true;
                this.modalCallback = callback;
                this.showModal = true;
            },

            closeModal() {
                this.showModal = false;
                this.modalTitle = '';
                this.modalContent = '';
                this.modalConfirm = false;
                this.modalCallback = null;
            },

            executeModalAction() {
                if (this.modalConfirm && this.modalCallback) {
                    this.modalCallback();
                }
                this.closeModal();
            },

            // Dans votre objet dataTable
            toggleVoting(row, columnKey) {
                // Basculer l'état localement
                row[columnKey] = !row[columnKey];

                // Mettre à jour via AJAX
                this.updateVotingStatus(row.id, row[columnKey]);
            },

            async updateVotingStatus(id, isVoting) {
                try {
                    const response = await fetch(`/admin/electeurs/${id}/voters`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            is_voting: isVoting
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        if (typeof NotificationManager !== 'undefined') {
                            NotificationManager.show(
                                isVoting ? 'Électeur marqué comme ayant voté' : 'Vote annulé',
                                'success'
                            );
                        }
                    }
                } catch (error) {
                    console.error('Error updating voting status:', error);
                    if (typeof NotificationManager !== 'undefined') {
                        NotificationManager.show('Erreur lors de la mise à jour', 'error');
                    }
                }
            },

            async confirmVotingToggle(row) {
                const newStatus = !row.is_voting;

                try {
                    this.isLoading = true;

                    const response = await fetch(`/admin/electeurs/${row.id}/voters`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            is_voting: newStatus,
                            voted_at: newStatus ? new Date().toISOString() : null
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Mettre à jour la ligne
                        row.is_voting = newStatus;
                        if (newStatus) {
                            row.voted_at = new Date().toISOString();
                        } else {
                            row.voted_at = null;
                        }

                        if (typeof NotificationManager !== 'undefined') {
                            NotificationManager.show(
                                newStatus ? 'Vote confirmé avec succès' : 'Vote annulé avec succès',
                                'success'
                            );
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    if (typeof NotificationManager !== 'undefined') {
                        NotificationManager.show('Erreur lors de la mise à jour', 'error');
                    }
                } finally {
                    this.isLoading = false;
                }
            }

        }
    }
</script>


<style>
    [x-cloak] {
        display: none !important;
    }

    @media print {

        .bg-gray-50,
        .bg-white,
        .bg-gray-100 {
            background-color: white !important;
        }

        .text-gray-500,
        .text-gray-700,
        .text-gray-900 {
            color: black !important;
        }
    }
</style>

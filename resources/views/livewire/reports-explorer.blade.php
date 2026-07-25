<div class="space-y-6 bg-slate-50 min-h-screen p-6 rounded-2xl" x-data="{ openFilter: null, showCheck: false }" @click.away="openFilter = null">

    {{-- 1. Barre de recherche et filtres supérieurs --}}
    {{-- <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-2 text-sm text-slate-600 font-semibold">
            <button wire:click="$set('selectedFarmId', null)" class="hover:text-indigo-600 transition-colors flex items-center gap-1.5">
                <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Accueil
            </button>
            <span class="text-slate-300">/</span>
            @if($selectedFarmId && $currentFarm = \App\Models\Farm::find($selectedFarmId))
                <span class="text-slate-900 font-bold bg-slate-100 px-2.5 py-1 rounded-lg">{{ $currentFarm->name }}</span>
            @else
                <span class="text-slate-400 font-medium">./</span>
            @endif
        </div>

        <div class="relative w-full md:w-96">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.1" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.4 4.4a7.5 7.5 0 0012.25 12.25z" />
            </svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Rechercher un rapport, un dossier..."
                class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-xl bg-slate-50 focus:outline-none focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-slate-900 placeholder:text-slate-400"
            >
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-xl">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75M3.75 10.125v3.75m16.5 0v3.75m-16.5-3.75v3.75" />
                </svg>
                Stockage : 14.2 GB / 50 GB
            </span>
        </div>
    </div> --}}

    {{-- 2. Section Dossiers (Exploitations) --}}
    {{-- @if(!$selectedFarmId)
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Dossiers (Exploitations)</h3>
                
                @if(auth()->user()->role === 'admin')
                    <div class="flex items-center gap-2">
                        <input type="text" placeholder="Ajouter une ferme..." class="px-3 py-1.5 text-xs border border-slate-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button class="p-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        </button>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($farms ?? \App\Models\Farm::all() as $farm)
                    <div class="group relative bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer overflow-hidden"
                         wire:click="$set('selectedFarmId', {{ $farm->id }})">
                        
                        <div class="absolute -top-4 -right-4 text-slate-100 opacity-60 group-hover:opacity-100 transition-opacity">
                            <svg width="80" height="80" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="80" cy="20" r="30" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 4"/>
                                <path d="M40 10l20 20-20 20" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-bold text-slate-800 tracking-tight group-hover:text-indigo-600 transition-colors">{{ $farm->name }}/</h4>
                                <p class="text-[10px] text-slate-400 mt-1">Créé le {{ $farm->created_at?->format('d M, Y') }}</p>
                            </div>

                            <div class="flex items-center justify-between pt-2">
                                <div class="h-10 w-10 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-100 transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-19.5 0A2.25 2.25 0 003 15v3a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18v-3a2.25 2.25 0 00-2.25-2.25m-16.5 0h16.5M3 12.75h18" />
                                    </svg>
                                </div>
                                
                                @if(auth()->user()->role === 'admin')
                                    <button onclick="event.stopPropagation()" wire:click="deleteFarm({{ $farm->id }})" wire:confirm="Supprimer ce dossier ?" class="p-1.5 text-slate-400 hover:text-red-500 rounded-lg hover:bg-slate-50 transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif --}}

    {{-- 3. Section Fichiers (Rapports) --}}
    <div class="space-y-3 pt-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Fichiers (Rapports récents)</h3>
            
            {{-- Sélection multiple Toggle --}}
            <div class="flex items-center gap-2">
                <label class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 cursor-pointer select-none">
                    <input type="checkbox" x-model="showCheck" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20 h-4 w-4">
                    Sélection multiple
                </label>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($reports as $report)
                @php
                    // Mappage dynamique des couleurs d'extensions de fichiers
                    $extensionData = match(strtolower($report->type)) {
                        'irrigation' => ['label' => 'XLS', 'color' => 'bg-emerald-600'],
                        'sol'        => ['label' => 'DOC', 'color' => 'bg-blue-600'],
                        'suivi'      => ['label' => 'PDF', 'color' => 'bg-rose-600'],
                        default      => ['label' => 'PDF', 'color' => 'bg-rose-600'],
                    };
                @endphp
                
                <div class="group relative bg-white border border-slate-200/80 rounded p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 overflow-hidden">
                    
                    {{-- Checkbox de sélection multiple conditionnelle --}}
                    <div x-show="showCheck" class="absolute top-4 left-4 z-10" x-cloak>
                        <input type="checkbox" wire:model.live="selected" value="{{ $report->id }}" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20 h-4 w-4">
                    </div>

                    {{-- Filigrane géométrique du fichier --}}
                    <div class="absolute -top-4 -right-4 text-slate-100 opacity-60 group-hover:opacity-100 transition-opacity">
                        <svg width="80" height="80" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="50" y="10" width="40" height="40" rx="6" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 4"/>
                            <path d="M40 40c10-5 20-5 30 0" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </div>

                    <div class="space-y-4">
                        {{-- Méta-données du fichier --}}
                        <div class="min-h-[50px] {{ $showCheck ? 'pl-6' : '' }}">
                            <h4 class="text-sm font-bold text-slate-800 tracking-tight line-clamp-1 group-hover:text-indigo-600 transition-colors" title="{{ $report->title }}">
                                {{ $report->title }}
                            </h4>
                            <p class="text-[10px] text-slate-400 mt-1">
                                {{ $report->created_at?->format('d M, Y H:i A') }} @if($report->size) | {{ $report->size }} @else | 8.45 MB @endif
                            </p>
                        </div>

                        {{-- Section basse de la carte --}}
                        <div class="flex items-end justify-between pt-2">
                            {{-- Badge d'extension du fichier (XLS, DOC, PDF) --}}
                            <div class="flex flex-col items-start gap-1">
                                {{-- <span class="inline-flex px-2 py-0.5 text-[9px] font-black text-white rounded-md tracking-wider shadow-sm uppercase {{ $extensionData['color'] }}">
                                    {{ $extensionData['label'] }}
                                </span> --}}
                                <img src="{{ asset('/img/pdf.png') }}" width="45px"  alt="">
                            </div>

                            {{-- Actions directes --}}
                            <div class="flex items-center gap-1">
                                {{-- Bouton d'Aperçu --}}
                                <a href="{{ route('admin.reports.show', $report->id) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-slate-50 transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </a>
                                {{-- Bouton de téléchargement --}}
                                <button wire:click="downloadReport({{ $report->id }})" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-slate-50 transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                </button>
                                {{-- Bouton de suppression (Administrateur) --}}
                                @if(auth()->user()->role === 'admin')
                                    <button wire:click="deleteReport({{ $report->id }})" wire:confirm="Supprimer ce fichier ?" class="p-1.5 text-slate-400 hover:text-red-500 rounded-lg hover:bg-slate-50 transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400">
                    Aucun rapport disponible dans ce dossier.
                </div>
            @endforelse
        </div>
    </div>

    {{-- 4. Zone d'Uploader / Glisser-déposer (Dropzone) --}}
    {{-- @if(auth()->user()->role === 'admin' || auth()->user()->role === 'technician')
        <div class="pt-6 border-t border-slate-200">
            <div class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-slate-300 rounded-2xl bg-white hover:bg-slate-50 transition-colors duration-200 cursor-pointer text-center relative group">
                <input type="file" wire:model="uploadedFiles" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                <div class="p-3 bg-slate-100 rounded-full text-slate-400 mb-3 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-800">Uploader un fichier <span class="font-normal text-slate-500">ou glisser-déposer</span></p>
                <p class="text-[10px] text-slate-400 mt-1 uppercase font-bold tracking-wider">PNG, JPG, PDF, XLS, et DOC</p>
            </div>
        </div>
    @endif --}}
</div>
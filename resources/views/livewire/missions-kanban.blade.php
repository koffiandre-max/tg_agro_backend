<div
    x-data="{
        showCreateModal: false,
        init() {
            this.initSortable();
            if (window.Livewire && Livewire.hook) {
                if (!this._hooked) {
                    this._hooked = true;
                    Livewire.hook('morph.updated', () => this.$nextTick(() => this.initSortable()));
                    this.initSortable();
                }
            } else {
                document.addEventListener('livewire:init', () => this.initSortable());
            }
        },
        initSortable() {
            if (typeof Sortable === 'undefined') {
                setTimeout(() => this.initSortable(), 250);
                return;
            }
            const container = document.getElementById('missions-kanban');
            if (!container) return;
            container.querySelectorAll('.kanban-column').forEach(column => {
                if (column._sortable) {
                    column._sortable.destroy();
                }
                column._sortable = new Sortable(column, {
                    group: 'missions',
                    animation: 180,
                    handle: '.drag-handle',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    onEnd: (evt) => {
                        const from = evt.from;
                        const to = evt.to;
                        if (!from || from === to) return;
                        const missionId = evt.item.getAttribute('data-mission-id');
                        const newStatus = to.getAttribute('data-status');
                        if (!missionId || !newStatus) return;
                        const csrf = document.querySelector('meta[name="csrf-token"]');
                        fetch('/technitian/missions/' + missionId + '/status', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf ? csrf.getAttribute('content') : '',
                            },
                            body: JSON.stringify({ status: newStatus }),
                        })
                        .then(res => { if (!res.ok) throw new Error('HTTP ' + res.status); return res.json(); })
                        .then(() => window.location.reload())
                        .catch(() => window.location.reload());
                    }
                });
            });
        }
    }"
    class="min-h-screen">

    @if(auth()->user()?->role === 'admin')
        <div class="mb-4 flex justify-end">
            <button type="button"
                    @click="showCreateModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nouvelle Mission
            </button>
        </div>
    @endif

    <div id="missions-kanban" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        @foreach($this->columns as $status => $columnConfig)
            <div class="flex flex-col h-full"
                 data-status="{{ $status }}">

                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold {{ $columnConfig['header'] }}">
                            {{ count($this->missions[$status] ?? []) }}
                        </span>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">
                            {{ $columnConfig['label'] }}
                        </h3>
                    </div>
                </div>

                <div class="kanban-column flex flex-col gap-3 flex-1 min-h-[200px] p-2 rounded-xl border border-dashed {{ $columnConfig['color'] }}">
                    @forelse($this->missions[$status] as $mission)
                        <div class="group relative bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg hover:border-gray-300 hover:-translate-y-0.5 transition-all duration-200 overflow-hidden"
                             data-mission-id="{{ $mission->id }}"
                             wire:key="mission-{{ $mission->id }}">

                            {{-- Barre d'accent colorée selon le statut --}}
                            <div class="h-1.5 w-full {{ $columnConfig['header'] }}"></div>

                            <div class="p-4">
                                <div class="flex items-start gap-2">
                                    <div class="drag-handle cursor-grab active:cursor-grabbing text-gray-300 hover:text-gray-500 transition-colors mt-0.5 shrink-0"
                                         title="Glisser pour changer le statut">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5m-16.5 6.75h16.5"/>
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-semibold text-gray-800 leading-snug line-clamp-2 break-words">
                                        {{ $mission->title }}
                                    </h4>
                                </div>

                                @if($mission->description)
                                    <p class="text-xs text-gray-500 mt-2 line-clamp-2 leading-relaxed">
                                        {{ $mission->description }}
                                    </p>
                                @endif
                                <div class="mt-3 flex flex-wrap items-center gap-1.5">
                                    @if($mission->farm)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-gray-50 border border-gray-100 text-[11px] font-medium text-gray-600">
                                            <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.188-1.066A2.25 2.25 0 012.25 17.5v-11.5a2.25 2.25 0 012.25-2.25h15a2.25 2.25 0 012.25 2.25v11.5a2.25 2.25 0 01-2.25 2.25L9 18.75v-8.25z"/>
                                            </svg>
                                            {{ $mission->farm->name }}
                                        </span>
                                    @endif

                                    @if($mission->technician && $mission->technician->user)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 border border-indigo-100 text-[11px] font-medium text-indigo-700">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                            </svg>
                                            {{ $mission->technician->user->name }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[11px] font-medium {{ $columnConfig['badge'] }}">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                                        </svg>
                                        {{ $mission->scheduled_date?->format('d/m/Y') ?? 'Sans date' }}
                                    </span>

                                    @if($mission->notes)
                                        <span class="text-gray-300 hover:text-gray-500 transition-colors" title="Notes">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487z"/>
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c0 .621.504 1.125 1.125 1.125h2.25"/>
                            </svg>
                            <p class="text-xs text-gray-400">Aucune mission</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>


    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .kanban-column {
            min-height: 200px;
        }
        .sortable-ghost { opacity: 0.4; background: #eef2ff !important; }
        .sortable-chosen { box-shadow: 0 0 0 2px #6366f1, 0 10px 20px -5px rgba(0,0,0,0.15); }
        .sortable-drag { opacity: 0.95; transform: rotate(2deg); }
    </style>


    @if(auth()->user()?->role === 'admin')
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-show="showCreateModal"
             x-cloak
             x-transition.opacity.duration.200ms>

            <div class="absolute inset-0 bg-black/50" @click="showCreateModal = false"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" @click.stop>
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Nouvelle Mission</h3>
                    <button type="button" @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Technicien <span class="text-red-500">*</span></label>
                        <select wire:model="new_technician_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Sélectionner un technicien —</option>
                            @foreach($this->technicians as $technician)
                                <option value="{{ $technician['id'] }}">
                                    {{ $technician['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('new_technician_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Exploitation <span class="text-red-500">*</span></label>
                        <select wire:model="new_farm_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Sélectionner une exploitation —</option>
                            @foreach($this->farms as $farm)
                                <option value="{{ $farm['id'] }}">
                                    {{ $farm['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('new_farm_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>


                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titre <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="new_title" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Titre de la mission">
                        @error('new_title')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea wire:model="new_description" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Description de la mission"></textarea>
                        @error('new_description')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date planifiée <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="new_scheduled_date" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @error('new_scheduled_date')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Statut <span class="text-red-500">*</span></label>
                            <select wire:model="new_status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                @foreach($this->columns as $status => $columnConfig)
                                    <option value="{{ $status }}">{{ $columnConfig['label'] }}</option>
                                @endforeach
                            </select>
                            @error('new_status')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button type="button" wire:click="createMission" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                        Créer la mission
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>



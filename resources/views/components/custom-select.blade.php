@props([
    'name',
    'options' => [],
    'selected' => '',
    'placeholder' => 'Sélectionner...',
    'required' => false,
    'disabled' => false,
])

<div x-data="{
    open: false,
    search: '',
    value: @js((string)($selected ?? '')),
    options: @js($options),
    placeholder: @js($placeholder),
    
    // Normalisation sécurisée des options en tableau [{id, label}]
    get normalizedOptions() {
        if (!this.options || typeof this.options !== 'object') return [];
        return Object.entries(this.options).map(([id, label]) => ({
            id: String(id),
            label: String(label)
        }));
    },

    get filteredOptions() {
        if (!this.search.trim()) {
            return this.normalizedOptions;
        }
        const query = this.search.toLowerCase();
        return this.normalizedOptions.filter(opt => 
            opt.label.toLowerCase().includes(query)
        );
    },

    get label() {
        if (this.value === null || this.value === undefined || String(this.value).trim() === '') {
            return this.placeholder;
        }
        const found = this.normalizedOptions.find(opt => opt.id === String(this.value));
        return found ? found.label : this.placeholder;
    },
    
    select(id) {
        if (@js($disabled)) return;
        
        this.value = String(id);
        this.open = false;
        this.search = '';

        // Notification et synchronisation natives pour les formulaires
        this.$nextTick(() => {
            if (this.$refs.hiddenInput) {
                this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                this.$refs.hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });
    }
}" 
@click.away="open = false; search = ''" 
@keydown.escape.window="open = false; search = ''"
@set-custom-select.window="if ($event.detail.name === @js($name)) value = String($event.detail.value)"
class="relative w-full" 
data-name="{{ $name }}">

    {{-- Input caché envoyant l'ID réel au formulaire PHP --}}
    <input x-ref="hiddenInput"
           type="hidden" 
           name="{{ $name }}" 
           :value="value" 
           {{ $required ? 'required' : '' }} 
           {{ $disabled ? 'disabled' : '' }}>

    {{-- Bouton principal --}}
    <button type="button"
            @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
            :aria-expanded="open"
            :disabled="@js($disabled)"
            class="w-full flex items-center justify-between px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
        <span class="truncate" :class="{ 'text-gray-400': !value || String(value).trim() === '', 'text-gray-900': value && String(value).trim() !== '' }">
            <span x-text="label"></span>
        </span>
        {{-- Icône Fleche (SVG Valide) --}}
        <svg class="w-4 h-4 ml-2 text-gray-400 transition-transform duration-200 shrink-0" 
             :class="{ 'rotate-180': open }" 
             fill="none" 
             viewBox="0 0 24 24" 
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Menu déroulant --}}
    <div x-show="open" 
         x-cloak 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-30 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
        
        {{-- Champ de recherche (Filtre avec icône SVG corrigée) --}}
        <div class="p-2 border-b border-gray-100 bg-gray-50 relative">
            <div class="relative">
                <input x-ref="searchInput"
                       x-model="search"
                       @keydown.stop
                       type="text" 
                       placeholder="Rechercher..." 
                       class="w-full pl-8 pr-3 py-1.5 text-sm bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                {{-- SVG Loupe au tracé syntaxiquement valide --}}
                <svg class="w-4 h-4 absolute left-2.5 top-2.5 text-gray-400 pointer-events-none" 
                     fill="none" 
                     viewBox="0 0 24 24" 
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <div class="py-1 max-h-52 overflow-y-auto">
            
            {{-- En-tête Placeholder non cliquable --}}
            @if($placeholder)
                <div x-show="!search" class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider select-none bg-gray-50/50 border-b border-gray-100 cursor-default">
                    {{ $placeholder }}
                </div>
            @endif

            {{-- Liste des options réelles --}}
            <template x-for="item in filteredOptions" :key="item.id">
                <button type="button"
                        @click.prevent="select(item.id)"
                        :class="{ 
                            'text-indigo-600 font-semibold bg-indigo-50': String(value) === String(item.id), 
                            'text-gray-700 hover:bg-gray-50': String(value) !== String(item.id) 
                        }"
                        class="block w-full text-left px-4 py-2 text-sm transition-colors truncate">
                    <span x-text="item.label"></span>
                </button>
            </template>

            {{-- Message si aucun résultat --}}
            <div x-show="filteredOptions.length === 0" 
                 class="px-4 py-3 text-sm text-gray-400 text-center select-none">
                Aucun résultat trouvé
            </div>

        </div>
    </div>
</div>
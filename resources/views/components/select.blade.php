@props([
    'name' => null,
    'id' => null,
    'options' => [],
    'value' => null,
    'placeholder' => 'Sélectionnez une option...',
    'multiple' => false,
    'searchable' => true,
    'clearable' => true,
    'disabled' => false,
    'error' => null,
])

@php
    $id = $id ?? $name ?? 'select-' . Str::random(8);
    $hasError = $error && $errors->has($error);
    
    // Normalisation intelligente des options
    $formattedOptions = collect($options)->map(function ($opt, $key) {
        if (!is_array($opt)) {
            // Si le tableau est indexé numériquement, la valeur est l'élément lui-même
            $val = is_int($key) ? $opt : $key;
            return [
                'value' => (string) $val,
                'label' => (string) $opt,
                'image' => null,
                'description' => null,
                'badge' => null,
            ];
        }
        return [
            'value' => (string) ($opt['value'] ?? $key),
            'label' => (string) ($opt['label'] ?? ''),
            'image' => $opt['image'] ?? null,
            'description' => $opt['description'] ?? null,
            'badge' => $opt['badge'] ?? null,
        ];
    })->values()->all();

    // Normalisation robuste de la valeur initiale
    $initialValue = old($name, $value);
    if ($multiple) {
        if (is_array($initialValue)) {
            $initialValue = array_map('strval', $initialValue);
        } elseif (!is_null($initialValue) && $initialValue !== '') {
            $initialValue = [(string) $initialValue];
        } else {
            $initialValue = [];
        }
    } else {
        $initialValue = is_null($initialValue) ? '' : (string) $initialValue;
    }
@endphp

<div 
    x-data="{
        open: false,
        search: '',
        focusedIndex: -1,
        options: {{ Js::from($formattedOptions) }},
        selectedValues: {{ Js::from($initialValue) }},
        isMultiple: {{ $multiple ? 'true' : 'false' }},
        isDisabled: {{ $disabled ? 'true' : 'false' }},

        toggle() {
            if (this.isDisabled) return;
            this.open = !this.open;
            if (this.open) {
                this.focusedIndex = -1;
                this.$nextTick(() => {
                    if (this.$refs.searchInput) {
                        this.$refs.searchInput.focus();
                    }
                });
            } else {
                this.search = '';
            }
        },

        close() {
            this.open = false;
            this.search = '';
            this.focusedIndex = -1;
        },

        select(option) {
            if (this.isDisabled || !option) return;
            const val = String(option.value);
            if (this.isMultiple) {
                if (!Array.isArray(this.selectedValues)) {
                    this.selectedValues = [];
                }
                if (this.isSelected(val)) {
                    this.deselect(val);
                } else {
                    this.selectedValues.push(val);
                }
            } else {
                this.selectedValues = val;
                this.close();
            }
        },

        deselect(value) {
            if (this.isDisabled) return;
            const val = String(value);
            if (this.isMultiple && Array.isArray(this.selectedValues)) {
                this.selectedValues = this.selectedValues.filter(v => String(v) !== val);
            }
        },

        clear() {
            if (this.isDisabled) return;
            this.selectedValues = this.isMultiple ? [] : '';
        },

        isSelected(value) {
            const val = String(value);
            if (this.isMultiple) {
                return Array.isArray(this.selectedValues) && this.selectedValues.some(v => String(v) === val);
            }
            return String(this.selectedValues) === val;
        },

        get selectedItem() {
            if (this.isMultiple) return null;
            return this.options.find(opt => String(opt.value) === String(this.selectedValues)) || null;
        },

        get selectedItems() {
            if (!this.isMultiple || !Array.isArray(this.selectedValues)) return [];
            return this.options.filter(opt => this.selectedValues.some(v => String(v) === String(opt.value)));
        },

        get hasSelection() {
            return this.isMultiple 
                ? Array.isArray(this.selectedValues) && this.selectedValues.length > 0 
                : (this.selectedValues !== '' && this.selectedValues !== null);
        },

        get filteredOptions() {
            if (!this.search) return this.options;
            const term = this.search.toLowerCase().trim();
            return this.options.filter(opt => 
                (opt.label && opt.label.toLowerCase().includes(term)) || 
                (opt.description && opt.description.toLowerCase().includes(term))
            );
        },

        focusNext() {
            if (!this.open) {
                this.toggle();
                return;
            }
            const count = this.filteredOptions.length;
            if (count === 0) return;
            this.focusedIndex = (this.focusedIndex + 1) % count;
        },

        focusPrevious() {
            if (!this.open) return;
            const count = this.filteredOptions.length;
            if (count === 0) return;
            this.focusedIndex = (this.focusedIndex - 1 + count) % count;
        },

        selectFocused() {
            if (!this.open) {
                this.toggle();
                return;
            }
            if (this.focusedIndex >= 0 && this.focusedIndex < this.filteredOptions.length) {
                this.select(this.filteredOptions[this.focusedIndex]);
            }
        }
    }"
    x-on:click.outside="close()"
    x-on:keydown.escape.window="close()"
    x-on:keydown.arrow-down.prevent="focusNext()"
    x-on:keydown.arrow-up.prevent="focusPrevious()"
    x-on:keydown.enter.prevent="selectFocused()"
    class="relative w-full"
>
    {{-- Input caché pour soumission du formulaire --}}
    @if($name)
        @if($multiple)
            <template x-for="val in selectedValues" :key="val">
                <input type="hidden" name="{{ $name }}[]" :value="val">
            </template>
        @else
            <input type="hidden" name="{{ $name }}" :value="selectedValues" id="{{ $id }}">
        @endif
    @endif

    {{-- Déclencheur Principal (Utilisation d'un div avec role="combobox" au lieu de <button> pour éviter l'erreur HTML de boutons imbriqués) --}}
    <div
        tabindex="0"
        role="combobox"
        :aria-expanded="open"
        :aria-disabled="isDisabled"
        x-on:click="toggle()"
        x-on:keydown.space.prevent="toggle()"
        @class([
            'relative w-full min-h-[42px] px-3 py-2 text-left bg-white dark:bg-gray-800 border rounded-lg shadow-sm focus:outline-none focus:ring-2 transition duration-150 flex items-center justify-between gap-2 cursor-pointer select-none',
            'border-red-500 focus:ring-red-500' => $hasError,
            'border-gray-300 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500' => !$hasError,
            'opacity-60 !cursor-not-allowed bg-gray-100 dark:bg-gray-900' => $disabled,
        ])
    >
        <div class="flex flex-wrap items-center gap-1.5 overflow-hidden flex-1">
            {{-- Mode Multiple : Tags --}}
            <template x-if="isMultiple && selectedItems.length > 0">
                <template x-for="item in selectedItems" :key="item.value">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 rounded-md">
                        <template x-if="item.image">
                            <img :src="item.image" :alt="item.label" class="w-3.5 h-3.5 rounded-full object-cover">
                        </template>
                        <span x-text="item.label"></span>
                        <span 
                            role="button"
                            tabindex="0"
                            x-on:click.stop="deselect(item.value)"
                            x-on:keydown.enter.stop="deselect(item.value)"
                            class="hover:text-indigo-900 dark:hover:text-indigo-100 focus:outline-none cursor-pointer px-0.5 leading-none"
                        >
                            &times;
                        </span>
                    </span>
                </template>
            </template>

            {{-- Mode Simple --}}
            <template x-if="!isMultiple && selectedItem">
                <div class="flex items-center gap-2 truncate">
                    <template x-if="selectedItem.image">
                        <img :src="selectedItem.image" :alt="selectedItem.label" class="w-5 h-5 rounded-full object-cover flex-shrink-0">
                    </template>
                    <span class="text-sm text-gray-900 dark:text-gray-100 truncate" x-text="selectedItem.label"></span>
                </div>
            </template>

            {{-- Placeholder --}}
            <template x-if="(isMultiple && selectedItems.length === 0) || (!isMultiple && !selectedItem)">
                <span class="text-sm text-gray-400 dark:text-gray-500 truncate">{{ $placeholder }}</span>
            </template>
        </div>

        {{-- Icones --}}
        <div class="flex items-center gap-1 flex-shrink-0 text-gray-400">
            @if($clearable)
                <span 
                    x-show="hasSelection && !isDisabled" 
                    x-on:click.stop="clear()" 
                    role="button"
                    tabindex="0"
                    title="Effacer la sélection"
                    class="p-0.5 hover:text-gray-600 dark:hover:text-gray-200 rounded focus:outline-none cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </span>
            @endif

            <svg class="w-4 h-4 transition-transform duration-200 pointer-events-none" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </div>

    {{-- Menu Déroulant --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg overflow-hidden"
        style="display: none;"
    >
        {{-- Recherche --}}
        @if($searchable)
            <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                <input
                    x-ref="searchInput"
                    x-model="search"
                    x-on:click.stop
                    x-on:keydown.space.stop
                    type="text"
                    placeholder="Rechercher..."
                    class="w-full px-3 py-1.5 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-gray-200"
                >
            </div>
        @endif

        {{-- Liste d'options --}}
        <ul class="max-h-60 overflow-y-auto py-1 divide-y divide-gray-50 dark:divide-gray-800">
            <template x-for="(option, index) in filteredOptions" :key="option.value">
                <li
                    x-on:click="select(option)"
                    x-on:mouseenter="focusedIndex = index"
                    :class="{
                        'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400': isSelected(option.value),
                        'bg-gray-100 dark:bg-gray-700/70': focusedIndex === index && !isSelected(option.value),
                        'hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-900 dark:text-gray-100': !isSelected(option.value) && focusedIndex !== index
                    }"
                    class="px-3 py-2.5 cursor-pointer text-sm flex items-center justify-between transition-colors select-none"
                >
                    <div class="flex items-center gap-3 overflow-hidden">
                        <template x-if="option.image">
                            <img :src="option.image" :alt="option.label" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                        </template>

                        <div class="flex flex-col truncate">
                            <span class="font-medium truncate" x-text="option.label"></span>
                            <template x-if="option.description">
                                <span class="text-xs text-gray-500 dark:text-gray-400 truncate" x-text="option.description"></span>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        <template x-if="option.badge">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300" x-text="option.badge"></span>
                        </template>

                        <template x-if="isSelected(option.value)">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                    </div>
                </li>
            </template>

            <template x-if="filteredOptions.length === 0">
                <li class="px-3 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                    Aucun résultat trouvé
                </li>
            </template>
        </ul>
    </div>

    @if($hasError)
        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $errors->first($error) }}</p>
    @endif
</div>
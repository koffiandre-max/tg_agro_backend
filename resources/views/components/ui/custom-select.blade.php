{{--
 ┌─────────────────────────────────────────────────────────────────────────────┐
 │  <x-custom-select>  — Composant select personnalisé                         │
 ├─────────────────────────────────────────────────────────────────────────────┤
 │  PROPS                                                                       │
 │   name        string   — nom du champ (requis)                               │
 │   id          string   — id HTML (défaut: name)                              │
 │   value       mixed    — valeur pré-sélectionnée                             │
 │   options     array    — tableau assoc ['val'=>'Label'] OU objets riches :   │
 │                          [['value'=>'', 'label'=>'', 'dot'=>'bg-...', 'desc']] │
 │   dotColors   array    — raccourci ['val' => 'bg-emerald-500', ...]          │
 │   placeholder string   — texte par défaut                                   │
 │   searchable  bool     — activer la recherche (défaut: true)                 │
 │   clearable   bool     — bouton effacer (défaut: false)                      │
 │   disabled    bool     — désactiver                                          │
 │   size        string   — 'sm'|'md'|'lg' (défaut: 'md')                      │
 │   class       string   — classes supplémentaires                             │
 │   addUrl      string   — URL du bouton "Ajouter" (affiché si liste vide)     │
 │   addLabel    string   — libellé du bouton (défaut: 'Ajouter')               │
 │   ajax        bool     — simuler un chargement AJAX lors de la recherche     │
 │                                                                               │
 │  USAGE BLADE                                                                  │
 │   <x-custom-select name="status"                                             │
 │       :options="['draft' => 'Brouillon', 'paid' => 'Payée']"                │
 │       :dot-colors="['draft' => 'bg-gray-400', 'paid' => 'bg-emerald-500']"  │
 │       placeholder="Choisir un statut…"                                       │
 │       clearable />                                                            │
 │                                                                               │
 │  USAGE JS (enhancement d'un <select> existant)                               │
 │   $customSelect('#mon-select')                                               │
 │   $customSelect('[data-cs]', { clearable: true, placeholder: '…' })         │
 │   $customSelect(el, { searchable: false })                                   │
 └─────────────────────────────────────────────────────────────────────────────┘
--}}
@props([
    'name'        => '',
    'id'          => null,
    'value'       => null,
    'options'     => [],
    'dotColors'   => [],   // raccourci: ['val' => 'bg-color-500']
    'placeholder' => 'Sélectionner…',
    'searchable'  => true,
    'clearable'   => false,
    'disabled'    => false,
    'size'        => 'md', // sm | md | lg
    'class'       => '',
    'addUrl'      => null,
    'addLabel'    => 'Ajouter',
    'ajax'        => false,
    'multiple'    => false,
])

@php
    $fieldId = $id ?? $name;
    $isMultiple = $multiple === true || $multiple === 'true';

    // Normaliser les options en tableau d'objets
    $normalizedOptions = [];
    foreach ($options as $key => $opt) {
        if (is_array($opt)) {
            $entry = $opt;
        } else {
            $entry = ['value' => (string) $key, 'label' => (string) $opt];
        }
        // Appliquer dotColors si présent
        $v = $entry['value'] ?? $key;
        if (!empty($dotColors[$v])) {
            $entry['dot'] = $dotColors[$v];
        }
        $normalizedOptions[] = $entry;
    }

    $sizeClasses = match($size) {
        'sm'    => 'py-1.5 px-3 text-xs',
        'lg'    => 'py-3 px-5 text-base',
        default => 'py-2 px-3 text-sm',  // CORRECTION: 'text-md' → 'text-sm'
    };

    // Pour multiple, value doit être un tableau - on le normalise
    $valueArray = $isMultiple ? (array) ($value ?? []) : [];
    $valueString = $isMultiple ? null : (string) ($value ?? '');

    // CORRECTION #9: Auto-append [] au name si multiple et pas déjà présent
    $fieldName = $isMultiple && !str_ends_with($name, '[]') ? $name . '[]' : $name;

    $config = json_encode([
        'options'     => $normalizedOptions,
        'value'       => $valueString,
        'values'      => $valueArray,
        'placeholder' => $placeholder,
        'searchable'  => (bool) $searchable,
        'clearable'   => (bool) $clearable,
        'addUrl'      => $addUrl,
        'addLabel'    => $addLabel,
        'ajax'        => (bool) $ajax,
        'multiple'    => $isMultiple,
    ]);
@endphp

<div class="relative {{ $class }}" x-data="$cs({{ $config }})" x-init="init()" x-modelable="value"
      @keydown="onKeydown($event)" @click.outside="close()">

    {{-- ── Hidden native select (pour la soumission du formulaire) ──────── --}}
    {{-- CORRECTION #9: Utilisation de $fieldName au lieu de $name --}}
    <select name="{{ $fieldName }}" id="{{ $fieldId }}" x-ref="native"
            class="sr-only" aria-hidden="true" tabindex="-1"
            @if($isMultiple) multiple @endif
            {{ $disabled ? 'disabled' : '' }}>
        @if(!$isMultiple)
        <option value=""></option>
        @endif
        @foreach($normalizedOptions as $opt)
            <option value="{{ $opt['value'] }}"
                    @if($isMultiple && in_array((string)$opt['value'], $valueArray)) selected @elseif(!$isMultiple && (string)$value === (string)$opt['value']) selected @endif>
                {{ $opt['label'] }}
            </option>
        @endforeach
    </select>

    {{-- ── Trigger ───────────────────────────────────────────────────────── --}}
    <button type="button" x-ref="trigger" @click="toggle()"
            :disabled="{{ $disabled ? 'true' : 'false' }}"
            class="relative w-full flex items-center justify-between gap-2 {{ $sizeClasses }}
                   bg-white dark:bg-gray-700 border rounded-lg
                   text-left text-gray-700 dark:text-gray-200
                   hover:border-indigo-400 dark:hover:border-indigo-500
                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                   transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :class="open
                ? 'border-indigo-400 dark:border-indigo-500 ring-2 ring-indigo-500'
                : 'border-gray-300 dark:border-gray-600'">

        {{-- Valeur sélectionnée / placeholder --}}
        <span class="flex items-center gap-2 min-w-0 flex-1 truncate">
            {{-- CORRECTION #3: Ajout du cas multiple avec 0 sélection --}}
            <template x-if="multiple && values.length > 0">
                <span class="truncate text-gray-900 dark:text-white" x-text="values.length + ' sélectionné(s)'"></span>
            </template>
            <template x-if="multiple && values.length === 0">
                <span class="truncate text-gray-400 dark:text-gray-500" x-text="placeholder"></span>
            </template>
            <template x-if="!multiple">
                <span class="truncate"
                      :class="selectedOpt ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'"
                      x-text="selectedOpt ? selectedOpt.label : placeholder"></span>
            </template>
        </span>

        {{-- Clear + Chevron --}}
        <span class="flex items-center gap-0.5 flex-shrink-0">
            {{-- CORRECTION #2: Condition corrigée pour le mode multiple --}}
            <span x-show="clearable && (value || values.length)" @click.stop="clear()" title="Effacer"
                  class="w-5 h-5 flex items-center justify-center rounded-full text-gray-400
                         hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </span>
            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200 flex-shrink-0"
                 :class="{ 'rotate-180': open }"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </span>
    </button>

    {{-- ── Dropdown (fixed pour échapper aux overflow:hidden des cards) ── --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         :style="`position:fixed;top:${_dropTop}px;left:${_dropLeft}px;width:${_dropWidth}px;z-index:9999`"
         class="bg-white dark:bg-gray-800
                border border-gray-200 dark:border-gray-700
                rounded-xl shadow-2xl overflow-hidden">

        {{-- Recherche --}}
        <div x-show="searchable" class="px-2.5 pt-2.5 pb-2 border-b border-gray-100 dark:border-gray-700">
            <div class="relative">
                {{-- Icône recherche --}}
                <svg x-show="!loading"
                     class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                {{-- Spinner AJAX --}}
                <svg x-show="loading"
                     class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-indigo-500 animate-spin pointer-events-none"
                     viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <input x-ref="search" x-model="search" @keydown.stop="onKeydown($event)"
                       type="text" placeholder="Rechercher…" autocomplete="off"
                       class="w-full pl-8 pr-3 py-1.5 text-sm
                              bg-gray-50 dark:bg-gray-700
                              border border-gray-200 dark:border-gray-600
                              rounded-lg placeholder-gray-400 dark:placeholder-gray-500
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                              text-gray-900 dark:text-white">
            </div>
        </div>

        {{-- Liste des options --}}
        <div class="overflow-y-auto" style="max-height:220px">

            {{-- Chargement AJAX --}}
            <div x-show="loading" class="px-3 py-6 text-center">
                <svg class="w-6 h-6 mx-auto text-indigo-400 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">Recherche en cours…</p>
            </div>

            {{-- Aucun résultat --}}
            <div x-show="!loading && filtered.length === 0"
                 class="px-3 py-6 text-center text-sm text-gray-400 dark:text-gray-500">
                <svg class="w-8 h-8 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Aucun résultat
                {{-- Bouton Ajouter --}}
                <div x-show="addUrl" class="mt-3">
                    <a :href="addUrl + (search.trim() ? '?name=' + encodeURIComponent(search.trim()) : '')"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                              bg-indigo-600 text-white rounded-lg shadow-sm
                              hover:bg-indigo-700 active:bg-indigo-800 transition-colors">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span x-text="search.trim() ? addLabel + ' «' + search.trim() + '»' : addLabel"></span>
                    </a>
                </div>
            </div>

            {{-- Options --}}
            <div x-show="!loading">
                <template x-for="(opt, idx) in filtered" :key="opt.value">
                    <div @click="!opt.disabled && select(opt)"
                         :class="{
                             'bg-indigo-50 dark:bg-indigo-900/30': isSelected(opt),
                             'bg-gray-50 dark:bg-gray-700/60': activeIdx === idx && !isSelected(opt),
                             'opacity-40 cursor-not-allowed': opt.disabled,
                             'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50': !opt.disabled && !isSelected(opt),
                         }"
                         class="flex items-center gap-2.5 px-3 py-2.5 transition-colors">

                        {{-- Dot couleur --}}
                        <span x-show="opt.dot" :class="opt.dot ?? ''"
                              class="w-2 h-2 rounded-full shrink-0"></span>

                        {{-- Texte + description --}}
                        <span class="flex-1 min-w-0">
                            <span class="block text-sm truncate"
                                  :class="isSelected(opt)
                                      ? 'font-medium text-indigo-700 dark:text-indigo-300'
                                      : 'text-gray-700 dark:text-gray-200'"
                                  x-text="opt.label"></span>
                            <span x-show="opt.description"
                                  class="block text-xs text-gray-400 dark:text-gray-500 truncate"
                                  x-text="opt.description ?? ''"></span>
                        </span>

                        {{-- Checkmark --}}
                        <svg x-show="isSelected(opt)"
                             class="w-4 h-4 shrink-0 text-indigo-600 dark:text-indigo-400"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

{{-- ── Script global (chargé une seule fois) ────────────────────────────── --}}
@once
<script>
/**
 * $cs(config) — Factory Alpine.js pour le composant custom-select
 */
window.$cs = function(config) {
    return {
        open:        false,
        search:      '',
        value:       config.value || null,
        values:      config.values || [],
        options:     config.options || [],
        placeholder: config.placeholder || 'Sélectionner…',
        searchable:  config.searchable !== false,
        clearable:   config.clearable  === true,
        addUrl:      config.addUrl     || null,
        addLabel:    config.addLabel   || 'Ajouter',
        ajax:        config.ajax       === true,
        loading:     false,
        _ajaxTimer:  null,
        _scrollH:    null,
        activeIdx:   -1,
        multiple:    config.multiple   === true,
        // Position fixe — échappe les overflow:hidden des cards
        _dropTop:    -9999,
        _dropLeft:   -9999,
        _dropWidth:  200,

        init() {
            this.$watch('value', v => {
                const sel = this.$refs.native;
                if (sel && !this.multiple) {
                    sel.value = v ?? '';
                    sel.dispatchEvent(new Event('change', { bubbles: true }));
                    sel.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });

            // CORRECTION #6: Ajout du dispatch d'event 'input' en mode multiple
            this.$watch('values', v => {
                const sel = this.$refs.native;
                if (sel && this.multiple) {
                    Array.from(sel.options).forEach(opt => {
                        opt.selected = v.includes(opt.value);
                    });
                    sel.dispatchEvent(new Event('change', { bubbles: true }));
                    sel.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });

            // CORRECTION #8: Réinitialiser activeIdx quand la recherche change
            this.$watch('search', () => {
                this.activeIdx = -1;
            });

            if (this.ajax || this.addUrl) {
                this.$watch('search', q => {
                    clearTimeout(this._ajaxTimer);
                    if (!q.trim()) { this.loading = false; return; }
                    this.loading = true;
                    this._ajaxTimer = setTimeout(() => { this.loading = false; }, 420);
                });
            }

            this._scrollH = (e) => {
                if (!this.open) return;
                // Vérifier que le scroll ne provient pas du dropdown lui-même
                const dropdown = this.$el.querySelector('div[x-show="open"]');
                if (dropdown && dropdown.contains(e.target)) return;
                this.close();
            };

            window.addEventListener('scroll', this._scrollH, true);
            window.addEventListener('resize', this._scrollH);

            // CORRECTION #5: Cleanup des listeners pour éviter les memory leaks
            this.$cleanup = () => {
                window.removeEventListener('scroll', this._scrollH, true);
                window.removeEventListener('resize', this._scrollH);
            };
        },

        destroy() {
            // CORRECTION #5: Nettoyage lors de la destruction du composant
            if (this.$cleanup) this.$cleanup();
        },

        _calcDropPos() {
            const btn = this.$refs.trigger;
            if (!btn) return;
            const r = btn.getBoundingClientRect();
            if (!r.width && !r.height) return;
            this._dropWidth = Math.max(r.width, 200);
            this._dropLeft  = Math.min(r.left, window.innerWidth - this._dropWidth - 8);
            // CORRECTION: S'assurer que le dropdown ne dépasse pas à gauche
            this._dropLeft = Math.max(8, this._dropLeft);
            const spaceBelow = window.innerHeight - r.bottom;
            const dropH      = 110;
            if (spaceBelow >= dropH || spaceBelow >= r.top) {
                this._dropTop = r.bottom + 4;
            } else {
                this._dropTop = Math.max(8, r.top - dropH - 4);
            }
        },

        get filtered() {
            if (!this.search.trim()) return this.options;
            const q = this.search.toLowerCase();
            return this.options.filter(o =>
                String(o.label).toLowerCase().includes(q) ||
                String(o.description ?? '').toLowerCase().includes(q)
            );
        },

        get selectedOpt() {
            if (!this.value) return null;
            return this.options.find(o => String(o.value) === String(this.value)) || null;
        },

        // CORRECTION #1: isSelected gère correctement les modes simple et multiple
        isSelected(opt) {
            if (this.multiple) {
                return this.values.includes(String(opt.value));
            }
            return String(this.value) === String(opt.value);
        },

        toggle() {
            if (this.open) { this.close(); return; }
            this.open = true;
            this.activeIdx = this.options.findIndex(o => this.isSelected(o));
            this.$nextTick(() => {
                this._calcDropPos();
                if (this.searchable && this.$refs.search) this.$refs.search.focus();
            });
        },

        close() {
            this.open = false;
            this.search = '';
            this.activeIdx = -1;
        },

        select(opt) {
            if (this.multiple) {
                const val = String(opt.value);
                if (this.values.includes(val)) {
                    this.values = this.values.filter(v => v !== val);
                } else {
                    this.values.push(val);
                }
            } else {
                this.value = String(opt.value);
                this.close();
            }
        },

        clear() {
            if (this.multiple) {
                this.values = [];
            } else {
                this.value = null;
            }
            this.search = '';
        },

        onKeydown(e) {
            const f = this.filtered;
            if (!this.open) {
                if (['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(e.key)) {
                    e.preventDefault();
                    this.open = true;
                }
                return;
            }
            if (e.key === 'Escape')   { e.preventDefault(); this.close(); return; }
            if (e.key === 'Tab')      { this.close(); return; }
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.activeIdx = this.activeIdx < f.length - 1 ? this.activeIdx + 1 : 0;
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.activeIdx = this.activeIdx > 0 ? this.activeIdx - 1 : f.length - 1;
            } else if (e.key === 'Enter' && this.activeIdx >= 0) {
                e.preventDefault();
                if (f[this.activeIdx] && !f[this.activeIdx].disabled) {
                    this.select(f[this.activeIdx]);
                }
            }
        },
    };
};

/**
 * $customSelect(selector, opts) — Enhancement d'un <select> HTML existant
 *
 * Exemples :
 *   $customSelect('#mon-select')
 *   $customSelect('[data-cs]', { clearable: true, placeholder: '…' })
 *   $customSelect(document.getElementById('s'), { searchable: false })
 */
window.$customSelect = (function() {

    function buildHTML(opts) {
        const cfg = JSON.stringify(opts).replace(/</g, '\u003c').replace(/>/g, '\u003e');
        // CORRECTION #7: Support du mode multiple dans buildHTML
        const multipleAttr = opts.multiple ? 'multiple' : '';
        const selectedValues = opts.multiple ? (opts.values || []) : [opts.value];

        return `
<div class="relative" x-data='$cs(${cfg})' x-init="init()" @keydown="onKeydown($event)" @click.outside="close()">
  <!-- native hidden -->
  <select x-ref="native" name="${opts._name||''}" id="${opts._id||''}"
          class="sr-only" aria-hidden="true" tabindex="-1" ${multipleAttr}>
    ${!opts.multiple ? '<option value=""></option>' : ''}
    ${opts.options.map(o=>{
        const isSelected = selectedValues.includes(String(o.value));
        return `<option value="${o.value}"${isSelected?' selected':''}>${o.label}</option>`;
    }).join('')}
  </select>
  <!-- trigger -->
  <button type="button" @click="toggle()"
          class="relative w-full flex items-center justify-between gap-2 py-2 px-3 text-sm
                 bg-white dark:bg-gray-700 border rounded-lg text-left
                 text-gray-700 dark:text-gray-200
                 hover:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                 transition-colors"
          :class="open ? 'border-indigo-400 ring-2 ring-indigo-500' : 'border-gray-300 dark:border-gray-600'">
    <span class="flex items-center gap-2 min-w-0 flex-1 truncate">
      ${opts.multiple ? `
      <template x-if="values.length > 0">
        <span class="truncate text-gray-900 dark:text-white" x-text="values.length + ' sélectionné(s)'"></span>
      </template>
      <template x-if="values.length === 0">
        <span class="truncate text-gray-400 dark:text-gray-500" x-text="placeholder"></span>
      </template>
      ` : `
      <span x-show="selectedOpt&&selectedOpt.dot" :class="selectedOpt?.dot??''" class="w-2 h-2 rounded-full flex-shrink-0"></span>
      <span class="truncate" :class="selectedOpt?'text-gray-900 dark:text-white':'text-gray-400'" x-text="selectedOpt?selectedOpt.label:placeholder"></span>
      `}
    </span>
    <span class="flex items-center gap-0.5 flex-shrink-0">
      <span x-show="clearable && (value || values.length)" @click.stop="clear()"
            class="w-5 h-5 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
      </span>
      <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180':open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </span>
  </button>
  <!-- dropdown (fixed pour échapper aux overflow:hidden des cards) -->
  <div x-show="open" x-cloak
       x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
       x-transition:leave="transition ease-in duration-75"   x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
       :style="\`position:fixed;top:\${_dropTop}px;left:\${_dropLeft}px;width:\${_dropWidth}px;z-index:9999\`"
       class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-2xl overflow-hidden">
    <div x-show="searchable" class="px-2.5 pt-2.5 pb-2 border-b border-gray-100 dark:border-gray-700">
      <div class="relative">
        <svg x-show="!loading" class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <svg x-show="loading" class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-indigo-500 animate-spin pointer-events-none" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
        <input x-ref="search" x-model="search" @keydown.stop="onKeydown($event)" type="text" placeholder="Rechercher…" autocomplete="off"
               class="w-full pl-8 pr-3 py-1.5 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900 dark:text-white">
      </div>
    </div>
    <div class="overflow-y-auto" style="max-height:220px">
      <div x-show="loading" class="px-3 py-6 text-center">
        <svg class="w-6 h-6 mx-auto text-indigo-400 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
        <p class="mt-2 text-xs text-gray-400">Recherche en cours…</p>
      </div>
      <div x-show="!loading && filtered.length===0" class="px-3 py-6 text-center text-sm text-gray-400">
        <svg class="w-8 h-8 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Aucun résultat
        <div x-show="addUrl" class="mt-3">
          <a :href="addUrl + (search.trim() ? '?name=' + encodeURIComponent(search.trim()) : '')"
             class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span x-text="search.trim() ? addLabel + ' «' + search.trim() + '»' : addLabel"></span>
          </a>
        </div>
      </div>
      <div x-show="!loading">
        <template x-for="(opt,idx) in filtered" :key="opt.value">
          <div @click="!opt.disabled&&select(opt)"
               :class="{'bg-indigo-50 dark:bg-indigo-900/30':isSelected(opt),'bg-gray-50 dark:bg-gray-700/60':activeIdx===idx&&!isSelected(opt),'opacity-40 cursor-not-allowed':opt.disabled,'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50':!opt.disabled&&!isSelected(opt)}"
               class="flex items-center gap-2.5 px-3 py-2.5 transition-colors">
            <span x-show="opt.dot" :class="opt.dot??''" class="w-2 h-2 rounded-full shrink-0"></span>
            <span class="flex-1 min-w-0">
              <span class="block text-sm truncate" :class="isSelected(opt)?'font-medium text-indigo-700 dark:text-indigo-300':'text-gray-700 dark:text-gray-200'" x-text="opt.label"></span>
              <span x-show="opt.description" class="block text-xs text-gray-400 truncate" x-text="opt.description??''"></span>
            </span>
            <svg x-show="isSelected(opt)" class="w-4 h-4 shrink-0 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
          </div>
        </template>
      </div>
    </div>
  </div>
</div>`;
    }

    return function $customSelect(selectorOrEl, userOpts = {}) {
        const els = typeof selectorOrEl === 'string'
            ? Array.from(document.querySelectorAll(selectorOrEl))
            : selectorOrEl instanceof NodeList
                ? Array.from(selectorOrEl)
                : [selectorOrEl];

        els.forEach(el => {
            if (!(el instanceof HTMLSelectElement)) return;
            if (el.dataset.csInit) return;
            el.dataset.csInit = '1';

            // Lire les options du select HTML
            const options = Array.from(el.options)
                .filter(o => o.value !== '' || (o.text.trim() && !/^[-–— ]+$/.test(o.text)))
                .map(o => ({
                    value:       o.value,
                    label:       o.text.trim(),
                    dot:         o.dataset.dot  || null,
                    description: o.dataset.desc || null,
                    disabled:    o.disabled,
                }));

            // Première option vide = placeholder
            const firstEmpty = Array.from(el.options).find(o => o.value === '');

            // CORRECTION #7 & #11: Détection du mode multiple et valeurs initiales
            const isMultiple = el.multiple;
            const selectedOptions = Array.from(el.selectedOptions).map(o => o.value);
            const initialValue = isMultiple ? null : (el.value || null);
            const initialValues = isMultiple ? selectedOptions : [];

            const cfg = {
                options,
                value:       initialValue,
                values:      initialValues,
                placeholder: userOpts.placeholder || el.dataset.placeholder
                             || firstEmpty?.text.trim()
                             || 'Sélectionner…',
                searchable:  userOpts.searchable  !== false && el.dataset.searchable !== 'false',
                clearable:   userOpts.clearable   === true  || el.dataset.clearable  === 'true',
                multiple:    isMultiple,
                addUrl:      userOpts.addUrl || el.dataset.addUrl || null,
                addLabel:    userOpts.addLabel || el.dataset.addLabel || 'Ajouter',
                ajax:        userOpts.ajax === true || el.dataset.ajax === 'true',
                _name:       el.name,
                _id:         el.id + '_cs',
                ...userOpts,
            };

            // CORRECTION #9: Auto-append [] au name si multiple
            if (isMultiple && cfg._name && !cfg._name.endsWith('[]')) {
                cfg._name += '[]';
            }

            // Créer le wrapper Alpine
            const wrapper = document.createElement('div');
            wrapper.innerHTML = buildHTML(cfg).trim();
            const root = wrapper.firstElementChild;

            // Masquer le select original
            el.hidden = true;
            el.parentNode.insertBefore(root, el.nextSibling);

            // Init Alpine sur le nouvel élément
            if (window.Alpine) {
                Alpine.initTree(root);
            } else {
                document.addEventListener('alpine:init', () => Alpine.initTree(root));
            }
        });
    };
})();
</script>
@endonce
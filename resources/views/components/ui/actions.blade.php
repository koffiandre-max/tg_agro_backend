@props([
    'actions' => [],
    'position' => 'bottom-right',
    'triggerIcon' => 'fas fa-chevron-down',
    'triggerClass' => '',
    'align' => 'right'
])

<div class="relative" x-data="{ open: false }" @click.away="open = false" @keydown.escape="open = false">
    <!-- Trigger Button -->
    <button
        type="button"
        @click="open = !open"
        class="{{ $triggerClass }} inline-flex items-center justify-center w-8 h-8 rounded hover:bg-gray-100 transition-colors duration-150"
        :class="{ 'bg-gray-100': open }"
    >
        <i class="{{ $triggerIcon }} text-gray-600 text-xs"></i>
    </button>

    <!-- Dropdown Menu -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-1 w-48 rounded-md shadow-lg border border-gray-200 bg-white py-1"
        style="display: none;"
        :class="{
            'right-0': '{{ $align }}' === 'right',
            'left-0': '{{ $align }}' === 'left'
        }"
    >
        @foreach($actions as $action)
            @if(isset($action['divider']) && $action['divider'])
                <div class="border-t border-gray-100 my-1"></div>
            @else
                <button
                    type="button"
                    @click="open = false"
                    class="w-full text-left px-3 py-2 text-sm flex items-center space-x-2 hover:bg-gray-50 transition-colors duration-150"
                    :class="{
                        'text-gray-700': '{{ $action['type'] ?? 'default' }}' === 'default',
                        'text-red-600': '{{ $action['type'] ?? 'default' }}' === 'danger'
                    }"
                    wire:click="{{ $action['wireClick'] ?? '' }}"
                >
                    @if(isset($action['icon']))
                        <i class="{{ $action['icon'] }} w-4 text-gray-400"></i>
                    @endif
                    <span>{{ $action['label'] }}</span>
                </button>
            @endif
        @endforeach
    </div>
</div> 
<!-- ====================================== -->
<!-- 1. BUTTON COMPONENT -->
<!-- resources/views/components/button.blade.php -->
<!-- ====================================== -->

@props([
    'variant' => 'primary', // primary, secondary, outline, danger, ghost
    'size' => 'md', // sm, md, lg
    'type' => 'button',
    'loading' => false,
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left', // left, right
])

@php
    $baseClasses =
        'inline-flex items-center justify-center font-medium transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $variants = [
        'primary' => 'bg-slate-900 text-white hover:bg-slate-800 focus-visible:outline-slate-900',
        'secondary' => 'bg-slate-100 text-slate-900 hover:bg-slate-200 focus-visible:outline-slate-900',
        'outline' =>
            'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 focus-visible:outline-slate-900',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus-visible:outline-red-600',
        'ghost' => 'text-slate-700 hover:bg-slate-100 focus-visible:outline-slate-900',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs rounded-md',
        'md' => 'px-4 py-2.5 text-sm rounded-md',
        'lg' => 'px-6 py-3 text-base rounded-lg',
    ];

    $classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}
    @if ($disabled || $loading) disabled @endif x-data="{ loading: @js($loading) }">

    @if ($loading)
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
    @elseif($icon && $iconPosition === 'left')
        {!! $icon !!}
    @endif

    {{ $slot }}

    @if ($icon && $iconPosition === 'right' && !$loading)
        {!! $icon !!}
    @endif
</button>


<!-- ====================================== -->
<!-- 2. INPUT COMPONENT -->
<!-- resources/views/components/input.blade.php -->
<!-- ====================================== -->

@props([
    'type' => 'text',
    'label' => null,
    'error' => null,
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'name' => '',
    'id' => null,
    'placeholder' => '',
])

@php
    $inputId = $id ?? $name;
    $baseClasses =
        'block w-full rounded-md border border-slate-300 text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 sm:text-sm transition-colors disabled:bg-slate-50 disabled:text-slate-500';
    $paddingClass = $icon ? ($iconPosition === 'left' ? 'pl-10 pr-3 py-2.5' : 'pl-3 pr-10 py-2.5') : 'px-3 py-2.5';
    $errorClass = $error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '';
@endphp

<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-slate-700 mb-1.5">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if ($icon && $iconPosition === 'left')
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                {!! $icon !!}
            </div>
        @endif

        <input type="{{ $type }}" name="{{ $name }}" id="{{ $inputId }}"
            placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->except(['class'])->merge([
                'class' => $baseClasses . ' ' . $paddingClass . ' ' . $errorClass,
            ]) }}>

        @if ($icon && $iconPosition === 'right')
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                {!! $icon !!}
            </div>
        @endif
    </div>

    @if ($hint && !$error)
        <p class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif

    @if ($error)
        <p class="mt-1.5 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>


<!-- ====================================== -->
<!-- 3. CARD COMPONENT -->
<!-- resources/views/components/card.blade.php -->
<!-- ====================================== -->

@props([
    'title' => null,
    'subtitle' => null,
    'header' => null,
    'footer' => null,
    'padding' => true,
    'shadow' => true,
])

@php
    $baseClasses = 'bg-white border border-slate-200 rounded-lg';
    $shadowClass = $shadow ? 'shadow-sm' : '';
    $paddingClass = $padding ? 'p-6' : '';
@endphp

<div {{ $attributes->merge(['class' => $baseClasses . ' ' . $shadowClass]) }}>
    @if ($title || $subtitle || $header)
        <div class="px-6 py-4 border-b border-slate-200">
            @if ($header)
                {{ $header }}
            @else
                @if ($title)
                    <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="mt-1 text-sm text-slate-600">{{ $subtitle }}</p>
                @endif
            @endif
        </div>
    @endif

    <div class="{{ $paddingClass }}">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 rounded-b-lg">
            {{ $footer }}
        </div>
    @endif
</div>


<!-- ====================================== -->
<!-- 4. TEXT/HEADING COMPONENTS -->
<!-- resources/views/components/heading.blade.php -->
<!-- ====================================== -->

@props([
    'level' => 'h2', // h1, h2, h3, h4, h5, h6
    'size' => null, // xs, sm, base, lg, xl, 2xl, 3xl, 4xl
])

@php
    $defaultSizes = [
        'h1' => 'text-4xl font-bold',
        'h2' => 'text-3xl font-semibold',
        'h3' => 'text-2xl font-semibold',
        'h4' => 'text-xl font-semibold',
        'h5' => 'text-lg font-medium',
        'h6' => 'text-base font-medium',
    ];

    $customSizes = [
        'xs' => 'text-xs',
        'sm' => 'text-sm',
        'base' => 'text-base',
        'lg' => 'text-lg',
        'xl' => 'text-xl',
        '2xl' => 'text-2xl',
        '3xl' => 'text-3xl',
        '4xl' => 'text-4xl',
    ];

    $sizeClass = $size ? $customSizes[$size] : $defaultSizes[$level];
    $baseClasses = 'text-slate-900 tracking-tight';
@endphp

<{{ $level }} {{ $attributes->merge(['class' => $baseClasses . ' ' . $sizeClass]) }}>
    {{ $slot }}
    </{{ $level }}>





    <!-- ====================================== -->
    <!-- 5. SELECT COMPONENT -->
    <!-- resources/views/components/select.blade.php -->
    <!-- ====================================== -->

    @props([
        'label' => null,
        'error' => null,
        'required' => false,
        'disabled' => false,
        'name' => '',
        'id' => null,
        'placeholder' => 'Sélectionnez une option',
        'options' => [],
    ])

    @php
        $inputId = $id ?? $name;
        $baseClasses =
            'block w-full rounded-md border border-slate-300 text-slate-900 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 sm:text-sm transition-colors disabled:bg-slate-50 disabled:text-slate-500 px-3 py-2.5';
        $errorClass = $error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '';
    @endphp

    <div {{ $attributes->only('class') }}>
        @if ($label)
            <label for="{{ $inputId }}" class="block text-sm font-medium text-slate-700 mb-1.5">
                {{ $label }}
                @if ($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        @endif

        <select name="{{ $name }}" id="{{ $inputId }}" {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->except(['class'])->merge([
                'class' => $baseClasses . ' ' . $errorClass,
            ]) }}>

            @if ($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

            @foreach ($options as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach

            {{ $slot }}
        </select>

        @if ($error)
            <p class="mt-1.5 text-sm text-red-600">{{ $error }}</p>
        @endif
    </div>


    <!-- ====================================== -->
    <!-- 6. TEXTAREA COMPONENT -->
    <!-- resources/views/components/textarea.blade.php -->
    <!-- ====================================== -->

    @props([
        'label' => null,
        'error' => null,
        'hint' => null,
        'required' => false,
        'disabled' => false,
        'name' => '',
        'id' => null,
        'placeholder' => '',
        'rows' => 4,
    ])

    @php
        $inputId = $id ?? $name;
        $baseClasses =
            'block w-full rounded-md border border-slate-300 text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 sm:text-sm transition-colors disabled:bg-slate-50 disabled:text-slate-500 px-3 py-2.5';
        $errorClass = $error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '';
    @endphp

    <div {{ $attributes->only('class') }}>
        @if ($label)
            <label for="{{ $inputId }}" class="block text-sm font-medium text-slate-700 mb-1.5">
                {{ $label }}
                @if ($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        @endif

        <textarea name="{{ $name }}" id="{{ $inputId }}" rows="{{ $rows }}"
            placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->except(['class'])->merge([
                'class' => $baseClasses . ' ' . $errorClass,
            ]) }}>{{ $slot }}</textarea>

        @if ($hint && !$error)
            <p class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
        @endif

        @if ($error)
            <p class="mt-1.5 text-sm text-red-600">{{ $error }}</p>
        @endif
    </div>





    <!-- ====================================== -->
    <!-- 8. BADGE COMPONENT -->
    <!-- resources/views/components/badge.blade.php -->
    <!-- ====================================== -->

    @props([
        'variant' => 'default', // default, success, warning, danger, info
        'size' => 'md', // sm, md, lg
        'dot' => false,
    ])

    @php
        $baseClasses = 'inline-flex items-center font-medium rounded-full';

        $variants = [
            'default' => 'bg-slate-100 text-slate-700',
            'success' => 'bg-green-100 text-green-700',
            'warning' => 'bg-yellow-100 text-yellow-700',
            'danger' => 'bg-red-100 text-red-700',
            'info' => 'bg-blue-100 text-blue-700',
        ];

        $sizes = [
            'sm' => 'px-2 py-0.5 text-xs',
            'md' => 'px-2.5 py-1 text-xs',
            'lg' => 'px-3 py-1.5 text-sm',
        ];

        $dotColors = [
            'default' => 'bg-slate-400',
            'success' => 'bg-green-400',
            'warning' => 'bg-yellow-400',
            'danger' => 'bg-red-400',
            'info' => 'bg-blue-400',
        ];

        $classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size];
    @endphp

    <span {{ $attributes->merge(['class' => $classes]) }}>
        @if ($dot)
            <span class="mr-1.5 h-1.5 w-1.5 rounded-full {{ $dotColors[$variant] }}"></span>
        @endif
        {{ $slot }}
    </span>


    <!-- ====================================== -->
    <!-- 9. ALERT COMPONENT -->
    <!-- resources/views/components/alert.blade.php -->
    <!-- ====================================== -->

    @props([
        'type' => 'info', // info, success, warning, danger
        'title' => null,
        'dismissible' => false,
    ])

    @php
        $types = [
            'info' => [
                'bg' => 'bg-blue-50',
                'border' => 'border-blue-200',
                'text' => 'text-blue-800',
                'icon' =>
                    '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>',
            ],
            'success' => [
                'bg' => 'bg-green-50',
                'border' => 'border-green-200',
                'text' => 'text-green-800',
                'icon' =>
                    '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>',
            ],
            'warning' => [
                'bg' => 'bg-yellow-50',
                'border' => 'border-yellow-200',
                'text' => 'text-yellow-800',
                'icon' =>
                    '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>',
            ],
            'danger' => [
                'bg' => 'bg-red-50',
                'border' => 'border-red-200',
                'text' => 'text-red-800',
                'icon' =>
                    '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>',
            ],
        ];

        $config = $types[$type];
    @endphp

    <div x-data="{ show: true }" x-show="show" x-transition
        {{ $attributes->merge(['class' => 'rounded-lg border p-4 ' . $config['bg'] . ' ' . $config['border']]) }}>
        <div class="flex">
            <div class="flex-shrink-0 {{ $config['text'] }}">
                {!! $config['icon'] !!}
            </div>
            <div class="ml-3 flex-1">
                @if ($title)
                    <h3 class="text-sm font-medium {{ $config['text'] }}">{{ $title }}</h3>
                    <div class="mt-2 text-sm {{ $config['text'] }}">
                        {{ $slot }}
                    </div>
                @else
                    <div class="text-sm {{ $config['text'] }}">
                        {{ $slot }}
                    </div>
                @endif
            </div>
            @if ($dismissible)
                <div class="ml-auto pl-3">
                    <button @click="show = false"
                        class="-mx-1.5 -my-1.5 rounded-lg p-1.5 {{ $config['text'] }} hover:bg-{{ explode('-', $config['bg'])[1] }}-100 focus:ring-2 focus:ring-{{ explode('-', $config['border'])[1] }}-400">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            @endif
        </div>
    </div>


    <!-- ====================================== -->
    <!-- 10. MODAL COMPONENT -->
    <!-- resources/views/components/modal.blade.php -->
    <!-- ====================================== -->

    @props([
        'name' => 'modal',
        'title' => null,
        'size' => 'md', // sm, md, lg, xl
        'show' => false,
    ])

    @php
        $sizes = [
            'sm' => 'max-w-md',
            'md' => 'max-w-lg',
            'lg' => 'max-w-2xl',
            'xl' => 'max-w-4xl',
        ];
    @endphp

    <div x-data="{ show: @js($show) }" x-show="show" x-on:open-modal-{{ $name }}.window="show = true"
        x-on:close-modal-{{ $name }}.window="show = false" x-on:keydown.escape.window="show = false"
        style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" {{ $attributes }}>

        <!-- Overlay -->
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/50 transition-opacity" @click="show = false">
        </div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="show" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative w-full {{ $sizes[$size] }} bg-white rounded-lg shadow-xl">

                @if ($title)
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                        <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
                        <button @click="show = false" class="text-slate-400 hover:text-slate-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endif

                <div class="px-6 py-4">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>

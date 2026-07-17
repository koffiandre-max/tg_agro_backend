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
    'value' => null
])

@php
$inputId = $id ?? $name;
$baseClasses = 'block bg-white w-full rounded-md border border-slate-300 text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 sm:text-sm transition-colors disabled:bg-slate-50 disabled:text-slate-500';
$paddingClass = $icon ? ($iconPosition === 'left' ? 'pl-10 pr-3 py-2.5' : 'pl-3 pr-10 py-2.5') : 'px-3 py-2.5';
$errorClass = $error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '';
@endphp

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-slate-700 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        @if($icon && $iconPosition === 'left')
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                {!! $icon !!}
            </div>
        @endif
        
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $inputId }}"
            placeholder="{{ $placeholder }}"
            value="{{ $value ?? $attributes->get('value') }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->except(['class', 'value'])->merge([
                'class' => $baseClasses . ' ' . $paddingClass . ' ' . $errorClass
            ]) }}
        >
        
        @if($icon && $iconPosition === 'right')
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                {!! $icon !!}
            </div>
        @endif
    </div>
    
    @if($hint && !$error)
        <p class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    
    @if($error)
        <p class="mt-1.5 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>


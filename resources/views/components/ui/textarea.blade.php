@props([
    'label' => null,
    'error' => null,
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'name' => '',
    'id' => null,
    'placeholder' => '',
    'value' => null,
    'rows' => 4
])

@php
$inputId = $id ?? $name;
$baseClasses = 'block bg-white w-full rounded-md border border-slate-300 text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 sm:text-sm transition-colors disabled:bg-slate-50 disabled:text-slate-500';
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
    
    <textarea
        name="{{ $name }}"
        id="{{ $inputId }}"
        placeholder="{{ $placeholder }}"
        rows="{{ $rows }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->except(['class'])->merge([
            'class' => $baseClasses . ' ' . $errorClass . ' px-3 py-2.5'
        ]) }}
    >{{ $value ?? $attributes->get('value') }}</textarea>
    
    @if($hint && !$error)
        <p class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    
    @if($error)
        <p class="mt-1.5 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
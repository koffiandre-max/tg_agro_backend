@props([
    'label' => null,
    'error' => null,
    'hint' => null,
    'required' => false,
    'name' => '',
    'id' => null,
])

@php
$inputId = $id ?? $name;
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

    <input
        type="file"
        name="{{ $name }}"
        id="{{ $inputId }}"
        accept="image/*"
        {{ $required ? 'required' : '' }}
        {{ $attributes->except(['class'])->merge([
            'class' => 'block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800'
        ]) }}
    >

    @if($hint && !$error)
        <p class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif

    @if($error)
        <p class="mt-1.5 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
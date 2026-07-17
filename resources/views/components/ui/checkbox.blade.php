@props([
    'label' => null,
    'error' => null,
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'name' => '',
    'id' => null,
    'checked' => false
])

@php
$inputId = $id ?? $name;
@endphp

<div {{ $attributes->only('class') }}>
    <div class="flex items-center justify-between">
        @if($label)
            <label for="{{ $inputId }}" class="text-sm font-medium text-slate-700">
                {{ $label }}
                @if($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        @endif
        
        <button
            type="button"
            role="switch"
            aria-checked="{{ $checked ? 'true' : 'false' }}"
            @disabled($disabled)
            x-data="{ on: {{ $checked ? 'true' : 'false' }} }"
            x-on:click="if(!{{ $disabled ? 'true' : 'false' }}) { on = !on; $dispatch('input', on) }"
            x-bind:aria-checked="on"
            :class="{
                'bg-slate-900': on,
                'bg-slate-300': !on,
                'opacity-50 cursor-not-allowed': {{ $disabled ? 'true' : 'false' }}
            }"
            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2"
        >
            <span
                aria-hidden="true"
                :class="{
                    'translate-x-5': on,
                    'translate-x-0': !on
                }"
                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out"
            ></span>
        </button>
    </div>
    
    <!-- Hidden input pour les formulaires -->
    <input
        type="hidden"
        name="{{ $name }}"
        x-bind:value="on"
        value="{{ $checked ? '1' : '0' }}"
    >

    @if($hint && !$error)
        <p class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    
    @if($error)
        <p class="mt-1.5 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
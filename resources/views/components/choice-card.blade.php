@props([
    'name',
    'label',
    'value' => '',
    'wireModel' => null,
    'precisionModel' => null,
    'precisionValue' => '',
    'placeholder' => 'Précisez...',
])

@php
    $currentValue = (string) ($value ?? '');
    $showPrecision = $currentValue === '';
    $hasWire = $wireModel && is_string($wireModel);
@endphp

<div class="space-y-2">
    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $label }}</label>
    <div x-data="{ showPrecision: @js($showPrecision) }" class="space-y-2">
        <div class="flex flex-wrap gap-3">
            <label class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 cursor-pointer hover:bg-gray-50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-700 transition-colors">
                <input type="radio" name="{{ $name }}" value="1" class="sr-only" @change="showPrecision = false" {{ $currentValue === '1' ? 'checked' : '' }} @if($hasWire)wire:model="{{ $wireModel }}"@endif>
                <span class="size-2 rounded-full bg-emerald-500"></span>
                Oui
            </label>
            <label class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 cursor-pointer hover:bg-gray-50 has-[:checked]:border-red-600 has-[:checked]:bg-red-50 has-[:checked]:text-red-700 transition-colors">
                <input type="radio" name="{{ $name }}" value="0" class="sr-only" @change="showPrecision = false" {{ $currentValue === '0' ? 'checked' : '' }} @if($hasWire)wire:model="{{ $wireModel }}"@endif>
                <span class="size-2 rounded-full bg-red-500"></span>
                Non
            </label>
            <label class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 cursor-pointer hover:bg-gray-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700 transition-colors">
                <input type="radio" name="{{ $name }}" value="" class="sr-only" @change="showPrecision = true" {{ $currentValue === '' ? 'checked' : '' }} @if($hasWire)wire:model="{{ $wireModel }}"@endif>
                <span class="size-2 rounded-full bg-blue-500"></span>
                À préciser
            </label>
        </div>
        <div x-show="showPrecision" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="overflow-hidden">
            <input
                type="text"
                name="{{ $name }}_precision"
                @if($hasWire && $precisionModel)wire:model="{{ $precisionModel }}"@endif
                value="{{ $precisionValue ?? '' }}"
                placeholder="{{ $placeholder }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
            >
        </div>
    </div>
</div>

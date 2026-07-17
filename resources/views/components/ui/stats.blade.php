@props([
    'count' => 0,
    'label' => 'Total',
    'icon' => null,
    'color' => 'emerald'
])

@php
    $colorClasses = [
        'emerald' => 'bg-emerald-600',
        'blue' => 'bg-blue-600',
        'red' => 'bg-red-600',
        'purple' => 'bg-purple-600',
        'orange' => 'bg-orange-600',
        'green' => 'bg-green-600',
        'indigo' => 'bg-indigo-600',
        'pink' => 'bg-pink-600',
        'yellow' => 'bg-yellow-600'
    ][$color] ?? 'bg-emerald-600';

    $defaultIcon = '<svg class="h-6 w-6 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg overflow-hidden shadow-md']) }}>
    <div class="{{ $colorClasses }} px-4 py-3 flex items-center justify-between">
        {!! $icon ?? $defaultIcon !!}
        <span class="text-3xl font-black text-white">{{ $count }}</span>
    </div>
    <div class="px-4 py-2.5">
        <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">{{ $label }}</p>
    </div>
</div>
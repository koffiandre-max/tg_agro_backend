@props([
    'variant' => 'primary',
    'type' => 'button',
    'block' => false,
    'disabled' => false,
])

@php
$baseClasses = 'inline-flex items-center justify-center px-4 py-2 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed';

$variantClasses = [
    'primary' => 'bg-indigo-600 hover:bg-indigo-700',
    'secondary' => 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50',
    'danger' => 'bg-red-600 hover:bg-red-700',
    'success' => 'bg-green-700 hover:bg-green-800',
    'green' => 'bg-green-700 hover:bg-green-800',
][$variant] ?? $variant;

$classes = $baseClasses . ' ' . $variantClasses;

if ($block) {
    $classes .= ' w-full';
}
@endphp

<button 
    type="{{ $type }}" 
    {{ $attributes->merge(['class' => $classes]) }} 
    {{ $disabled ? 'disabled' : '' }}
>
    {{ $slot }}
</button>
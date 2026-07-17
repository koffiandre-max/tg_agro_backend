@props(['variant' => 'default', 'class' => ''])

@php
$classes = match($variant) {
    'default' => 'rounded-lg border bg-white text-gray-900 shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-100',
    'secondary' => 'rounded-lg border bg-gray-50 text-gray-900 dark:border-gray-800 dark:bg-gray-800 dark:text-gray-100',
    'outline' => 'rounded-lg border-2 border-gray-200 bg-transparent dark:border-gray-700',
    'sidebar' => 'rounded-lg border bg-white shadow-sm sticky top-6 dark:border-gray-800 dark:bg-gray-900',
    default => 'rounded-lg border bg-white text-gray-900 shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-100',
};
@endphp

<div {{ $attributes->merge(['class' => "$classes $class"]) }}>
    {{ $slot }}
</div>
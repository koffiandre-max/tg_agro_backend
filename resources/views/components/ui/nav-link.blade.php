@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-3 text-sm font-black text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border-l-4 border-indigo-600 transition-all shadow-sm'
            : 'flex items-center px-4 py-3 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-indigo-600 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl border-l-4 border-transparent transition-all';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <i class="{{ $icon }} mr-3 w-5 text-center {{ $active ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}"></i>
    @endif
    {{ $slot }}
</a>
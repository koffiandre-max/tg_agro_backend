@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'px-6 py-4 bg-slate-50 border-t border-slate-200 rounded-b-lg dark:bg-gray-800 dark:border-gray-700 ' . $class]) }}>
    {{ $slot }}
</div>
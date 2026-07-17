@props([
    'title'       => '',
    'value'       => 0,
    'icon'        => 'heroicon-o-chart-bar',
    'color'       => 'blue',
    'trend'       => null,       // 'up' | 'down'
    'trendValue'  => null,
    'description' => null,
    'href'        => null,       // renders as <a> when provided
])

@php
    $colors = [
        'blue'    => ['bg' => 'bg-blue-100 dark:bg-blue-900/50',     'text' => 'text-blue-600 dark:text-blue-400',     'ring' => 'ring-blue-500'],
        'green'   => ['bg' => 'bg-green-100 dark:bg-green-900/50',   'text' => 'text-green-600 dark:text-green-400',   'ring' => 'ring-green-500'],
        'red'     => ['bg' => 'bg-red-100 dark:bg-red-900/50',       'text' => 'text-red-600 dark:text-red-400',       'ring' => 'ring-red-500'],
        'yellow'  => ['bg' => 'bg-yellow-100 dark:bg-yellow-900/50', 'text' => 'text-yellow-600 dark:text-yellow-400', 'ring' => 'ring-yellow-500'],
        'orange'  => ['bg' => 'bg-orange-100 dark:bg-orange-900/50', 'text' => 'text-orange-600 dark:text-orange-400', 'ring' => 'ring-orange-500'],
        'purple'  => ['bg' => 'bg-purple-100 dark:bg-purple-900/50', 'text' => 'text-purple-600 dark:text-purple-400', 'ring' => 'ring-purple-500'],
        'pink'    => ['bg' => 'bg-pink-100 dark:bg-pink-900/50',     'text' => 'text-pink-600 dark:text-pink-400',     'ring' => 'ring-pink-500'],
        'indigo'  => ['bg' => 'bg-indigo-100 dark:bg-indigo-900/50', 'text' => 'text-indigo-600 dark:text-indigo-400', 'ring' => 'ring-indigo-500'],
        'emerald' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/50','text' => 'text-emerald-600 dark:text-emerald-400','ring' => 'ring-emerald-500'],
    ];

    $colorScheme = $colors[$color] ?? $colors['blue'];

    $icons = [
        'heroicon-o-chart-bar'       => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'heroicon-o-table'           => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z',
        'heroicon-o-users'           => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 5.197a4 4 0 00-4-4m4 4a4 4 0 010 4',
        'heroicon-o-user-group'      => 'M17 20h4v-2a3 3 0 00-5.356-1.857M23 20v-2a3 3 0 00-2.184-2.883M5.05 18.12A3 3 0 013 20v-2M3.754 6.975C5.473 5.685 7.65 5 10 5c4.97 0 9 3.186 9 7.317 0 .666-.105 1.312-.298 1.92M16 11a4 4 0 11-8 0 4 4 0 018 0z',
        'heroicon-o-map'             => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7',
        'heroicon-o-bell'            => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
        'heroicon-o-megaphone'       => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.002 4.002 0 0117 14.882V13a1 1 0 00-1-1H5.436a1 1 0 00-1 1v.683z',
        'heroicon-o-currency-dollar' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'heroicon-o-shopping-cart'   => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        'heroicon-o-document-text'   => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'heroicon-o-clock'           => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'heroicon-o-cash'            => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
        'heroicon-o-check-circle'    => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    ];

    $iconPath  = $icons[$icon] ?? $icons['heroicon-o-chart-bar'];
    $baseClass = 'bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg';
    $hoverClass = $href ? ' hover:shadow-md transition-shadow' : '';
@endphp

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClass . $hoverClass]) }}>
@else
<div {{ $attributes->merge(['class' => $baseClass]) }}>
@endif

    <div class="p-5">
        <div class="flex items-center">
            <div class="shrink-0">
                <div class="h-12 w-12 rounded-lg {{ $colorScheme['bg'] }} flex items-center justify-center ring-3 ring-opacity-50 {{ $colorScheme['ring'] }}">
                    <svg class="h-6 w-6 {{ $colorScheme['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}" />
                    </svg>
                </div>
            </div> 

            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                        {{ $title }}
                    </dt>
                    <dd class="flex items-baseline">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white font-mono">
                            {{ $value }}
                        </div>

                        @if($trend)
                            <div class="ml-2 flex items-baseline text-sm font-semibold {{ $trend === 'up' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                @if($trend === 'up')
                                    <svg class="self-center shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="self-center shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                                <span class="sr-only">{{ $trend === 'up' ? 'Augmenté de' : 'Diminué de' }}</span>
                                {{ $trendValue }}
                            </div>
                        @endif
                    </dd>

                    @if($description)
                        <dt class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400 truncate">
                            {{ $description }}
                        </dt>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    @if(isset($footer))
        <div class="bg-gray-50 dark:bg-gray-700/50 px-5 py-3">
            {{ $footer }}
        </div>
    @endif

@if($href)
</a>
@else
</div>
@endif

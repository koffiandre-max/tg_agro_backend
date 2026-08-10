{{-- Sidebar latérale avec couleur selon le rôle --}}
@php
$navItems = collect($navigation);
$userRole = auth()->user()->role ?? 'client';

// Couleurs profondes et modernes adaptées au design flottant
$sidebarBg = match($userRole) {
    'admin' => 'yellow-600',
    'client' => 'emerald-600', 
    'technician' => 'emerald-600', 
    default => 'emerald-600',
};

$sidebarBorder = match($userRole) {
    'admin' => 'border-yellow-800',
    'client' => 'border-emerald-600', 
    'technician' => 'border-emerald-900/50', 
    default => 'border-emerald-600',
};

// Classes de survol et actif fixes basées sur du blanc/transparent (visible sur tous les fonds)
$sidebarHoverBg = 'hover:bg-slate-950 hover:text-white';
$sidebarActiveBg = 'bg-slate-950 text-white font-semibold shadow-sm border-l-2 border-white';
$textColor = 'text-white';
@endphp

{{-- Overlay mobile --}}
<div x-show="sidebarOpen" 
     x-cloak
     @click="sidebarOpen = false"
     class="fixed inset-0 z-10 bg-black/40 backdrop-blur-sm lg:hidden"
     x-transition.opacity
     aria-hidden="true"></div>

<aside id="sidebar-lateral"
       class="fixed z-20 left-0 top-0 bottom-0 h-screen flex flex-col shadow-lg transition-all duration-300 ease-in-out overflow-hidden bg-{{ $sidebarBg }}"
       :class="{
           'w-20': sidebarCollapsed && isDesktop,
           'w-64': !sidebarCollapsed && isDesktop,
           'translate-x-0': sidebarOpen && !isDesktop,
           '-translate-x-full': !sidebarOpen && !isDesktop
       }">

    {{-- Header / Logo --}}
    <div class="h-16 flex items-center px-4 bg-{{ $sidebarBg }} border-b border-white/5 shrink-0">
        <div class="flex items-center gap-3 overflow-hidden w-full">
            {{-- Icône de Logo moderne & minimaliste --}}
            <div class="h-9 w-9 bg-white/10 rounded-xl flex items-center justify-center shrink-0 border border-white/10">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m11.314 11.314l.707.707M12 5a7 7 0 100 14 7 7 0 000-14z" />
                </svg>
            </div>
            <span class="font-bold text-sm tracking-wider text-white transition-opacity duration-300 whitespace-nowrap"
                  :class="{ 'opacity-0 absolute pointer-events-none': sidebarCollapsed }">
                TG’INVEST <span class="font-normal text-white/60">CONSULTING</span>
            </span>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4">
        <ul class="space-y-1.5">
            @foreach ($navigation as $item)
                @if (($item['type'] ?? null) !== 'separator' && ($item['can'] ?? false))
                    @if(!isset($item['children']))
                        {{-- Menu simple --}}
                        <li>
                            <a href="{{ $item['route'] ? route($item['route']) : '#' }}"
                               class="group flex items-center gap-3 px-3 py-2.5 rounded text-sm transition-all duration-150 {{ $textColor }} {{ $sidebarHoverBg }} {{ isset($item['route']) && request()->routeIs($item['route']) ? $sidebarActiveBg : '' }}">
                                <svg class="h-4 w-4 shrink-0 opacity-70 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    {!! $item['icon'] !!}
                                </svg>
                                <span class="transition-opacity duration-300" :class="{ 'opacity-0 absolute pointer-events-none': sidebarCollapsed }">
                                    {{ $item['name'] }}
                                </span>
                            </a>
                        </li>
                    @else
                        {{-- Menu déroulant avec Alpine --}}
                        @php
                            $currentRouteName = request()->route() ? request()->route()->getName() : '';
                            $isChildActive = in_array($currentRouteName, array_column($item['children'], 'route') ?? []);
                        @endphp
                        <li x-data="{ open: {{ $isChildActive ? 'true' : 'false' }} }" class="flex flex-col">
                            <button @click="open = !open; if (window.innerWidth < 1024 && sidebarCollapsed) sidebarCollapsed = false"
                                    class="group flex items-center justify-between gap-2 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 w-full {{ $textColor }} {{ $sidebarHoverBg }}"
                                    :class="open ? 'bg-white/15 text-white font-semibold' : ''">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="h-4 w-4 flex items-center justify-center shrink-0 opacity-70 group-hover:opacity-100 transition-opacity">
                                        {!! $item['icon'] !!}
                                    </div>
                                    <span class="transition-opacity duration-300 truncate" :class="{ 'opacity-0 absolute pointer-events-none': sidebarCollapsed }">
                                        {{ $item['name'] }}
                                    </span>
                                </div>
                                <svg class="h-3.5 w-3.5 shrink-0 opacity-60 transition-transform duration-300"
                                     :class="{ 'rotate-180': open, 'opacity-0 absolute pointer-events-none': sidebarCollapsed }"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            {{-- Sous-menu --}}
                            <div x-show="open" 
                                 x-collapse
                                 class="mt-1 ml-5 pl-2.5 border-l border-white/10 space-y-1 overflow-hidden"
                                 :class="{ 'hidden': sidebarCollapsed }">
                                @foreach ($item['children'] as $child)
                                    @if (isset($child['can']) && $child['can'])
                                        <a href="{{ $child['route'] ? route($child['route']) : '#' }}"
                                           class="group flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-150 {{ isset($child['route']) && request()->routeIs($child['route']) ? 'bg-white/15 text-white font-semibold' : 'text-white/60 hover:bg-white/10 hover:text-white' }}"
                                           @click="if (window.innerWidth < 1024) closeSidebarOnMobile()">
                                            <span class="h-1.5 w-1.5 rounded-full shrink-0 transition-all duration-150 {{ isset($child['route']) && request()->routeIs($child['route']) ? 'bg-white scale-125' : 'bg-white/20 group-hover:bg-white/50' }}"></span>
                                            <span class="truncate">{{ $child['name'] }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </li>
                    @endif
                @endif
            @endforeach
        </ul>
    </nav>

    {{-- Profil de l'utilisateur (Bas de page) --}}
    {{-- <div class="p-3 border-t border-white/5 shrink-0 bg-{{ $sidebarBg }}">
        <div class="flex items-center gap-3 overflow-hidden rounded-xl p-1">
            <div class="h-10 w-10 bg-white/10 rounded-xl flex items-center justify-center text-white font-bold shrink-0 border border-white/10">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="transition-opacity duration-300 min-w-0"
                 :class="{ 'opacity-0 absolute pointer-events-none': sidebarCollapsed }">
                <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? 'Utilisateur' }}</p>
                <p class="text-[11px] text-white/50 capitalize font-medium tracking-wide flex items-center gap-1">
                    <span class="h-1 w-1 rounded-full bg-white/40"></span>
                    {{ auth()->user()->role ?? 'user' }}
                </p>
            </div>
        </div>
    </div> --}}
</aside>

<style>
    [x-cloak] { display: none !important; }
</style>

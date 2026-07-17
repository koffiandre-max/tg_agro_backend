{{-- Sidebar minimaliste flottante — Style LinkedIn --}}
@php
    $navigation = \App\Support\Navigation::filtered();
    $user = auth()->user();
@endphp

{{-- Overlay mobile : ferme la sidebar au clic à l'extérieur --}}
<div id="sidebarOverlay"
     onclick="toggleSidebar()"
     class="fixed inset-0 z-10 bg-black/30 opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden"
     aria-hidden="true"></div>

<aside id="sidebar"
        class="fixed z-20 flex flex-col transition-all duration-300 ease-in-out overflow-hidden -translate-x-[280px] lg:translate-x-0"
        style="left:12px;top:70px;bottom:12px;width:240px;background:var(--sidebar-bg,oklch(57% 0.135 163.225));border-radius:14px;border:1px solid rgba(0,0,0,0.07);box-shadow:0 4px 24px rgba(0,0,0,0.06);"
        role="navigation" aria-label="Navigation principale">

    {{-- Logo / Brand --}}
    <div id="sidebarHeader" class="px-4 py-4 li-border-b">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shrink-0">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="nav-label flex-1 min-w-0">
                <p class="text-sm font-bold truncate li-text">{{ config('app.name', 'TG Invest') }}</p>
                <p class="text-[10px] truncate li-muted">Gestion Agricole</p>
            </div>
            {{-- Bouton collapse desktop : reste visible et se recentre en mode réduit --}}
            <button type="button" id="collapseToggleBtn" onclick="toggleDesktopSidebar()" title="Réduire la sidebar"
                    class="hidden lg:flex h-7 w-7 shrink-0 items-center justify-center rounded-md li-btn-muted">
                <svg id="li-collapse-icon" class="h-4 w-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-3 li-no-scroll">
        @foreach ($navigation as $item)
            @if ($item['can'] ?? false)
                @php
                    $hasChildren = isset($item['children']) && !empty($item['children']);

                    if ($hasChildren && isset($item['segments'])) {
                        $isActive = collect($item['segments'])->contains(fn($s) => request()->segment(1) === $s || request()->segment(2) === $s);
                    } elseif (isset($item['route'])) {
                        $isActive = request()->routeIs($item['route']);
                    } else {
                        $isActive = false;
                    }
                @endphp

                @if (!$hasChildren)
                    {{-- Item simple --}}
                    <a href="{{ route($item['route']) }}"
                       title="{{ $item['name'] }}"
                       @if($isActive) aria-current="page" @endif
                       class="li-item flex items-center gap-2.5 px-3 py-2 rounded-lg mb-1 {{ $isActive ? 'li-active' : '' }}">
                        <svg class="h-4 w-4 shrink-0 li-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            {!! $item['icon'] !!}
                        </svg>
                        <span class="nav-label flex-1 text-sm li-label">{{ $item['name'] }}</span>
                    </a>
                @else
                    {{-- Item avec sous-menu --}}
                    <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }" class="mb-1">
                        <button type="button" @click="open = !open" :aria-expanded="open.toString()"
                                title="{{ $item['name'] }}"
                                class="li-item w-full flex items-center gap-2.5 px-3 py-2 rounded-lg {{ $isActive ? 'li-active' : '' }}">
                            <svg class="h-4 w-4 shrink-0 li-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                {!! $item['icon'] !!}
                            </svg>
                            <span class="nav-label flex-1 text-left text-sm li-label">{{ $item['name'] }}</span>
                            <svg class="nav-label h-3 w-3 shrink-0 li-icon transition-transform duration-200"
                                 :class="open ? 'rotate-180' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="nav-children-wrap pl-8 mt-0.5 space-y-0.5">
                            @foreach ($item['children'] as $child)
                                @if ($child['can'] ?? false)
                                    @php $childActive = request()->routeIs($child['route']); @endphp
                                    <a href="{{ route($child['route']) }}"
                                       @if($childActive) aria-current="page" @endif
                                       class="li-child flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs {{ $childActive ? 'li-child-active' : '' }}">
                                        <span class="w-1 h-1 rounded-full shrink-0 {{ $childActive ? 'li-dot-active' : 'li-dot' }}"></span>
                                        <span class="truncate">{{ $child['name'] }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        @endforeach
    </nav>

    {{-- Profil utilisateur --}}
    <div class="px-3 py-3 li-border-t">
        <div class="nav-label-group flex items-center gap-2.5 px-2 py-2 rounded-lg li-profile-hover">
            <div class="h-7 w-7 rounded-full flex items-center justify-center shrink-0 text-[10px] font-bold text-white"
                 style="background:linear-gradient(135deg,#6366f1,#4f46e5)">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0 nav-label">
                <p class="text-xs font-medium truncate leading-none li-text">{{ $user->name ?? 'Utilisateur' }}</p>
                <p class="text-[10px] truncate mt-0.5 li-muted">{{ ucfirst($user->role ?? 'user') }}</p>
            </div>
            <button type="button" onclick="document.getElementById('logoutForm').submit()" title="Déconnexion"
                    class="h-6 w-6 flex items-center justify-center rounded-md transition-colors nav-label li-btn-muted li-btn-logout">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </div>
        <form id="logoutForm" action="{{ url('/logout') }}" method="POST" class="hidden">@csrf</form>
    </div>
</aside>

<style>
    [x-cloak] { display: none !important; }
    body:not(.sidebar-collapsed) .sidebar-flyout { display: none !important; }

    /* ── Couleurs adaptatives ────────────────────────────────────
       Le fond par défaut est vert (agri) : le texte/muted/hover/actif
       par défaut sont donc calés sur un fond sombre pour rester
       lisibles sans qu'un consommateur du composant ait à tout
       redéfinir. Un accent doré ("invest") marque l'état actif. ── */
    .li-text    { color: var(--sidebar-text,  oklch(98% 0.01 160)); }
    .li-muted   { color: var(--sidebar-muted, rgba(255,255,255,.65)); }
    .li-icon    { color: var(--sidebar-muted, rgba(255,255,255,.65)); }
    .li-label   { color: var(--sidebar-text,  oklch(98% 0.01 160)); }
    .li-border-b { border-bottom: 1px solid rgba(255,255,255,0.12); }
    .li-border-t { border-top:    1px solid rgba(255,255,255,0.12); }

    /* ── Items ───────────────────────────────────────────────── */
    .li-item { transition: background .12s, color .12s; cursor: pointer; }
    .li-item:hover { background: var(--sidebar-hover, rgba(255,255,255,0.10)); }
    .li-item:focus-visible { outline: 2px solid var(--sidebar-accent, #f5b942); outline-offset: 2px; }

    .li-active { background: var(--sidebar-active-bg, rgba(255,255,255,0.16)) !important; }
    .li-active .li-icon,
    .li-active .li-label { color: var(--sidebar-accent, #f5b942) !important; font-weight: 600; }

    /* ── Sous-items ──────────────────────────────────────────── */
    .li-child       { color: var(--sidebar-muted, rgba(255,255,255,.65)); transition: background .12s, color .12s; }
    .li-child:hover { background: var(--sidebar-hover, rgba(255,255,255,0.10)); color: var(--sidebar-text, oklch(98% 0.01 160)); }
    .li-child-active { color: var(--sidebar-accent, #f5b942) !important; font-weight: 600; background: rgba(255,255,255,0.08); }
    .li-dot         { background: var(--sidebar-muted, rgba(255,255,255,.5)); }
    .li-dot-active  { background: var(--sidebar-accent, #f5b942); }

    /* ── Boutons ─────────────────────────────────────────────── */
    .li-btn-muted { color: var(--sidebar-muted, rgba(255,255,255,.65)); }
    .li-btn-muted:hover { background: var(--sidebar-hover, rgba(255,255,255,0.10)); color: var(--sidebar-text, oklch(98% 0.01 160)); }
    .li-btn-logout:hover { color: #fca5a5 !important; background: rgba(239,68,68,0.18) !important; }

    /* ── Profil hover ────────────────────────────────────────── */
    .li-profile-hover:hover { background: var(--sidebar-hover, rgba(255,255,255,0.10)); }

    /* ── Scrollbar ───────────────────────────────────────────── */
    .li-no-scroll { scrollbar-width: none; }
    .li-no-scroll::-webkit-scrollbar { display: none; }

    /* ── Overlay mobile actif ────────────────────────────────── */
    body.sidebar-open #sidebarOverlay { opacity: 1; pointer-events: auto; }
    body.sidebar-open #sidebar { transform: translateX(0) !important; }

    /* ── Décalage contenu + mode réduit (desktop uniquement) ──── */
    @media (min-width: 1024px) {
        #main-content { margin-left: 16.5rem !important; }
        footer        { margin-left: 16.5rem !important; }
        #nav-left     { width: 16.5rem !important; }

        body.sidebar-collapsed #sidebar           { width: 3.5rem !important; top: 56px !important; }
        body.sidebar-collapsed #main-content      { margin-left: 5.5rem !important; }
        body.sidebar-collapsed footer              { margin-left: 5.5rem !important; }
        body.sidebar-collapsed #nav-left           { width: 5.5rem !important; padding-left:.75rem; padding-right:.5rem; justify-content:flex-start; }
        body.sidebar-collapsed .nav-label          { display: none !important; }
        body.sidebar-collapsed .nav-label-group    { background: transparent !important; padding: .25rem; }
        body.sidebar-collapsed .nav-children-wrap  { display: none !important; }
        body.sidebar-collapsed .nav-separator      { display: none !important; }
        body.sidebar-collapsed #sidebar nav a,
        body.sidebar-collapsed #sidebar nav > div > button { padding-left: .625rem; padding-right: .5rem; }
        body.sidebar-collapsed .li-item            { padding-left: .625rem; padding-right: .5rem; }
        body.sidebar-collapsed #li-collapse-icon   { transform: rotate(180deg); }
    }
</style>

<script>
    // Sidebar mobile : ouverture/fermeture + overlay + fermeture au clavier (Échap)
    function toggleSidebar() {
        document.body.classList.toggle('sidebar-open');
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') document.body.classList.remove('sidebar-open');
    });

    // Sidebar desktop : réduite / étendue, mémorisé en localStorage
    function toggleDesktopSidebar() {
        const collapsed = document.body.classList.toggle('sidebar-collapsed');
        localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
    }

    (function () {
        if (localStorage.getItem('sidebarCollapsed') === '1') {
            document.body.classList.add('sidebar-collapsed');
        }
    })();
</script>

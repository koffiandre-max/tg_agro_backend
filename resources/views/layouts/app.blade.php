<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{
    sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
    sidebarOpen: false,
    toggleSidebar() {
        if (window.innerWidth >= 1024) {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        } else {
            this.sidebarOpen = !this.sidebarOpen;
        }
    },
    closeSidebarOnMobile() {
        if (window.innerWidth < 1024) {
            this.sidebarOpen = false;
        }
    }
}" @click.outside="closeSidebarOnMobile">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TG Invest') }} - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css'])
    <script src="/jQuery/jquery-3.4.1.min.js"></script>
    <link href="{{ asset('css/tokens.css') }}" rel="stylesheet">
    <script src="/alpine/alpine.js" defer></script>
    <script src="/alpine/collapse.js" defer></script>
    <script src="{{ asset('tailwind/tailwind.js') }}"></script>
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-gray-100">
    @php
    $userRole = auth()->user()->role ?? 'client';
    
    // Couleurs du header selon le rôle
    $headerBgColors = [
        'admin' => 'bg-gray-900',
        'client' => 'bg-emerald-600',
        'technician' => 'bg-blue-900',
    ];
    $headerBorderColors = [
        'admin' => 'border-gray-800',
        'client' => 'border-emerald-700',
        'technician' => 'border-blue-800',
    ];
    $headerTextColors = [
        'admin' => 'text-white',
        'client' => 'text-emerald-100',
        'technician' => 'text-blue-100',
    ];
    $headerHoverColors = [
        'admin' => 'hover:bg-gray-800',
        'client' => 'hover:bg-emerald-700',
        'technician' => 'hover:bg-blue-800',
    ];
    
    $headerBg = $headerBgColors[$userRole] ?? $headerBgColors['client'];
    $headerBorder = $headerBorderColors[$userRole] ?? $headerBorderColors['client'];
    $headerText = $headerTextColors[$userRole] ?? $headerTextColors['client'];
    $headerHover = $headerHoverColors[$userRole] ?? $headerHoverColors['client'];
    
    // Couleurs pour le header mobile
    $mobileHeaderBgColors = [
        'admin' => 'bg-gray-900',
        'client' => 'bg-emerald-600',
        'technician' => 'bg-blue-700',
    ];
    $mobileHeaderTextColors = [
        'admin' => 'text-white',
        'client' => 'text-white',
        'technician' => 'text-white',
    ];
    $mobileHeaderBorderColors = [
        'admin' => 'border-gray-800',
        'client' => 'border-emerald-700',
        'technician' => 'border-blue-800',
    ];
    
    $mobileHeaderBg = $mobileHeaderBgColors[$userRole] ?? $mobileHeaderBgColors['client'];
    $mobileHeaderText = $mobileHeaderTextColors[$userRole] ?? $mobileHeaderTextColors['client'];
    $mobileHeaderBorder = $mobileHeaderBorderColors[$userRole] ?? $mobileHeaderBorderColors['client'];
    @endphp
    
    {{-- Notification Container --}}
    <div id="notification-container" class="fixed top-4 right-4 z-50 flex flex-col gap-3 max-w-sm w-full sm:w-auto" aria-live="polite"></div>

    {{-- Sidebar Navigation --}}
    @include('layouts.sidebar-emerald')

    {{-- Header Desktop (CORRIGÉ : Classes 'left-64' et 'left-20' rendues dynamiques) --}}
   <header class="hidden lg:flex fixed top-0 right-0 h-16 {{ $headerBg }} border-b {{ $headerBorder }} z-20 items-center justify-between px-6 transition-all duration-300 ease-in-out"
        :class="{
            'left-64': !sidebarCollapsed && window.innerWidth >= 1024,
            'left-20': sidebarCollapsed && window.innerWidth >= 1024,
            'left-0': window.innerWidth < 1024
        }">
        <div class="flex items-center gap-2">
            <h1 class="text-lg font-semibold {{ $headerText }}">@yield('page-title', 'Dashboard')</h1>
        </div>

        <div class="flex items-center gap-4">
            {{-- Messages --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="relative h-10 w-10 flex items-center justify-center rounded-lg {{ $headerText }} {{ $headerHover }} transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 h-2.5 w-2.5 bg-red-400 rounded-full border-2 {{ $headerBorder }}"></span>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                     style="display: none;">
                    <div class="p-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-800">Messages</p>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <a href="{{ route('admin.portail.messages') }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50">
                            <p class="text-sm font-medium text-gray-800">Nouveau message</p>
                            <p class="text-xs text-gray-500 mt-1">Vous avez reçu un nouveau message</p>
                            <p class="text-xs text-gray-400 mt-1">Il y a 5 minutes</p>
                        </a>
                    </div>
                    <div class="p-2">
                        <a href="{{ route('admin.portail.messages') }}" class="block text-center text-sm {{ $headerText }} hover:text-white py-1.5">
                            Voir tous les messages
                        </a>
                    </div>
                </div>
            </div>

            {{-- Notifications --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="relative h-10 w-10 flex items-center justify-center rounded-lg {{ $headerText }} {{ $headerHover }} transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.484.172 2.25 2.25 0 012.25 2.25c0 .85-.55 1.6-1.324 1.876a12.05 12.05 0 01-8.125 0 1.875 1.875 0 01-.513-.172A2.25 2.25 0 013 18.25c0-.904.575-1.716 1.39-2.014a23.865 23.865 0 005.484-.172M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 h-2.5 w-2.5 bg-red-400 rounded-full border-2 {{ $headerBorder }}"></span>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                     style="display: none;">
                    <div class="p-3 border-b border-gray-100 flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-800">Notifications</p>
                        <span class="text-xs text-gray-500">3 non lues</span>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-50">
                            <p class="text-sm font-medium text-gray-800">Nouveau rapport validé</p>
                            <p class="text-xs text-gray-500 mt-1">Rapport de la ferme #123 validé</p>
                            <p class="text-xs text-gray-400 mt-1">Il y a 10 minutes</p>
                        </div>
                        <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-50 bg-white/5">
                            <p class="text-sm font-medium text-gray-800">Nouvelle mission assignée</p>
                            <p class="text-xs text-gray-500 mt-1">Mission #456 assignée à Jean Kouassi</p>
                            <p class="text-xs text-gray-400 mt-1">Il y a 1 heure</p>
                        </div>
                        <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer">
                            <p class="text-sm font-medium text-gray-800">Photo approuvée</p>
                            <p class="text-xs text-gray-500 mt-1">Photo de la ferme #789 approuvée</p>
                            <p class="text-xs text-gray-400 mt-1">Il y a 2 heures</p>
                        </div>
                    </div>
                    <div class="p-2">
                        <button class="w-full text-center text-sm {{ $headerText }} hover:text-white py-1.5">
                            Tout marquer comme lu
                        </button>
                    </div>
                </div>
            </div>

            {{-- Avatar utilisateur --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ $headerHover }} transition-colors">
                    <div class="h-9 w-9 rounded-full bg-white flex items-center justify-center {{ $headerText }} font-semibold text-sm">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="hidden xl:block text-left">
                        <p class="text-sm font-medium text-white">{{ auth()->user()->name ?? 'Utilisateur' }}</p>
                        <p class="text-xs {{ $headerText }}/70">{{ ucfirst(auth()->user()->role ?? 'user') }}</p>
                    </div>
                    <svg class="h-4 w-4 {{ $headerText }}/80" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                     style="display: none;">
                    <div class="p-2">
                        <a href="#" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Mon Profil
                        </a>
                        <div class="border-t border-gray-200 my-1"></div>
                        <form method="POST" action="{{ url('/logout') }}" id="header-logout-form" class="hidden">@csrf</form>
                        <button type="button" onclick="document.getElementById('header-logout-form').submit()"
                                class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l-4-4m0 0l4-4m-4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Déconnexion
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Header Mobile --}}
    <header class="lg:hidden fixed top-0 left-0 right-0 h-16 {{ $mobileHeaderBg }} border-b {{ $mobileHeaderBorder }} z-30 flex items-center justify-between px-4">
        <div class="flex items-center gap-3">
            <button @click="toggleSidebar()" class="h-10 w-10 flex items-center justify-center rounded-md {{ $mobileHeaderText }}/70 hover:bg-white/10 transition-colors">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="font-semibold {{ $mobileHeaderText }}">{{ config('app.name', 'TG Invest') }}</span>
        </div>
    </header>

    {{-- Main Content --}}
    <main id="main-content" 
          class="min-h-screen transition-all duration-300 ease-in-out pt-20"
          :class="{
              'ml-64': !sidebarCollapsed && window.innerWidth >= 1024,
              'ml-20': sidebarCollapsed && window.innerWidth >= 1024,
              'ml-0': window.innerWidth < 1024
          }">
        @yield('content')
    </main>

    {{-- Footer (CORRIGÉ : Marges de décalage rendues dynamiques) --}}
    <footer class="transition-all duration-300 ease-in-out p-4 text-center text-xs text-gray-500"
            :class="{
                'lg:ml-64': !sidebarCollapsed && window.innerWidth >= 1024,
                'lg:ml-20': sidebarCollapsed && window.innerWidth >= 1024,
                'ml-0': window.innerWidth < 1024
            }">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'TG Invest') }}. Tous droits réservés.</p>
    </footer>

    {{-- Notification Manager --}}
    <script>
        const NotificationManager = {
            show: function(message, type = 'success', duration = 5000) {
                const container = document.getElementById('notification-container');
                const id = 'notification-' + Date.now();

                const icons = {
                    success: `<svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>`,
                    error: `<svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>`,
                    warning: `<svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>`,
                    info: `<svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>`
                };

                const colors = {
                    success: 'bg-white border-l-4 border-green-500',
                    error: 'bg-white border-l-4 border-red-500',
                    warning: 'bg-white border-l-4 border-yellow-500',
                    info: 'bg-white border-l-4 border-blue-500'
                };

                const notification = document.createElement('div');
                notification.id = id;
                notification.className = `${colors[type]} shadow-lg rounded-lg p-4 notification-enter`;
                notification.setAttribute('role', 'alert');
                notification.innerHTML = `
                    <div class="flex items-start">
                        <div class="flex-shrink-0">${icons[type]}</div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">${message}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button onclick="NotificationManager.close('${id}')" class="inline-flex text-gray-400 hover:text-gray-500 transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                `;

                container.appendChild(notification);

                if (duration > 0) {
                    setTimeout(() => this.close(id), duration);
                }
            },

            close: function(id) {
                const notification = document.getElementById(id);
                if (notification) {
                    notification.classList.remove('notification-enter');
                    notification.classList.add('notification-exit');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }
            }
        };

        window.NotificationManager = NotificationManager;

        // Afficher les messages flash Laravel
        @if (session('success'))
            NotificationManager.show("{{ session('success') }}", 'success');
        @endif

        @if (session('error'))
            NotificationManager.show("{{ session('error') }}", 'error');
        @endif

        @if (session('warning'))
            NotificationManager.show("{{ session('warning') }}", 'warning');
        @endif

        @if (session('info'))
            NotificationManager.show("{{ session('info') }}", 'info');
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                NotificationManager.show("{{ $error }}", 'error');
            @endforeach
        @endif

        // Écouteur d'événements Livewire pour les notifications
        window.addEventListener('notify', function(event) {
            const { type, message } = event.detail;
            if (message) {
                NotificationManager.show(message, type || 'success');
            }
        });
    </script>

    <style>
        .notification-enter {
            animation: slideInRight 0.3s ease-out forwards;
        }
        .notification-exit {
            animation: fadeOut 0.3s ease-in forwards;
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(100%); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to   { opacity: 0; }
        }
    </style>

    @livewireScripts
    <script src="/alpine/collapse.js" defer></script>
    @stack('scripts')
</body>
</html>
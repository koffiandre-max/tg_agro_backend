<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{
    sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
    sidebarOpen: false,
    windowWidth: window.innerWidth,
    get isDesktop() {
        return this.windowWidth >= 1024;
    },
    init() {
        window.addEventListener('resize', () => {
            this.windowWidth = window.innerWidth;
        });
    },
    toggleSidebar() {
        if (this.isDesktop) {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        } else {
            this.sidebarOpen = !this.sidebarOpen;
        }
    },
    closeSidebarOnMobile() {
        if (!this.isDesktop) {
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
    @fonts
    <script src="/jQuery/jquery-3.4.1.min.js"></script>
    <link href="{{ asset('css/tokens.css') }}" rel="stylesheet">
    
    <script>
        // Enregistrer le composant chat AVANT le chargement d'Alpine.js
        // pour éviter les conflits de quotes dans l'attribut HTML x-data
        document.addEventListener('alpine:init', () => {
            Alpine.data('chatComponent', () => ({
                chatOpen: false,
                currentMessage: '',
                messages: [],
                loading: false,
                sending: false,
                pollingInterval: null,
                init() {
                    this.initPolling();
                },
                async loadMessages() {
                    this.loading = true;
                    try {
                        const response = await fetch('/chat/messages', { credentials: 'same-origin' });
                        const data = await response.json();
                        if (Array.isArray(data) && data.length > 0) {
                            this.messages = data.map(item => ({
                                id: item.id,
                                text: item.message,
                                sentBy: item.is_mine ? 'user' : 'support',
                                time: item.time,
                            }));
                        } else {
                            this.messages = [{ text: 'Bonjour ! Comment pouvons-nous vous accompagner aujourd\'hui ?', sentBy: 'support', time: 'À l\'instant' }];
                        }
                        this.$nextTick(() => this.scrollToBottom());
                    } catch (e) {
                        this.messages = [{ text: 'Bonjour ! Comment pouvons-nous vous accompagner aujourd\'hui ?', sentBy: 'support', time: 'À l\'instant' }];
                    } finally {
                        this.loading = false;
                    }
                },
                scrollToBottom() {
                    const container = document.getElementById('chat-messages-container');
                    if (container) container.scrollTop = container.scrollHeight;
                },
                async sendMessage() {
                    const text = this.currentMessage.trim();
                    if (!text || this.sending) return;
                    this.sending = true;
                    this.messages.push({ text, sentBy: 'user', time: 'À l\'instant' });
                    this.currentMessage = '';
                    this.$nextTick(() => this.scrollToBottom());
                    try {
                        const response = await fetch('/chat/messages', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            },
                            body: JSON.stringify({ message: text }),
                        });
                        if (!response.ok) throw new Error('Erreur serveur');
                    } catch (e) {
                        this.messages = this.messages.filter(m => m.text !== text);
                        this.messages.push({ text: 'Erreur lors de l\'envoi. Veuillez réessayer.', sentBy: 'support', time: 'À l\'instant' });
                        this.$nextTick(() => this.scrollToBottom());
                    } finally {
                        this.sending = false;
                    }
                },
                initPolling() {
                    this.pollingInterval = setInterval(() => {
                        if (this.chatOpen) this.refreshMessages();
                    }, 8000);
                },
                async refreshMessages() {
                    try {
                        const response = await fetch('/chat/messages', { credentials: 'same-origin' });
                        const data = await response.json();
                        if (Array.isArray(data)) {
                            const localIds = this.messages.map(m => m.id).filter(id => id);
                            const hasNew = data.some(m => m.id && !localIds.includes(m.id));
                            if (hasNew) {
                                this.messages = data.map(item => ({
                                    id: item.id,
                                    text: item.message,
                                    sentBy: item.is_mine ? 'user' : 'support',
                                    time: item.time,
                                }));
                                this.$nextTick(() => this.scrollToBottom());
                            }
                            // Marquer les messages comme lus
                            const unread = data.filter(m => m.id && !m.is_mine);
                            unread.forEach(msg => {
                                if (msg.id) {
                                    fetch('/chat/messages/' + msg.id + '/read', {
                                        method: 'POST',
                                        credentials: 'same-origin',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        },
                                    }).catch(() => {});
                                }
                            });
                        }
                    } catch (e) {}
                }
            }));
        });
    </script>
    <script src="/alpine/alpine.js" defer></script>
    <script src="/alpine/collapse.js" defer></script>
    <script src="{{ asset('tailwind/tailwind.js') }}"></script>
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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

    $baseBgColors = [
        'admin' => 'gray-900',
        'client' => 'emerald-600',
        'technician' => 'blue-900',
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
    $sidebarBgColor = $baseBgColors[$userRole] ?? $baseBgColors['client'];
    
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

    {{-- Header Desktop --}}
    <header class="hidden lg:flex fixed top-0 right-0 h-16 {{ $headerBg }} border-b {{ $headerBorder }} z-20 items-center justify-between px-6 transition-all duration-300 ease-in-out"
        :class="{
            'left-64': !sidebarCollapsed && isDesktop,
            'left-20': sidebarCollapsed && isDesktop,
            'left-0': !isDesktop
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
                        @if(auth()->user() && auth()->user()->role === 'admin')
                         <a href="{{ route('admin.messages.index') }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50">
                             <p class="text-sm font-medium text-gray-800">Nouveau message</p>
                             <p class="text-xs text-gray-500 mt-1">Vous avez reçu un nouveau message</p>
                             <p class="text-xs text-gray-400 mt-1">Il y a 5 minutes</p>
                         </a>
                         <a href="{{ route('admin.messages.index') }}" class="block text-center text-sm {{ $headerText }} hover:text-white py-1.5">
                             Voir tous les messages
                         </a>
                     @else
                         <a href="{{ route('admin.portail.messages') }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50">
                             <p class="text-sm font-medium text-gray-800">Nouveau message</p>
                             <p class="text-xs text-gray-500 mt-1">Vous avez reçu un nouveau message</p>
                             <p class="text-xs text-gray-400 mt-1">Il y a 5 minutes</p>
                         </a>
                         <a href="{{ route('admin.portail.messages') }}" class="block text-center text-sm {{ $headerText }} hover:text-white py-1.5">
                             Voir tous les messages
                         </a>
                     @endif
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                     style="display: none;">
                    <div class="p-2">
                        <a href="#" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Mon Profil
                        </a>
                        <div class="border-t border-gray-200 my-1"></div>
                        <form method="POST" action="{{ url('/logout') }}" id="header-logout-form" class="hidden">@csrf</form>
                        <button type="button" onclick="document.getElementById('header-logout-form').submit()"
                                class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l-4-4m0 0l4-4m-4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
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
              'ml-64': !sidebarCollapsed && isDesktop,
              'ml-20': sidebarCollapsed && isDesktop,
              'ml-0': !isDesktop
          }">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="transition-all duration-300 ease-in-out p-4 text-center text-xs text-gray-500"
            :class="{
                'lg:ml-64': !sidebarCollapsed && isDesktop,
                'lg:ml-20': sidebarCollapsed && isDesktop,
                'ml-0': !isDesktop
            }">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'TG Invest') }}. Tous droits réservés.</p>
    </footer>

    @php
    // Classes CSS fixes par rôle pour que Tailwind JIT les détecte en production
    $chatBgColor = match($userRole) {
        'admin' => 'bg-gray-900',
        'client' => 'bg-emerald-600',
        'technician' => 'bg-blue-900',
        default => 'bg-emerald-600',
    };
    $chatBorderColor = match($userRole) {
        'admin' => 'border-gray-900',
        'client' => 'border-emerald-600',
        'technician' => 'border-blue-900',
        default => 'border-emerald-600',
    };
    @endphp

    {{-- Bulle & Modal de Discussion (Support Chat) - Masquée pour les admins --}}
    @if(auth()->user() && auth()->user()->role !== 'admin')
    <div x-data="chatComponent">
        {{-- Bouton flottant (Bulle de chat) --}}
        <button @click="chatOpen = !chatOpen; if(chatOpen && !messages.length) loadMessages()" 
                class="fixed bottom-6 right-6 z-40 flex h-14 w-14 items-center justify-center rounded-full {{ $chatBgColor }} text-white shadow-lg shadow-emerald-500/25 hover:scale-105 active:scale-95 transition-all duration-200 focus:outline-none">
            {{-- Icône fermée --}}
            <svg x-show="!chatOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            {{-- Icône ouverte --}}
            <svg x-show="chatOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Modal de Discussion --}}
        <div x-show="chatOpen" 
             @click.outside="chatOpen = false"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="fixed bottom-24 right-6 z-50 w-[380px] max-w-[calc(100vw-2rem)] h-[550px] flex flex-col bg-white rounded-2xl shadow-2xl border border-slate-200/60 overflow-hidden"
             style="display: none;">
            
            {{-- En-tête de la discussion --}}
            <div class="px-5 py-4 {{ $chatBgColor }} text-white flex items-center justify-between shadow-sm shrink-0">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="h-10 w-10 bg-white/20 rounded-xl flex items-center justify-center font-bold border border-white/10 text-sm">
                            TG
                        </div>
                        <span class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-emerald-400 border-2 border-emerald-600"></span>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm tracking-wide">Assistance TG'AGRO</h4>
                        <p class="text-[11px] text-white/80 flex items-center gap-1 mt-0.5">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Support connecté
                        </p>
                    </div>
                </div>
                <button @click="chatOpen = false" class="text-white/80 hover:text-white transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Zone des messages --}}
            <div class="flex-1 p-4 overflow-y-auto bg-slate-50 space-y-3.5" id="chat-messages-container">
                {{-- Loader --}}
                <template x-if="loading">
                    <div class="flex items-center justify-center py-10">
                        <div class="flex gap-1.5">
                            <div class="h-2 w-2 rounded-full bg-emerald-400 animate-bounce" style="animation-delay: 0s"></div>
                            <div class="h-2 w-2 rounded-full bg-emerald-400 animate-bounce" style="animation-delay: 0.15s"></div>
                            <div class="h-2 w-2 rounded-full bg-emerald-400 animate-bounce" style="animation-delay: 0.3s"></div>
                        </div>
                    </div>
                </template>

                <template x-for="msg in messages" :key="msg.id ?? $index">
                    <div class="flex items-start gap-2.5 max-w-[88%]" :class="msg.sentBy === 'user' ? 'ml-auto flex-row-reverse' : ''">
                        {{-- Avatar ou initiales --}}
                        <div class="h-8 w-8 rounded-lg text-xs font-bold shrink-0 flex items-center justify-center"
                             :class="msg.sentBy === 'user' ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100'">
                            <span x-text="msg.sentBy === 'user' ? 'M' : 'S'"></span>
                        </div>
                        
                        {{-- Bulle textuelle --}}
                        <div class="flex flex-col" :class="msg.sentBy === 'user' ? 'items-end' : 'items-start'">
                            <div class="p-3 text-sm rounded-2xl shadow-sm border max-w-full break-words"
                                 :class="msg.sentBy === 'user' 
                                     ? '{{ str_replace('bg-', '', $chatBgColor) }} text-white {{ $chatBorderColor }} rounded-tr-none' 
                                     : 'bg-white text-slate-800 border-slate-200/60 rounded-tl-none'">
                                <p class="leading-relaxed whitespace-pre-wrap" x-text="msg.text"></p>
                            </div>
                            <span class="text-[9px] text-slate-400 mt-1 px-0.5" x-text="msg.time"></span>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Formulaire de saisie du message --}}
            <div class="p-3 bg-white border-t border-slate-150 flex items-center gap-2 shrink-0">
                <input type="text" 
                       x-model="currentMessage"
                       @keydown.enter="sendMessage()"
                       :disabled="sending"
                       placeholder="Saisissez votre message..." 
                       class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all disabled:opacity-50">
                
                <button @click="sendMessage()"
                        :disabled="sending || !currentMessage.trim()"
                        class="h-10 w-10 {{ $chatBgColor }} text-white rounded-xl flex items-center justify-center hover:opacity-90 active:scale-95 transition-all shrink-0 shadow-sm shadow-emerald-600/10 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="!sending" class="h-4 w-4 transform rotate-45 -translate-x-0.5 translate-y-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <svg x-show="sending" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @endif

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
        [x-cloak] { display: none !important; }

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
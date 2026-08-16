@extends('layouts.app')

@section('title', 'Paramètres Système')
@section('page-title', 'Paramètres Système')

@section('content')
<div class="min-h-screen bg-gray-50/50" x-data="settingsApp">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- En-tête --}}
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Paramètres Système</h1>
                    <p class="mt-2 text-gray-600">Personnalisez l'apparence et le comportement de la plateforme</p>
                </div>
                
                {{-- Aperçu live --}}
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Aperçu</p>
                        <p class="text-xs text-gray-500 mt-1">En-tête, barre latérale & textes</p>
                    </div>
                    <div class="flex h-16 w-44 overflow-hidden rounded-xl border-2 border-gray-200 shadow-sm">
                        <div class="flex w-1/3 items-center justify-center"
                             :style="`background-color: ${settings.sidebar_color || '#1f2937'}; color: ${settings.text_color || '#ffffff'}`">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM14 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM4 16a2.25 2.25 0 012.25-2.25h2.25a2.25 2.25 0 012.25 2.25v2.25A2.25 2.25 0 016.75 20.25H4.5A2.25 2.25 0 012.25 18v-2.25z"/>
                            </svg>
                        </div>
                        <div class="flex flex-1 flex-col">
                            <div class="flex h-1/3 items-center px-2 text-xs font-semibold"
                                 :style="`background-color: ${settings.header_color || '#111827'}; color: ${settings.text_color || '#ffffff'}`">
                                En-tête
                            </div>
                            <div class="flex flex-1 items-center gap-1.5 px-2"
                                 :style="`background-color: ${settings.primary_color || '#111827'}`">
                                <span class="h-3 w-3 rounded-full" :style="`background-color: ${settings.secondary_color || '#f59e0b'}`"></span>
                                <span class="text-xs font-medium" :style="`color: ${settings.text_color || '#ffffff'}`">Aperçu</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs Navigation --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px overflow-x-auto" aria-label="Tabs">
                    <button @click="activeTab = 'appearance'"
                            :class="activeTab === 'appearance' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        Apparence
                    </button>
                    <button @click="activeTab = 'information'"
                            :class="activeTab === 'information' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Informations
                    </button>
                    <button @click="activeTab = 'notifications'"
                            :class="activeTab === 'notifications' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Notifications
                    </button>
                    <button @click="activeTab = 'security'"
                            :class="activeTab === 'security' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Sécurité
                    </button>
                </nav>
            </div>

            <div class="p-6 lg:p-8">
                <form method="POST" action="{{ route('admin.settings.system.update') }}">
                    @csrf
                    
                    {{-- Tab: Apparence --}}
                    <div x-show="activeTab === 'appearance'" x-transition.opacity.duration.300ms>
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Couleurs</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Couleur principale</label>
                                        <div class="flex items-center gap-4">
                                            <input type="color" 
                                                   x-model="settings.primary_color"
                                                   name="settings[primary_color]"
                                                   value="{{ $settings['primary_color'] ?? '#111827' }}"
                                                   class="h-12 w-12 rounded-lg border-2 border-gray-200 cursor-pointer shadow-sm">
                                            <div class="flex-1">
                                                <input type="text" 
                                                       :value="settings.primary_color || '#111827'"
                                                       @input="settings.primary_color = $event.target.value"
                                                       class="w-full px-3 py-2 text-sm font-mono border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                       placeholder="#111827">
                                            </div>
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500">Utilisée pour les boutons, liens et éléments principaux</p>
                                    </div>

                                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Couleur secondaire</label>
                                        <div class="flex items-center gap-4">
                                            <input type="color" 
                                                   x-model="settings.secondary_color"
                                                   name="settings[secondary_color]"
                                                   value="{{ $settings['secondary_color'] ?? '#f59e0b' }}"
                                                   class="h-12 w-12 rounded-lg border-2 border-gray-200 cursor-pointer shadow-sm">
                                            <div class="flex-1">
                                                <input type="text" 
                                                       :value="settings.secondary_color || '#f59e0b'"
                                                       @input="settings.secondary_color = $event.target.value"
                                                       class="w-full px-3 py-2 text-sm font-mono border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                       placeholder="#f59e0b">
                                            </div>
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500">Utilisée pour les accents et surbrillances</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Barre latérale, en-tête & textes</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Couleur de la barre latérale</label>
                                        <div class="flex items-center gap-4">
                                            <input type="color" 
                                                   x-model="settings.sidebar_color"
                                                   name="settings[sidebar_color]"
                                                   value="{{ $settings['sidebar_color'] ?? '#1f2937' }}"
                                                   class="h-12 w-12 rounded-lg border-2 border-gray-200 cursor-pointer shadow-sm">
                                            <div class="flex-1">
                                                <input type="text" 
                                                       :value="settings.sidebar_color || '#1f2937'"
                                                       @input="settings.sidebar_color = $event.target.value"
                                                       class="w-full px-3 py-2 text-sm font-mono border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                       placeholder="#1f2937">
                                            </div>
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500">Fond de la navigation latérale</p>
                                    </div>

                                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Couleur de l'en-tête</label>
                                        <div class="flex items-center gap-4">
                                            <input type="color" 
                                                   x-model="settings.header_color"
                                                   name="settings[header_color]"
                                                   value="{{ $settings['header_color'] ?? '#111827' }}"
                                                   class="h-12 w-12 rounded-lg border-2 border-gray-200 cursor-pointer shadow-sm">
                                            <div class="flex-1">
                                                <input type="text" 
                                                       :value="settings.header_color || '#111827'"
                                                       @input="settings.header_color = $event.target.value"
                                                       class="w-full px-3 py-2 text-sm font-mono border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                       placeholder="#111827">
                                            </div>
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500">Fond de l'en-tête supérieur</p>
                                    </div>

                                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Couleur des textes</label>
                                        <div class="flex items-center gap-4">
                                            <input type="color" 
                                                   x-model="settings.text_color"
                                                   name="settings[text_color]"
                                                   value="{{ $settings['text_color'] ?? '#ffffff' }}"
                                                   class="h-12 w-12 rounded-lg border-2 border-gray-200 cursor-pointer shadow-sm">
                                            <div class="flex-1">
                                                <input type="text" 
                                                       :value="settings.text_color || '#ffffff'"
                                                       @input="settings.text_color = $event.target.value"
                                                       class="w-full px-3 py-2 text-sm font-mono border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                       placeholder="#ffffff">
                                            </div>
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500">Texte sur l'en-tête et la barre latérale</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Thème</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="settings[theme]" value="light" 
                                               {{ ($settings['theme'] ?? 'light') === 'light' ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition-all hover:border-gray-300">
                                            <div class="w-full h-20 bg-white border border-gray-200 rounded-lg mb-3 shadow-sm"></div>
                                            <p class="text-sm font-medium text-gray-900">Clair</p>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="settings[theme]" value="dark" 
                                               {{ ($settings['theme'] ?? '') === 'dark' ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition-all hover:border-gray-300">
                                            <div class="w-full h-20 bg-gray-900 border border-gray-700 rounded-lg mb-3 shadow-sm"></div>
                                            <p class="text-sm font-medium text-gray-900">Sombre</p>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="settings[theme]" value="auto" 
                                               {{ ($settings['theme'] ?? '') === 'auto' ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition-all hover:border-gray-300">
                                            <div class="w-full h-20 bg-gradient-to-r from-white to-gray-900 border border-gray-300 rounded-lg mb-3 shadow-sm"></div>
                                            <p class="text-sm font-medium text-gray-900">Automatique</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab: Informations --}}
                    <div x-show="activeTab === 'information'" x-transition.opacity.duration.300ms style="display: none;">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom de l'application</label>
                                        <input type="text" 
                                               name="settings[app_name]"
                                               value="{{ $settings['app_name'] ?? 'TG Invest' }}"
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                               placeholder="TG Invest">
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Slogan</label>
                                        <input type="text" 
                                               name="settings[app_slogan]"
                                               value="{{ $settings['app_slogan'] ?? 'Diaspor Invest' }}"
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                               placeholder="Diaspor Invest">
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email de contact</label>
                                        <input type="email" 
                                               name="settings[support_email]"
                                               value="{{ $settings['support_email'] ?? 'contact@tginvest.com' }}"
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                               placeholder="contact@tginvest.com">
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                                        <input type="text" 
                                               name="settings[support_phone]"
                                               value="{{ $settings['support_phone'] ?? '+33 1 23 45 67 89' }}"
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                               placeholder="+33 1 23 45 67 89">
                                    </div>
                                    <div class="md:col-span-2 bg-gray-50 rounded-xl p-5 border border-gray-200">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                                        <input type="text" 
                                               name="settings[app_address]"
                                               value="{{ $settings['app_address'] ?? '123 Avenue de la République, 34000 Montpellier, France' }}"
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                               placeholder="Adresse complète">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab: Notifications --}}
                    <div x-show="activeTab === 'notifications'" x-transition.opacity.duration.300ms style="display: none;">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Préférences de notification</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Notifications par email</p>
                                            <p class="text-xs text-gray-500 mt-1">Recevoir les notifications importantes par email</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="settings[email_notifications]" 
                                                   {{ ($settings['email_notifications'] ?? 'true') === 'true' ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Notifications push</p>
                                            <p class="text-xs text-gray-500 mt-1">Recevoir les notifications en temps réel</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="settings[push_notifications]" 
                                                   {{ ($settings['push_notifications'] ?? 'true') === 'true' ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Notifications SMS</p>
                                            <p class="text-xs text-gray-500 mt-1">Recevoir les alertes par SMS</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="settings[sms_notifications]" 
                                                   {{ ($settings['sms_notifications'] ?? 'false') === 'true' ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab: Sécurité --}}
                    <div x-show="activeTab === 'security'" x-transition.opacity.duration.300ms style="display: none;">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Paramètres de sécurité</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Authentification à deux facteurs</p>
                                            <p class="text-xs text-gray-500 mt-1">Exiger une vérification supplémentaire lors de la connexion</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="settings[two_factor_auth]" 
                                                   {{ ($settings['two_factor_auth'] ?? 'false') === 'true' ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Connexion unique (SSO)</p>
                                            <p class="text-xs text-gray-500 mt-1">Autoriser la connexion via SSO</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="settings[sso_enabled]" 
                                                   {{ ($settings['sso_enabled'] ?? 'false') === 'true' ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>

                                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Durée de session (minutes)</label>
                                        <input type="number" 
                                               name="settings[session_timeout]"
                                               value="{{ $settings['session_timeout'] ?? '120' }}"
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                               placeholder="120">
                                        <p class="mt-2 text-xs text-gray-500">Déconnexion automatique après inactivité</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.settings') }}" 
                           class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Retour aux KPIs
                        </a>
                        <button type="submit" 
                                class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('alpine-components')
Alpine.data('settingsApp', () => ({
    activeTab: 'appearance',
    settings: {
        primary_color: @json($settings['primary_color'] ?? '#111827'),
        secondary_color: @json($settings['secondary_color'] ?? '#f59e0b'),
        sidebar_color: @json($settings['sidebar_color'] ?? '#1f2937'),
        header_color: @json($settings['header_color'] ?? '#111827'),
        text_color: @json($settings['text_color'] ?? '#ffffff')
    }
}));
@endpush

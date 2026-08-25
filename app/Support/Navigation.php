<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

class Navigation
{
    public static function tree()
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        $rules = [
            // Dashboard - accessible à tous les rôles connectés
            [
                'name' => 'Tableau de Bord',
                'route' => 'dashboard',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM14 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM4 16a2.25 2.25 0 012.25-2.25h2.25a2.25 2.25 0 012.25 2.25v2.25A2.25 2.25 0 016.75 20.25H4.5A2.25 2.25 0 012.25 18v-2.25z"/>',
                'can' => true,
            ],

            // ============================================
            // ESPACE ADMIN
            // ============================================
            [
                'name' => 'Gestion des Clients',
                'route' => 'admin.clients.index',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>',
                'segments' => ['admin', 'clients'],
                'can' => $user->role === 'admin',
            ],

            [
                'name' => 'Gestion des Techniciens',
                'route' => 'admin.technicians.index',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.725A7.488 7.488 0 0012 15.75a7.482 7.482 0 00-6 3m12 0v.275A7.488 7.488 0 0012 15.75a7.482 7.482 0 00-6 3v.275"/>',
                'segments' => ['admin', 'technicians'],
                'can' => $user->role === 'admin',
            ],
            [
                'name' => 'Gestion des Missions',
                'route' => 'admin.technitian.missions.kanban',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM14 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM4 16a2.25 2.25 0 012.25-2.25h2.25a2.25 2.25 0 012.25 2.25v2.25A2.25 2.25 0 016.75 20.25H4.5A2.25 2.25 0 012.25 18v-2.25z"/>',
                'segments' => ['admin', 'technitian', 'missions', 'kanban'],
                'can' => $user->role === 'admin',
            ],
            [
                'name' => 'Gestion des Exploitations',
                'route' => 'admin.farms.index',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.188-1.066A2.25 2.25 0 012.25 17.5v-11.5a2.25 2.25 0 012.25-2.25h15a2.25 2.25 0 012.25 2.25v11.5a2.25 2.25 0 01-2.25 2.25L9 18.75v-8.25z"/>',
                'segments' => ['admin', 'farms'],
                'can' => $user->role === 'admin',
            ],
            // [
            //     'name' => 'Gestion des Rapports',
            //     'route' => 'admin.reports.datatable',
            //     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>',
            //     'segments' => ['admin', 'reports'],
            //     'can' => $user->role === 'admin',
            // ],
            [
                'name' => 'Rapports de Visite',
                'route' => 'admin.rapports-visite.index',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c0 .621.504 1.125 1.125 1.125h2.25"/>',
                'segments' => ['admin', 'rapports-visite'],
                'can' => $user->role === 'admin',
            ],
            [
                'name' => 'Galleries',
                'route' => 'admin.gallery.index',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25z"/>',
                'segments' => ['admin', 'gallery'],
                'can' => $user->role === 'admin',
            ],
            // [
            //     'name' => 'Validation Photos',
            //     'route' => 'admin.photos.validation',
            //     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            //     'segments' => ['admin', 'photos'],
            //     'can' => $user->role === 'admin',
            // ],
            [
                'name' => 'Calendrier',
                'route' => 'admin.calendar',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>',
                'segments' => ['admin', 'calendar'],
                'can' => $user->role === 'admin',
            ],
            [
                'name' => 'Diagnostique du terrain',
                'route' => 'admin.data.validation',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM14 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM4 16a2.25 2.25 0 012.25-2.25h2.25a2.25 2.25 0 012.25 2.25v2.25A2.25 2.25 0 016.75 20.25H4.5A2.25 2.25 0 012.25 18v-2.25z"/>',
                'segments' => ['admin', 'data'],
                'can' => $user->role === 'admin',
            ],
            // [
            //     'name' => 'Prix du Marché',
            //     'route' => 'admin.market-prices.index',
            //     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            //     'segments' => ['admin', 'market-prices'],
            //     'can' => $user->role === 'admin',
            // ],
            [
                'name' => 'Gestion des Abonnements',
                'route' => 'admin.subscriptions.index',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                'segments' => ['admin', 'subscriptions'],
                'can' => $user->role === 'admin',
            ],
            [
                'name' => 'Gestion des Utilisateurs',
                'route' => 'admin.users.index',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.25h15.002c.966 0 1.75-.784 1.75-1.75V18a5.25 5.25 0 00-10.5 0v.75c0 .966.784 1.75 1.75 1.75z"/>',
                'segments' => ['admin', 'users'],
                'can' => $user->role === 'admin',
            ],
            [
                'name' => 'Paramètres KPIs',
                'route' => 'admin.settings',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
                'segments' => ['admin', 'settings'],
                'can' => $user->role === 'admin',
            ],
            [
                'name' => 'Paramètres Système',
                'route' => 'admin.settings.system',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/>',
                'segments' => ['admin', 'settings'],
                'can' => $user->role === 'admin',
            ],
            // [
            //     'name' => 'Rôles et Permissions',
            //     'route' => 'admin.roles.index',
            //     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            //     'segments' => ['admin', 'roles'],
            //     'can' => $user->role === 'admin',
            // ],
            // [
            //     'name' => 'Permissions',
            //     'route' => 'admin.permissions.index',
            //     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h7.5M3 12h18M3 6h18M3 18h18M3 12a9 9 0 0118 0"/>',
            //     'segments' => ['admin', 'permissions'],
            //     'can' => $user->role === 'admin',
            // ],

            // ============================================
            // ESPACE CLIENT (PORTAIL)
            // ============================================
            [
                'name' => 'Mes Exploitations',
                'route' => 'admin.portail.farms',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.188-1.066A2.25 2.25 0 012.25 17.5v-11.5a2.25 2.25 0 012.25-2.25h15a2.25 2.25 0 012.25 2.25v11.5a2.25 2.25 0 01-2.25 2.25L9 18.75v-8.25z"/>',
                'segments' => ['admin', 'portail', 'farms'],
                'can' => $user->role === 'client',
            ],
            [
                'name' => 'Galerie Photos',
                'route' => 'admin.portail.gallery',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25z"/>',
                'segments' => ['admin', 'portail', 'gallery'],
                'can' => $user->role === 'client',
            ],
            [
                'name' => 'Mes Rapports',
                'route' => 'admin.portail.reports',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>',
                'segments' => ['admin', 'portail', 'reports'],
                'can' => $user->role === 'client',
            ],
            // [
            //     'name' => 'Mes Saisies de Données',
            //     'route' => 'admin.portail.data',
            //     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM14 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM4 16a2.25 2.25 0 012.25-2.25h2.25a2.25 2.25 0 012.25 2.25v2.25A2.25 2.25 0 016.75 20.25H4.5A2.25 2.25 0 012.25 18v-2.25z"/>',
            //     'segments' => ['admin', 'portail', 'data'],
            //     'can' => $user->role === 'client',
            // ],
            [
                'name' => 'Messagerie',
                'route' => 'admin.portail.messages',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>',
                'segments' => ['admin', 'portail', 'messages'],
                'can' => $user->role === 'client',
            ],
            [
                'name' => 'Mon Abonnement',
                'route' => 'admin.portail.subscription',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                'segments' => ['admin', 'portail', 'subscription'],
                'can' => $user->role === 'client',
            ],

            // ============================================
            // ESPACE TECHNICIEN
            // ============================================
            [
                'name' => 'Mes Missions',
                'route' => 'admin.technitian.missions',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 17.816l-3.42-3.42a1 1 0 00-1.42 0L3 16.25V5.25a2.25 2.25 0 012.25-2.25h10.5A2.25 2.25 0 0118 5.25v11a2.25 2.25 0 01-2.25 2.25H5.25"/>',
                'segments' => ['admin', 'technitian', 'missions'],
                'can' => $user->role === 'technician',
            ],
            [
                'name' => 'Rapports de Visite',
                'route' => 'admin.rapports-visite.index',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c0 .621.504 1.125 1.125 1.125h2.25"/>',
                'segments' => ['admin', 'rapports-visite'],
                'can' => $user->role === 'technician',
            ],
            // [
            //     'name' => 'Tableau Kanban',
            //     'route' => 'admin.technitian.missions',
            //     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM14 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM4 16a2.25 2.25 0 012.25-2.25h2.25a2.25 2.25 0 012.25 2.25v2.25A2.25 2.25 0 016.75 20.25H4.5A2.25 2.25 0 012.25 18v-2.25z"/>',
            //     'segments' => ['admin', 'technitian', 'missions', 'kanban'],
            //     'can' => $user->role === 'technician' || $user->role === 'admin',
            // ],
            [
                'name' => 'Upload Photos',
                'route' => 'admin.technitian.photos.create',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25z"/>',
                'segments' => ['admin', 'technitian', 'photos'],
                'can' => $user->role === 'technician',
            ],
            [
                'name' => 'Galerie Photos',
                'route' => 'admin.gallery.index',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25z"/>',
                'segments' => ['admin', 'gallery'],
                'can' => $user->role === 'technician',
            ],
            [
                'name' => 'Saisie Données',
                'route' => 'admin.technitian.data.create',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM14 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM4 16a2.25 2.25 0 012.25-2.25h2.25a2.25 2.25 0 012.25 2.25v2.25A2.25 2.25 0 016.75 20.25H4.5A2.25 2.25 0 012.25 18v-2.25z"/>',
                'segments' => ['admin', 'technitian', 'data'],
                'can' => $user->role === 'technician',
            ],
            [
                'name' => 'Calendrier',
                'route' => 'admin.technitian.calendar',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>',
                'segments' => ['admin', 'technitian', 'calendar'],
                'can' => $user->role === 'technician',
            ],
        ];

        $categories = [
            'Tableau de Bord' => 'Tableau de bord',
            'Gestion des Clients' => 'Gestion',
            'Gestion des Techniciens' => 'Gestion',
            'Gestion des Missions' => 'Terrain & Visites',
            'Gestion des Exploitations' => 'Gestion',
            'Rapports de Visite' => 'Terrain & Visites',
            'Galleries' => 'Médias',
            'Calendrier' => 'Terrain & Visites',
            'Diagnostique du terrain' => 'Terrain & Visites',
            'Gestion des Abonnements' => 'Gestion',
            'Gestion des Utilisateurs' => 'Gestion',
            'Paramètres KPIs' => 'Tableau de bord',
            'Paramètres Système' => 'Tableau de bord',
            'Mes Exploitations' => 'Mon espace',
            'Galerie Photos' => 'Mon espace',
            'Mes Rapports' => 'Mon espace',
            'Mes Saisies de Données' => 'Mon espace',
            'Messagerie' => 'Mon espace',
            'Mon Abonnement' => 'Mon espace',
            'Mes Missions' => 'Mon espace',
            'Upload Photos' => 'Mon espace',
            'Saisie Données' => 'Mon espace',
        ];

        foreach ($rules as &$item) {
            $item['category'] = $categories[$item['name']] ?? 'Autres';
        }
        unset($item);

        return $rules;
    }

    private static function canAccessMenu($user, string $menuCode): bool
    {
        if (! $user) {
            return false;
        }

        // Pour TG'INVEST, les accès sont gérés directement par les rôles
        // Cette méthode peut être étendue si besoin de permissions plus fines
        return true;
    }

    private static function itemVisible(array $item): bool
    {
        return $item['can'] ?? true;
    }

    public static function grouped()
    {
        $navigation = self::tree();

        $groups = [];
        $order = [];

        foreach ($navigation as $item) {
            if (! self::itemVisible($item)) {
                continue;
            }

            $category = $item['category'] ?? 'Autres';

            if (! isset($groups[$category])) {
                $groups[$category] = [];
                $order[] = $category;
            }

            $groups[$category][] = $item;
        }

        return collect($order)
            ->map(fn($category) => ['label' => $category, 'items' => $groups[$category]])
            ->values()
            ->all();
    }

    public static function filtered()
    {
        $navigation = self::tree();

        return collect($navigation)->map(function ($item) {
            if (isset($item['children'])) {
                $item['children'] = collect($item['children'])
                    ->filter(fn($child) => self::itemVisible($child))
                    ->values()
                    ->toArray();
            }

            return $item;
        })->filter(function ($item) {
            if (($item['type'] ?? null) === 'separator') {
                return true;
            }

            if (! self::itemVisible($item)) {
                return false;
            }

            if (isset($item['children']) && empty($item['children'])) {
                return false;
            }

            return true;
        })->values()->toArray();
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Farm;
use App\Models\Mission;
use App\Models\RapportVisite;
use App\Models\Photo;
use App\Models\DataEntry;
use App\Models\MarketPrice;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Technician;
use App\Models\Report;
use App\Support\PlatformSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    private const FEATURES = [
        'clients' => ['label' => 'Clients', 'icon' => 'M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z'],
        'technicians' => ['label' => 'Techniciens', 'icon' => 'M18 18.725A7.488 7.488 0 0012 15.75a7.482 7.482 0 00-6 3m12 0v.275A7.488 7.488 0 0012 15.75a7.482 7.482 0 00-6 3v.275'],
        'farms' => ['label' => 'Exploitations', 'icon' => 'M9 20l-5.188-1.066A2.25 2.25 0 012.25 17.5v-11.5a2.25 2.25 0 012.25-2.25h15a2.25 2.25 0 012.25 2.25v11.5a2.25 2.25 0 01-2.25 2.25L9 18.75v-8.25z'],
        'missions' => ['label' => 'Missions', 'icon' => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM14 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM4 16a2.25 2.25 0 012.25-2.25h2.25a2.25 2.25 0 012.25 2.25v2.25A2.25 2.25 0 016.75 20.25H4.5A2.25 2.25 0 012.25 18v-2.25z'],
        'rapports' => ['label' => 'Rapports de Visite', 'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c0 .621.504 1.125 1.125 1.125h2.25'],
        'gallery' => ['label' => 'Galleries', 'icon' => 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25z'],
        'photos' => ['label' => 'Photos', 'icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'data' => ['label' => 'Diagnostique du terrain', 'icon' => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM14 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM4 16a2.25 2.25 0 012.25-2.25h2.25a2.25 2.25 0 012.25 2.25v2.25A2.25 2.25 0 016.75 20.25H4.5A2.25 2.25 0 012.25 18v-2.25z'],
        'market' => ['label' => 'Prix du Marché', 'icon' => 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'subscriptions' => ['label' => 'Abonnements', 'icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'users' => ['label' => 'Utilisateurs', 'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.25h15.002c.966 0 1.75-.784 1.75-1.75V18a5.25 5.25 0 00-10.5 0v.75c0 .966.784 1.75 1.75 1.75z'],
        'reports' => ['label' => 'Rapports', 'icon' => 'M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z'],
    ];

    public function index()
    {
        $user = Auth::user();

        $enabled = PlatformSettings::get('dashboard_features', null);
        if ($enabled === null) {
            // Par défaut, toutes les fonctionnalités sont activées
            $enabled = array_fill_keys(array_keys(self::FEATURES), true);
        }

        $features = collect(self::FEATURES)->map(function ($feature, $key) use ($enabled) {
            $feature['key'] = $key;
            $feature['enabled'] = ! empty($enabled[$key]);
            $feature['count'] = $this->countFor($key);
            $feature['trend'] = $this->trendFor($key);
            return $feature;
        })->values()->toArray();

        $stats = [
            'labels' => collect($features)->pluck('label')->toArray(),
            'counts' => collect($features)->pluck('count')->toArray(),
            'colors' => $this->palette(count($features)),
        ];

        return view('admin.settings.index', compact('features', 'stats', 'user'));
    }

    public function updateFeatures(Request $request)
    {
        $selected = (array) $request->input('features', []);
        $enabled = [];
        foreach (array_keys(self::FEATURES) as $key) {
            $enabled[$key] = in_array($key, $selected, true);
        }

        PlatformSettings::set('dashboard_features', $enabled);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'enabled' => $enabled]);
        }

        return redirect()->route('admin.settings')->with('success', 'Préférences de tableau de bord enregistrées.');
    }

    private function countFor(string $key): int
    {
        return match ($key) {
            'clients' => Client::count(),
            'technicians' => Technician::count(),
            'farms' => Farm::count(),
            'missions' => Mission::count(),
            'rapports' => RapportVisite::count(),
            'gallery' => Photo::count(),
            'photos' => Photo::count(),
            'data' => DataEntry::count(),
            'market' => MarketPrice::count(),
            'subscriptions' => Subscription::count(),
            'users' => User::count(),
            'reports' => Report::count(),
            default => 0,
        };
    }

    private function modelFor(string $key): ?string
    {
        return match ($key) {
            'clients' => Client::class,
            'technicians' => Technician::class,
            'farms' => Farm::class,
            'missions' => Mission::class,
            'rapports' => RapportVisite::class,
            'gallery' => Photo::class,
            'photos' => Photo::class,
            'data' => DataEntry::class,
            'market' => MarketPrice::class,
            'subscriptions' => Subscription::class,
            'users' => User::class,
            'reports' => Report::class,
            default => null,
        };
    }

    /**
     * Évolution en % par rapport au mois dernier (créations du mois en cours
     * vs créations du mois précédent). Données dynamiques provenant de la BDD.
     */
    private function trendFor(string $key): float
    {
        $model = $this->modelFor($key);

        if (! $model) {
            return 0;
        }

        $now = now();
        $startThis = $now->copy()->startOfMonth();
        $startLast = $now->copy()->subMonth()->startOfMonth();
        $endLast = $now->copy()->subMonth()->endOfMonth();

        $thisMonth = $model::whereBetween('created_at', [$startThis, $now])->count();
        $lastMonth = $model::whereBetween('created_at', [$startLast, $endLast])->count();

        if ($lastMonth === 0) {
            return $thisMonth > 0 ? 100 : 0;
        }

        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    private function palette(int $count): array
    {
        $base = [
            '#6366f1', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444',
            '#3b82f6', '#ec4899', '#14b8a6', '#f97316', '#84cc16',
            '#06b6d4', '#a855f7', '#eab308', '#22c55e',
        ];

        $colors = [];
        for ($i = 0; $i < max($count, 1); $i++) {
            $colors[] = $base[$i % count($base)];
        }

        return $colors;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Support\DashboardFeatures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $features = DashboardFeatures::withStats();

        $stats = [
            'labels' => collect($features)->pluck('label')->values()->toArray(),
            'counts' => collect($features)->pluck('count')->values()->toArray(),
            'colors' => DashboardFeatures::palette(count($features)),
        ];

        $monthLabels = DashboardFeatures::monthLabels();

        $systemSettings = SystemSetting::all()->pluck('value', 'key');

        return view('admin.settings.index', compact('features', 'stats', 'user', 'monthLabels', 'systemSettings'));
    }

    public function updateFeatures(Request $request)
    {
        $selected = $request->input('features', []);

        if (is_array($selected) && count($selected) > 0 && is_array(reset($selected))) {
            DashboardFeatures::saveOrder($selected);
        } else {
            $items = collect(DashboardFeatures::ordered())->map(function ($feature) use ($selected) {
                return [
                    'key' => $feature['key'],
                    'enabled' => in_array($feature['key'], (array) $selected, true),
                ];
            })->values()->all();

            DashboardFeatures::saveOrder($items);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.settings')->with('success', 'Préférences de tableau de bord enregistrées.');
    }

    public function updateSystem(Request $request)
    {
        $validated = $request->validate([
            'settings' => ['required', 'array'],
        ]);

        foreach ($validated['settings'] as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => is_array($value) ? 'array' : 'text']
            );
        }

        return redirect()->route('admin.settings')->with('success', 'Paramètres système enregistrés.');
    }
}

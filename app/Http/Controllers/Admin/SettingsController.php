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

    public function system()
    {
        $settings = SystemSetting::all()->pluck('value', 'key');

        return view('admin.settings.system', compact('settings'));
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

        return redirect()->route('admin.settings.system')->with('success', 'Paramètres système enregistrés.');
    }

    public function payments()
    {
        $settings = SystemSetting::where('group', 'payments')->get()->pluck('value', 'key');

        return view('admin.settings.payments', compact('settings'));
    }

    public function updatePayments(Request $request)
    {
        $validated = $request->validate([
            'payment_default_provider' => ['nullable', 'string', 'max:100'],
            'payment_default_phone' => ['nullable', 'string', 'max:20'],
            'payment_default_code_prefix' => ['nullable', 'string', 'max:50'],
            'payment_reminder_days_before' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key, 'group' => 'payments'],
                ['value' => $value, 'type' => is_array($value) ? 'array' : 'text']
            );
        }

        return redirect()->route('admin.settings.payments')->with('success', 'Paramètres de paiement enregistrés.');
    }
}

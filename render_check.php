<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
app('view')->share('errors', new \Illuminate\Support\ViewErrorBag());
app('view')->share('session', session());
if (! app('view')->shared('user')) {
    app('view')->share('user', \App\Models\User::first());
}

try {
    $features = \App\Support\DashboardFeatures::withStats();
} catch (\Throwable $e) {
    $features = [];
}

$stats = [
    'labels' => collect($features)->pluck('label')->values()->toArray(),
    'counts' => collect($features)->pluck('count')->values()->toArray(),
    'colors' => \App\Support\DashboardFeatures::palette(max(count($features), 1)),
];
$monthLabels = \App\Support\DashboardFeatures::monthLabels();

try {
    $html = view('admin.settings.index', [
        'features'  => $features,
        'stats'     => $stats,
        'user'      => \App\Models\User::first(),
        'monthLabels' => $monthLabels,
    ])->render();
} catch (\Throwable $e) {
    echo "RENDER ERROR: " . $e->getMessage() . "\n";
    exit;
}

$lines = explode("\n", $html);
echo "--- total lines: " . count($lines) . "\n";
// Show all lines containing '>}' or '}>' that are inside script context
foreach ($lines as $i => $l) {
    if (strpos($l, '}>') !== false || strpos($l, '}>') !== false) {
        $ln = $i + 1;
        // print surrounding context
        $start = max(0, $ln - 2);
        for ($j = $start; $j < min(count($lines), $ln + 3); $j++) {
            echo ($j + 1) . ": " . $lines[$j] . "\n";
        }
        echo "...\n";
    }
}

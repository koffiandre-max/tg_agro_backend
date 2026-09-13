<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;

$user = \App\Models\User::where('role', 'technician')->orderBy('id')->first();
Auth::login($user);

$http = $app->make(Illuminate\Contracts\Http\Kernel::class);
try {
    $resp = $http->handle(Illuminate\Http\Request::create('/technitian/photos/create', 'GET'));
    $content = $resp->getContent();
    echo "STATUS: " . $resp->getStatusCode() . " (" . strlen($content) . " octets)\n";
    foreach (['max-w-7xl', 'x-select', 'Ajouter des photos', 'Obtenir ma position', 'Enregistrer les photos'] as $needle) {
        echo (str_contains($content, $needle) ? 'OK  ' : 'MISS') . " : {$needle}\n";
    }
    // Vérifier qu'il ne reste pas de <select> natif pour client/farm
    echo (str_contains($content, '<select name="client_id"') ? 'STILL native client select' : 'OK  plus de select natif client_id');
    echo "\n";
    echo (str_contains($content, '<select name="farm_id"') ? 'STILL native farm select' : 'OK  plus de select natif farm_id');
    echo "\n";
} catch (\Throwable $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() . "\n";
    exit(1);
}
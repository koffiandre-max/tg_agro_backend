<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;

$request = Request::create('/login', 'POST', [
    'email' => 'admin@tginvest.com',
    'password' => 'password123',
    'remember' => false,
]);

$app->instance('request', $request);
$session = $app->make('session')->driver();
$request->setLaravelSession($session);

try {
    $response = (new AuthController())->login($request);
    echo 'OK: ' . (method_exists($response, 'getContent') ? $response->getContent() : json_encode($response)) . PHP_EOL;
} catch (\Throwable $e) {
    echo 'EXCEPTION: ' . get_class($e) . PHP_EOL;
    echo 'MESSAGE: ' . $e->getMessage() . PHP_EOL;
    echo 'FILE: ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
    echo 'TRACE: ' . $e->getTraceAsString() . PHP_EOL;
}

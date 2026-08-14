<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$app['config']->set('app.debug', true);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$request = Request::create('/login', 'POST', [], [], [], [
    'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
], http_build_query([
    'email' => 'admin@tginvest.com',
    'password' => 'password123',
    'remember' => false,
]));

try {
    $response = $app->make(Illuminate\Contracts\Http\Kernel::class)->handle($request);
    echo 'STATUS: ' . $response->getStatusCode() . PHP_EOL;
    echo 'BODY: ' . $response->getContent() . PHP_EOL;
} catch (\Throwable $e) {
    echo 'EXCEPTION: ' . get_class($e) . PHP_EOL;
    echo 'MESSAGE: ' . $e->getMessage() . PHP_EOL;
    echo 'FILE: ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
    echo 'TRACE: ' . $e->getTraceAsString() . PHP_EOL;
}

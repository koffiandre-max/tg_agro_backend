<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$consoleKernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$consoleKernel->bootstrap();

use Illuminate\Http\Request;

// 1) GET /login to obtain session cookie + csrf token
$get = Request::create('/login', 'GET');
$getResponse = $kernel->handle($get);
$cookie = null;
foreach ($getResponse->headers->getCookies() as $c) {
    if ($c->getName() === 'tg_agro_backend_session' || $c->getName() === config('session.cookie')) {
        $cookie = $c;
    }
}
if (!$cookie) {
    // fallback: grab first cookie
    $cookies = $getResponse->headers->getCookies();
    $cookie = $cookies[0] ?? null;
}
$cookieName = $cookie ? $cookie->getName() : config('session.cookie');
$cookieValue = $cookie ? $cookie->getValue() : '';
echo "COOKIE: $cookieName=" . substr($cookieValue, 0, 20) . "..." . PHP_EOL;

// extract csrf token from meta tag
$body = $getResponse->getContent();
preg_match('/name="csrf-token" content="([^"]+)"/', $body, $m);
$csrf = $m[1] ?? '';
echo "CSRF: " . substr($csrf, 0, 20) . "..." . PHP_EOL;

// 2) POST /login with cookie + csrf header
$post = Request::create('/login', 'POST', [
    'email' => 'admin@tginvest.com',
    'password' => 'password123',
    'remember' => false,
], [], [], [
    'HTTP_ACCEPT' => 'application/json',
    'CONTENT_TYPE' => 'application/json',
    'HTTP_X_CSRF_TOKEN' => $csrf,
    'HTTP_COOKIE' => "$cookieName=$cookieValue",
], json_encode([
    'email' => 'admin@tginvest.com',
    'password' => 'password123',
    'remember' => false,
]));

$postResponse = $kernel->handle($post);
echo "STATUS: " . $postResponse->getStatusCode() . PHP_EOL;
echo "BODY: " . $postResponse->getContent() . PHP_EOL;

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

$u = User::where('email', 'admin@tginvest.com')->first();
echo 'exists=' . ($u ? 'yes' : 'no') . PHP_EOL;
echo 'role=' . ($u ? $u->role : '-') . PHP_EOL;
echo 'has_password=' . ($u && $u->password ? 'yes' : 'no') . PHP_EOL;
echo 'attempt=' . (Auth::attempt(['email' => 'admin@tginvest.com', 'password' => 'password123']) ? 'OK' : 'FAIL') . PHP_EOL;

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Route;

echo "Checking Export Routes...\n";

$routes = collect(Route::getRoutes())->filter(function ($route) {
    return str_contains($route->uri(), 'export');
});

foreach ($routes as $route) {
    echo "URI: " . $route->uri() . " | Methods: " . implode(',', $route->methods()) . " | Middleware: " . implode(',', $route->gatherMiddleware()) . "\n";
}

$user = User::where('role', 'petugas')->first();
if ($user) {
    echo "\nTesting as Petugas: " . $user->email . "\n";
    // We can't easily perform a request here without a full HTTP stack, 
    // but we can check if the middleware would block it.
}

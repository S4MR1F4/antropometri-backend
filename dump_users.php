<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo json_encode(App\Models\User::all(['id', 'name', 'email', 'role'])->toArray());

<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$action = $argv[1] ?? 'get';
$userId = (int) ($argv[2] ?? 1);
$val = $argv[3] ?? '0';

$user = \App\Models\User::find($userId);
if (!$user) {
    echo "USER_NOT_FOUND";
    exit(1);
}

if ($action === 'set') {
    if ($val === '1' || $val === 'now') {
        $user->products_tour_seen_at = now();
    } else {
        $user->products_tour_seen_at = null;
    }
    $user->save();
}

echo $user->products_tour_seen_at ? $user->products_tour_seen_at->toIso8601String() : 'NULL';

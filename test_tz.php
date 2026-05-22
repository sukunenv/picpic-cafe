<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$res = \App\Models\Order::select(\Illuminate\Support\Facades\DB::raw("CONVERT_TZ(created_at, '+00:00', '+07:00') as converted"))->latest()->first();
echo "CONVERTED: " . $res->converted;

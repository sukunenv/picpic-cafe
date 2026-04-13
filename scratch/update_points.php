<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('email', 'member@picpic.com')->first();
if ($user) {
    $user->points = 1600;
    $user->save();
    echo "Points updated to 1600!";
} else {
    echo "User not found!";
}

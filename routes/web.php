<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use Illuminate\Support\Facades\Artisan;

Route::get('/run-migrate', function () {
    if (app()->environment('production')) {
        Artisan::call('migrate', ['--force' => true]);
        return 'Migration done: ' . Artisan::output();
    }
    return 'Not allowed';
});

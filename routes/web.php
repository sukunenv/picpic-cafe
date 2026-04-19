<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// HAPUS SETELAH DIPAKAI!
Route::get('/run-migration-seeder-picpic', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'UpdateCategoryTypesSeeder', '--force' => true]);
    $categories = \Illuminate\Support\Facades\DB::table('categories')->select('id','name','type')->get();
    $output = '';
    foreach ($categories as $c) {
        $output .= $c->name . ' => ' . $c->type . "\n";
    }
    return '<pre>' . $output . '</pre>';
});

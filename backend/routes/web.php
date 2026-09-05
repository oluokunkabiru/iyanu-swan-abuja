<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'frontendUrl' => config('app.frontend_url'),
    ]);
});

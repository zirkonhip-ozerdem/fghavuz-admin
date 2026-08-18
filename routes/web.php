<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/'.trim(env('FILAMENT_PATH', 'admin'), '/'));
});

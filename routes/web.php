<?php

use Illuminate\Support\Facades\Route;

// Panel Filament tarafindan /admin altinda kendi route'larini kaydeder
// (bkz. App\Providers\FilamentAdminPanelProvider). Bu proje API-first oldugu
// icin ayrica bir web arayuzu sunulmuyor.
Route::get('/', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'app' => config('app.name'),
            'admin_panel' => url('/admin'),
            'api' => url('/api/v1'),
        ],
        'message' => null,
    ]);
});

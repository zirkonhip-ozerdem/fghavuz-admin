<?php

return [
    'name' => env('APP_NAME', 'FGPOOL Admin'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'Europe/Istanbul'),

    // Site geneli desteklenen diller. "locale" varsayilan (tr).
    'locale' => env('APP_LOCALE', 'tr'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'tr'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'tr_TR'),

    // App\Support\Traits\HasTranslatableSeo ve API locale middleware'i tarafindan kullanilir.
    'available_locales' => array_filter(explode(',', env('ACTIVE_LOCALES', 'tr,en,ar'))),

    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => array_filter(explode(',', env('APP_PREVIOUS_KEYS', ''))),

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],
];

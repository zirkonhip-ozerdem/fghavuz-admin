<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Next.js frontend origin'leri .env -> CORS_ALLOWED_ORIGINS icinden okunur.
    'allowed_origins' => array_values(array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000'))))),

    // Vercel preview domainleri gibi pattern ihtiyaclari icin.
    'allowed_origins_patterns' => array_values(array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGIN_PATTERNS', ''))))),

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];

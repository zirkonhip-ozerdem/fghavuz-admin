<?php

use Illuminate\Support\Facades\App;

if (! function_exists('active_locales')) {
    /**
     * Sitenin aktif oldugu diller. .env ACTIVE_LOCALES -> config('app.available_locales').
     */
    function active_locales(): array
    {
        return config('app.available_locales', ['tr', 'en', 'ar']);
    }
}

if (! function_exists('current_api_locale')) {
    /**
     * SetApiLocale middleware'i tarafindan set edilen aktif API locale'i.
     * Middleware calismadiysa (ornegin console/test context) config default'una duser.
     */
    function current_api_locale(): string
    {
        $locale = App::getLocale();

        return in_array($locale, active_locales(), true) ? $locale : config('app.locale', 'tr');
    }
}

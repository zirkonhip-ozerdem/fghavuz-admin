<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public API'nin ?locale= query parametresini okuyup uygulama locale'ini set eder.
 * Gecersiz/eksik locale -> varsayilan "tr" (config('app.locale')).
 * Ornek: GET /api/v1/products?locale=en
 */
class SetApiLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $requested = $request->query('locale');
        $allowed = active_locales();

        $locale = in_array($requested, $allowed, true) ? $requested : config('app.locale', 'tr');

        App::setLocale($locale);

        return $next($request);
    }
}

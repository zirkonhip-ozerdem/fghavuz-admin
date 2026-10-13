<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventAdminIndexing
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $panelPath = trim(Filament::getPanel('admin')->getPath(), '/');

        if ($panelPath === '' || $request->is('/', $panelPath, $panelPath.'/*')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}

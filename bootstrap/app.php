<?php

use App\Http\Middleware\SetApiLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();

        $middleware->api(prepend: [
            SetApiLocale::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/v1/contact/messages',
            'api/v1/quote-requests',
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Tum /api/* isteklerinde tutarli JSON zarfi (success/data/message) donsun.
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
            $status = is_int($status) && $status >= 400 && $status < 600 ? $status : 500;

            return response()->json([
                'success' => false,
                'message' => app()->hasDebugModeEnabled() ? $e->getMessage() : match ($status) {
                    404 => 'Kayit bulunamadi.',
                    403 => 'Bu islem icin yetkiniz yok.',
                    401 => 'Kimlik dogrulama gerekli.',
                    429 => 'Cok fazla istek gonderildi. Lutfen daha sonra tekrar deneyin.',
                    default => 'Beklenmeyen bir hata olustu.',
                },
                'errors' => app()->hasDebugModeEnabled() ? [
                    'exception' => get_class($e),
                    'trace' => collect($e->getTrace())->take(5)->toArray(),
                ] : null,
            ], $status);
        });
    })->create();

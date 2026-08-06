<?php

namespace App\Support\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Tum API controller'larinda tutarli { success, data, message } zarfi.
 * Hata zarfi { success:false, message, errors } bootstrap/app.php exception
 * handler'i tarafindan otomatik uretilir (validation / 4xx / 5xx).
 */
trait ApiResponses
{
    protected function success(mixed $data = null, ?string $message = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    protected function created(mixed $data = null, ?string $message = 'Kayit olusturuldu.'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function fail(string $message, int $status = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}

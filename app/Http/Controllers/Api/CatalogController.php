<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CatalogResource;
use App\Models\Catalog;
use App\Support\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CatalogController extends Controller
{
    use ApiResponses;

    /**
     * GET /api/v1/catalogs
     */
    public function index(): JsonResponse
    {
        $catalogs = Catalog::query()->active()->ordered()->get();

        return $this->success(CatalogResource::collection($catalogs));
    }

    /**
     * GET /api/v1/catalogs/{slugOrId}
     */
    public function show(string $slugOrId): JsonResponse
    {
        $catalog = $this->resolve($slugOrId);

        if (! $catalog) {
            return $this->fail('Katalog bulunamadı.', 404);
        }

        return $this->success(new CatalogResource($catalog));
    }

    /**
     * GET /api/v1/catalogs/{slugOrId}/download
     */
    public function download(string $slugOrId): StreamedResponse|JsonResponse
    {
        $catalog = $this->resolve($slugOrId);

        if (! $catalog || ! Storage::disk('public')->exists($catalog->file)) {
            return $this->fail('Katalog dosyası bulunamadı.', 404);
        }

        return Storage::disk('public')->download($catalog->file, $catalog->getTranslation('title', current_api_locale(), false).'.pdf');
    }

    private function resolve(string $slugOrId): ?Catalog
    {
        return Catalog::query()->active()
            ->where('slug', $slugOrId)
            ->orWhere(fn ($q) => is_numeric($slugOrId) ? $q->where('id', $slugOrId) : $q->whereRaw('1=0'))
            ->first();
    }
}

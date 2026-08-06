<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SeoPageResource;
use App\Models\SeoPage;
use App\Support\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

class SeoPageController extends Controller
{
    use ApiResponses;

    /**
     * GET /api/v1/seo/{page_key}?locale=tr
     */
    public function show(string $pageKey): JsonResponse
    {
        $locale = current_api_locale();

        $seoPage = SeoPage::query()
            ->where('page_key', $pageKey)
            ->where('locale', $locale)
            ->first()
            ?? SeoPage::query()->where('page_key', $pageKey)->where('locale', config('app.fallback_locale', 'tr'))->first();

        if (! $seoPage) {
            return $this->fail('SEO kaydı bulunamadı: '.$pageKey, 404);
        }

        return $this->success(new SeoPageResource($seoPage));
    }
}

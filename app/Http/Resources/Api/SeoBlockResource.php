<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * HasSeoFields trait'i kullanan tum modellerde (Corporate, ProductCategory,
 * Product, BlogPost, ...) ortak "seo" bloğunu uretir.
 */
class SeoBlockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = current_api_locale();
        $model = $this->resource;

        $translate = fn (string $field) => method_exists($model, 'getTranslation')
            ? $model->getTranslation($field, $locale, false)
            : $model->{$field};

        return [
            'title' => $translate('seo_title'),
            'description' => $translate('seo_description'),
            'keywords' => $translate('seo_keywords'),
            'canonical_url' => $model->canonical_url,
            'og_title' => $translate('og_title'),
            'og_description' => $translate('og_description'),
            'og_image' => $model->og_image ? asset('storage/'.$model->og_image) : null,
            'robots' => $model->robots,
        ];
    }
}

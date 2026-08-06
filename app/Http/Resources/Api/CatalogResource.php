<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Catalog
 */
class CatalogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = current_api_locale();

        return [
            'id' => $this->id,
            'title' => $this->getTranslation('title', $locale, false),
            'slug' => $this->slug,
            'description' => $this->getTranslation('description', $locale, false),
            'cover_image' => $this->cover_image ? asset('storage/'.$this->cover_image) : null,
            'file_url' => asset('storage/'.$this->file),
            'download_url' => route('api.v1.catalogs.download', $this->slug),
            'file_type' => $this->file_type,
            'file_size' => $this->file_size,
            'seo' => new SeoBlockResource($this->resource),
        ];
    }
}

<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\SeoPage
 */
class SeoPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'page_key' => $this->page_key,
            'locale' => $this->locale,
            'title' => $this->title,
            'description' => $this->description,
            'keywords' => $this->keywords,
            'canonical_url' => $this->canonical_url,
            'og_title' => $this->og_title,
            'og_description' => $this->og_description,
            'og_image' => $this->og_image ? asset('storage/'.$this->og_image) : null,
            'robots' => $this->robots,
            'schema_json' => $this->schema_json,
        ];
    }
}

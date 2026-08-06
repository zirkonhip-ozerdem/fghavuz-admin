<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\BlogCategory
 */
class BlogCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = current_api_locale();

        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', $locale, false),
            'slug' => $this->slug,
            'description' => $this->getTranslation('description', $locale, false),
            'posts_count' => $this->whenCounted('posts'),
            'seo' => new SeoBlockResource($this->resource),
        ];
    }
}

<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\BlogPost
 */
class BlogPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = current_api_locale();
        $isDetail = $request->routeIs('*.blog.posts.show');

        return [
            'id' => $this->id,
            'title' => $this->getTranslation('title', $locale, false),
            'slug' => $this->slug,
            'excerpt' => $this->getTranslation('excerpt', $locale, false),
            'content' => $this->when($isDetail, fn () => $this->getTranslation('content', $locale, false)),
            'cover_image' => $this->cover_image ? asset('storage/'.$this->cover_image) : null,
            'author_name' => $this->author_name,
            'published_at' => $this->published_at?->toIso8601String(),
            'reading_time' => $this->reading_time,
            'is_featured' => (bool) $this->is_featured,
            'category' => new BlogCategoryResource($this->whenLoaded('category')),
            'seo' => new SeoBlockResource($this->resource),
        ];
    }
}

<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\ProductCategory
 */
class ProductCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = current_api_locale();

        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', $locale, false),
            'slug' => $this->slug,
            'description' => $this->getTranslation('description', $locale, false),
            'image' => $this->image ? asset('storage/'.$this->image) : null,
            'icon' => $this->icon,
            'is_featured' => (bool) $this->is_featured,
            'subcategories' => ProductSubcategoryResource::collection($this->whenLoaded('subcategories')),
            'products_count' => $this->whenCounted('products'),
            'seo' => new SeoBlockResource($this->resource),
        ];
    }
}

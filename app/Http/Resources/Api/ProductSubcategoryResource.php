<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\ProductSubcategory
 */
class ProductSubcategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = current_api_locale();

        return [
            'id' => $this->id,
            'product_category_id' => $this->product_category_id,
            'name' => $this->getTranslation('name', $locale, false),
            'slug' => $this->slug,
            'description' => $this->getTranslation('description', $locale, false),
            'image' => $this->image ? asset('storage/'.$this->image) : null,
            'products_count' => $this->whenCounted('products'),
            'seo' => new SeoBlockResource($this->resource),
        ];
    }
}

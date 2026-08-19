<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Product
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = current_api_locale();

        return [
            'id' => $this->id,
            'title' => $this->getTranslation('title', $locale, false),
            'slug' => $this->slug,
            'short_description' => $this->getTranslation('short_description', $locale, false),
            'description' => $this->when($request->routeIs('*.products.show'), fn () => $this->getTranslation('description', $locale, false)),
            'technical_description' => $this->when($request->routeIs('*.products.show'), fn () => $this->getTranslation('technical_description', $locale, false)),
            'series' => $this->series,
            'sku' => $this->sku,
            'cover_image' => $this->cover_image ? asset('storage/'.$this->cover_image) : null,
            'gallery' => $this->when($request->routeIs('*.products.show'), fn () => $this->getMedia('gallery')->map(fn ($media) => [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'thumb' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl(),
                'alt' => $media->getCustomProperty('alt.'.$locale, $media->name),
                'order' => $media->order_column,
            ])),
            'documents' => $this->when($request->routeIs('*.products.show'), fn () => $this->getMedia('documents')->map(fn ($media) => [
                'id' => $media->id,
                'name' => $media->name,
                'url' => $media->getUrl(),
                'size' => $media->size,
                'mime_type' => $media->mime_type,
            ])),
            'features' => $this->when($request->routeIs('*.products.show'), $this->features),
            'technical_specs' => $this->when($request->routeIs('*.products.show'), $this->technical_specs),
            'category' => new ProductCategoryResource($this->whenLoaded('category')),
            'subcategory' => new ProductSubcategoryResource($this->whenLoaded('subcategory')),
            'is_featured' => (bool) $this->is_featured,
            'seo' => new SeoBlockResource($this->resource),
        ];
    }
}

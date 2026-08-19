<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\ReferenceProject
 */
class ReferenceProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = current_api_locale();

        return [
            'id' => $this->id,
            'title' => $this->getTranslation('title', $locale, false),
            'slug' => $this->slug,
            'location' => $this->getTranslation('location', $locale, false),
            'description' => $this->getTranslation('description', $locale, false),
            'image' => $this->image ? asset('storage/'.$this->image) : null,
            'image_alt' => $this->getTranslation('image_alt', $locale, false),
            'is_featured' => (bool) $this->is_featured,
        ];
    }
}

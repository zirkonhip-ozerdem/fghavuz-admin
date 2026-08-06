<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Corporate
 */
class CorporateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = current_api_locale();
        $t = fn (string $field) => $this->getTranslation($field, $locale, false);

        return [
            'title' => $t('title'),
            'subtitle' => $t('subtitle'),
            'description' => $t('description'),
            'story_sections' => $t('story_sections'),
            'mission' => $t('mission'),
            'vision' => $t('vision'),
            'values' => $t('values'),
            'milestones' => $t('milestones'),
            'video_url' => $this->video_url,
            'video_media' => $this->video_media ? asset('storage/'.$this->video_media) : null,
            'image' => $this->image ? asset('storage/'.$this->image) : null,
            'seo' => new SeoBlockResource($this->resource),
        ];
    }
}

<?php

namespace App\Models;

use App\Support\Traits\HasSeoFields;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * Kurumsal sayfa icerigi. Tekil (singleton) kayittir; CorporateResource
 * canCreate() ile ikinci kayit olusturulmasi engellenir.
 */
class Corporate extends Model
{
    use HasSeoFields, HasTranslations;

    protected $fillable = [
        'title', 'subtitle', 'description', 'story_sections', 'mission', 'vision',
        'values', 'milestones', 'video_url', 'video_media', 'image',
        'is_active', 'sort_order',
        'seo_title', 'seo_description', 'seo_keywords', 'canonical_url',
        'og_title', 'og_description', 'og_image', 'robots',
    ];

    public array $translatable = [
        'title', 'subtitle', 'description', 'story_sections', 'mission', 'vision', 'values', 'milestones',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1], [
            'title' => ['tr' => 'Kurumsal', 'en' => 'Corporate', 'ar' => 'الشركة'],
        ]);
    }
}

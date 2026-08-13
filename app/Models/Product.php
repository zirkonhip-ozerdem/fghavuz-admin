<?php

namespace App\Models;

use App\Support\Traits\HasActiveSortable;
use App\Support\Traits\HasCentralizedSlug;
use App\Support\Traits\HasSeoFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Product extends Model implements HasMedia
{
    use HasActiveSortable, HasCentralizedSlug, HasSeoFields, HasTranslations, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'product_category_id', 'product_subcategory_id',
        'title', 'slug', 'short_description', 'description', 'technical_description',
        'series', 'sku', 'cover_image', 'features', 'technical_specs',
        'is_active', 'sort_order', 'is_featured',
        'seo_title', 'seo_description', 'seo_keywords', 'canonical_url',
        'og_title', 'og_description', 'og_image', 'robots',
    ];

    public array $translatable = ['title', 'slug', 'short_description', 'description', 'technical_description'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'features' => 'array',
            'technical_specs' => 'array',
        ];
    }

    public static function slugSourceField(): string
    {
        return 'title';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')
            ->useDisk(config('media-library.disk_name'))
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('documents')
            ->useDisk(config('media-library.disk_name'))
            ->acceptsMimeTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->performOnCollections('gallery')
            ->nonQueued();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(ProductSubcategory::class, 'product_subcategory_id');
    }
}

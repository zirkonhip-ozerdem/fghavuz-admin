<?php

namespace App\Models;

use App\Support\Traits\HasActiveSortable;
use App\Support\Traits\HasCentralizedSlug;
use App\Support\Traits\HasSeoFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class BlogPost extends Model
{
    use HasActiveSortable, HasCentralizedSlug, HasSeoFields, HasTranslations, SoftDeletes;

    protected $fillable = [
        'blog_category_id', 'title', 'slug', 'excerpt', 'content', 'cover_image', 'cover_image_alt',
        'author_name', 'published_at', 'reading_time', 'is_featured', 'is_active', 'sort_order',
        'seo_title', 'seo_description', 'seo_keywords', 'canonical_url',
        'og_title', 'og_description', 'og_image', 'robots',
    ];

    public array $translatable = ['title', 'slug', 'excerpt', 'content', 'cover_image_alt'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public static function slugSourceField(): string
    {
        return 'title';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}

<?php

namespace App\Models;

use App\Support\Traits\HasActiveSortable;
use App\Support\Traits\HasCentralizedSlug;
use App\Support\Traits\HasSeoFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Translatable\HasTranslations;

class ProductCategory extends Model
{
    use HasActiveSortable, HasCentralizedSlug, HasSeoFields, HasSlug, HasTranslations, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'icon',
        'is_active', 'sort_order', 'is_featured',
        'seo_title', 'seo_description', 'seo_keywords', 'canonical_url',
        'og_title', 'og_description', 'og_image', 'robots',
    ];

    public array $translatable = ['name', 'description'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public static function slugSourceField(): string
    {
        return 'name';
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(ProductSubcategory::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

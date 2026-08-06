<?php

namespace App\Models;

use App\Support\Traits\HasActiveSortable;
use App\Support\Traits\HasCentralizedSlug;
use App\Support\Traits\HasSeoFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Translatable\HasTranslations;

class ProductSubcategory extends Model
{
    use HasActiveSortable, HasCentralizedSlug, HasSeoFields, HasSlug, HasTranslations, SoftDeletes;

    protected $fillable = [
        'product_category_id', 'name', 'slug', 'description', 'image',
        'is_active', 'sort_order',
        'seo_title', 'seo_description', 'seo_keywords', 'canonical_url',
        'og_title', 'og_description', 'og_image', 'robots',
    ];

    public array $translatable = ['name', 'description'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public static function slugSourceField(): string
    {
        return 'name';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

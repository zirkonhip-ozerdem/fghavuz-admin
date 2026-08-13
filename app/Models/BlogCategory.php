<?php

namespace App\Models;

use App\Support\Traits\HasActiveSortable;
use App\Support\Traits\HasCentralizedSlug;
use App\Support\Traits\HasSeoFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class BlogCategory extends Model
{
    use HasActiveSortable, HasCentralizedSlug, HasSeoFields, HasTranslations, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'is_active', 'sort_order',
        'seo_title', 'seo_description', 'seo_keywords', 'canonical_url',
        'og_title', 'og_description', 'og_image', 'robots',
    ];

    public array $translatable = ['name', 'slug', 'description'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public static function slugSourceField(): string
    {
        return 'name';
    }

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }
}

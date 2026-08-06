<?php

namespace App\Models;

use App\Support\Traits\HasActiveSortable;
use App\Support\Traits\HasCentralizedSlug;
use App\Support\Traits\HasSeoFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Translatable\HasTranslations;

class Catalog extends Model
{
    use HasActiveSortable, HasCentralizedSlug, HasSeoFields, HasSlug, HasTranslations, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'description', 'file', 'cover_image', 'file_type', 'file_size',
        'is_active', 'sort_order',
        'seo_title', 'seo_description', 'seo_keywords', 'canonical_url',
        'og_title', 'og_description', 'og_image', 'robots',
    ];

    public array $translatable = ['title', 'description'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public static function slugSourceField(): string
    {
        return 'title';
    }

    protected static function booted(): void
    {
        // file_type / file_size dosya her degistiginde otomatik hesaplanir,
        // panelde elle girilmesine gerek kalmaz.
        static::saving(function (self $catalog) {
            if ($catalog->isDirty('file') && $catalog->file && Storage::disk('public')->exists($catalog->file)) {
                $catalog->file_size = Storage::disk('public')->size($catalog->file);
                $catalog->file_type = Storage::disk('public')->mimeType($catalog->file) ?: pathinfo($catalog->file, PATHINFO_EXTENSION);
            }
        });
    }

    public function downloadUrl(): string
    {
        return route('api.v1.catalogs.download', $this->slug);
    }
}

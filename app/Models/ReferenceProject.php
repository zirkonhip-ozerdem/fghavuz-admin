<?php

namespace App\Models;

use App\Support\Traits\HasActiveSortable;
use App\Support\Traits\HasCentralizedSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Translatable\HasTranslations;

class ReferenceProject extends Model
{
    use HasActiveSortable, HasCentralizedSlug, HasSlug, HasTranslations, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'location', 'description', 'image', 'is_active', 'sort_order', 'is_featured',
    ];

    public array $translatable = ['title', 'location', 'description'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public static function slugSourceField(): string
    {
        return 'title';
    }
}

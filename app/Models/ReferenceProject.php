<?php

namespace App\Models;

use App\Support\Traits\HasActiveSortable;
use App\Support\Traits\HasCentralizedSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ReferenceProject extends Model
{
    use HasActiveSortable, HasCentralizedSlug, HasTranslations, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'location', 'description', 'image', 'image_alt', 'is_active', 'sort_order', 'is_featured',
    ];

    public array $translatable = ['title', 'slug', 'location', 'description', 'image_alt'];

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

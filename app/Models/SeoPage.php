<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoPage extends Model
{
    public const PAGE_KEYS = [
        'home', 'corporate', 'products', 'catalog', 'blog', 'contact', 'quote', 'references',
    ];

    protected $fillable = [
        'page_key',
        'locale',
        'title',
        'description',
        'keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'robots',
        'schema_json',
    ];

    protected function casts(): array
    {
        return [
            'schema_json' => 'array',
        ];
    }
}

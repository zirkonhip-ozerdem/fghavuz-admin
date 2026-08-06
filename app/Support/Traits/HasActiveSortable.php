<?php

namespace App\Support\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * is_active + sort_order alanlarina sahip modellerde ortak query scope'lari.
 * Migration: $table->publishable(); (bkz. AppServiceProvider Blueprint macro)
 */
trait HasActiveSortable
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
}

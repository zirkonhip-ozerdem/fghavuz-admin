<?php

namespace App\Support\Traits;

use Spatie\Sluggable\SlugOptions;

/**
 * Slug uretimini merkezilestirir: hangi (translatable) alandan uretilecegini
 * modelin static::slugSourceField() metoduyla belirtiriz, kalanini
 * spatie/laravel-sluggable halleder. Kaynak alan translatable oldugundan
 * kayit sirasinda aktif locale'in (varsayilan tr) degeri kullanilir.
 */
trait HasCentralizedSlug
{
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function ($model) {
                $field = $model::slugSourceField();
                $value = $model->getTranslation($field, 'tr', false) ?: $model->getTranslation($field, app()->getLocale(), false);

                return $value ?: $model->{$field};
            })
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate()
            ->usingSeparator('-')
            ->slugsShouldBeNoLongerThan(180);
    }

    public static function slugSourceField(): string
    {
        return 'title';
    }
}

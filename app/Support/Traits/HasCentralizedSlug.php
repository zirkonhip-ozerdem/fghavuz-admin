<?php

namespace App\Support\Traits;

/**
 * Slug artik dil bazli (translatable) bir alandir; her dilin slug'i o dilin
 * kendi formundan (istemci tarafinda, canli sunucu istegi olmadan) uretilir.
 * Bu trait sadece hangi (translatable) alandan slug uretilecegini belirtir
 * (bkz. HasTranslatableTabs: packTranslatableFields sunucu tarafi yedek uretim,
 * slugGeneratorAttributes istemci tarafi uretim).
 */
trait HasCentralizedSlug
{
    public static function slugSourceField(): string
    {
        return 'title';
    }
}

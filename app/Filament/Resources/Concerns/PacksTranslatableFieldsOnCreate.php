<?php

namespace App\Filament\Resources\Concerns;

/**
 * CreateRecord sayfalarinda kullanilir. Kaynak Resource'un HasTranslatableTabs
 * trait'i ile sagladigi packTranslatableFields() metodunu kayittan once cagirir.
 */
trait PacksTranslatableFieldsOnCreate
{
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return static::getResource()::packTranslatableFields($data);
    }
}

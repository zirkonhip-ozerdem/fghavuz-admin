<?php

namespace App\Filament\Resources\Concerns;

/**
 * EditRecord sayfalarinda kullanilir. Kaynak Resource'un HasTranslatableTabs
 * trait'i ile sagladigi pack/unpackTranslatableFields() metotlarini formu
 * doldururken ve kaydederken cagirir.
 */
trait PacksTranslatableFieldsOnEdit
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return static::getResource()::unpackTranslatableFields($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return static::getResource()::packTranslatableFields($data);
    }
}

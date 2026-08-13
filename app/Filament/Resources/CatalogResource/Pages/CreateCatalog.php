<?php

namespace App\Filament\Resources\CatalogResource\Pages;

use App\Filament\Resources\CatalogResource;
use App\Filament\Resources\Concerns\PacksTranslatableFieldsOnCreate;
use Filament\Resources\Pages\CreateRecord;

class CreateCatalog extends CreateRecord
{
    use PacksTranslatableFieldsOnCreate;

    protected static string $resource = CatalogResource::class;
}

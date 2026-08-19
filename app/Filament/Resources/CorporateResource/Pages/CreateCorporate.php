<?php

namespace App\Filament\Resources\CorporateResource\Pages;

use App\Filament\Resources\Concerns\PacksTranslatableFieldsOnCreate;
use App\Filament\Resources\CorporateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCorporate extends CreateRecord
{
    use PacksTranslatableFieldsOnCreate;

    protected static string $resource = CorporateResource::class;
}

<?php

namespace App\Filament\Resources\CorporateResource\Pages;

use App\Filament\Resources\Concerns\PacksTranslatableFieldsOnEdit;
use App\Filament\Resources\CorporateResource;
use Filament\Resources\Pages\EditRecord;

class EditCorporate extends EditRecord
{
    use PacksTranslatableFieldsOnEdit;

    protected static string $resource = CorporateResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

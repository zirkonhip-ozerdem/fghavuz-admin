<?php

namespace App\Filament\Resources\CatalogResource\Pages;

use App\Filament\Resources\CatalogResource;
use App\Filament\Resources\Concerns\PacksTranslatableFieldsOnEdit;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCatalog extends EditRecord
{
    use PacksTranslatableFieldsOnEdit;

    protected static string $resource = CatalogResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}

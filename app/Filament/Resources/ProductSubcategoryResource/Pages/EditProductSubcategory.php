<?php

namespace App\Filament\Resources\ProductSubcategoryResource\Pages;

use App\Filament\Resources\Concerns\PacksTranslatableFieldsOnEdit;
use App\Filament\Resources\ProductSubcategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductSubcategory extends EditRecord
{
    use PacksTranslatableFieldsOnEdit;

    protected static string $resource = ProductSubcategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}

<?php

namespace App\Filament\Resources\ProductCategoryResource\Pages;

use App\Filament\Resources\Concerns\PacksTranslatableFieldsOnEdit;
use App\Filament\Resources\ProductCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductCategory extends EditRecord
{
    use PacksTranslatableFieldsOnEdit;

    protected static string $resource = ProductCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}

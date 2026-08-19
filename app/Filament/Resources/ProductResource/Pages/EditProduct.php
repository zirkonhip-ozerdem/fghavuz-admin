<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\Concerns\PacksTranslatableFieldsOnEdit;
use App\Filament\Resources\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    use PacksTranslatableFieldsOnEdit;

    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}

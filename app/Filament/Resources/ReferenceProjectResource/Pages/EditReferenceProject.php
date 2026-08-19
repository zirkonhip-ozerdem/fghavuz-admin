<?php

namespace App\Filament\Resources\ReferenceProjectResource\Pages;

use App\Filament\Resources\Concerns\PacksTranslatableFieldsOnEdit;
use App\Filament\Resources\ReferenceProjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReferenceProject extends EditRecord
{
    use PacksTranslatableFieldsOnEdit;

    protected static string $resource = ReferenceProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}

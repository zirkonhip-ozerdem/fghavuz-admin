<?php

namespace App\Filament\Resources\ReferenceProjectResource\Pages;

use App\Filament\Resources\ReferenceProjectResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;

class EditReferenceProject extends EditRecord
{
    use Translatable;

    protected static string $resource = ReferenceProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [LocaleSwitcher::make(), DeleteAction::make()];
    }
}

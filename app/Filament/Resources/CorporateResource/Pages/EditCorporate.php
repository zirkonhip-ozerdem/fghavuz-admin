<?php

namespace App\Filament\Resources\CorporateResource\Pages;

use App\Filament\Resources\CorporateResource;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;

class EditCorporate extends EditRecord
{
    use Translatable;

    protected static string $resource = CorporateResource::class;

    protected function getHeaderActions(): array
    {
        return [LocaleSwitcher::make()];
    }
}

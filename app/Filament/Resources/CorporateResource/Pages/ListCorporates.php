<?php

namespace App\Filament\Resources\CorporateResource\Pages;

use App\Filament\Resources\CorporateResource;
use Filament\Actions\CreateAction;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;

class ListCorporates extends ListRecords
{
    use Translatable;

    protected static string $resource = CorporateResource::class;

    protected function getHeaderActions(): array
    {
        return [LocaleSwitcher::make(), CreateAction::make()];
    }
}

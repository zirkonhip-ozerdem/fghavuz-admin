<?php

namespace App\Filament\Resources\CorporateResource\Pages;

use App\Filament\Resources\CorporateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCorporates extends ListRecords
{
    protected static string $resource = CorporateResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

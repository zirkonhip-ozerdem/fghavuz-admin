<?php

namespace App\Filament\Resources\ReferenceProjectResource\Pages;

use App\Filament\Resources\ReferenceProjectResource;
use Filament\Actions\CreateAction;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;

class ListReferenceProjects extends ListRecords
{
    use Translatable;

    protected static string $resource = ReferenceProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [LocaleSwitcher::make(), CreateAction::make()];
    }
}

<?php

namespace App\Filament\Resources\ReferenceProjectResource\Pages;

use App\Filament\Resources\Concerns\PacksTranslatableFieldsOnCreate;
use App\Filament\Resources\ReferenceProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReferenceProject extends CreateRecord
{
    use PacksTranslatableFieldsOnCreate;

    protected static string $resource = ReferenceProjectResource::class;
}

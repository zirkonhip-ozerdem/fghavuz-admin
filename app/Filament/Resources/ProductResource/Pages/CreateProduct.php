<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\Concerns\PacksTranslatableFieldsOnCreate;
use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    use PacksTranslatableFieldsOnCreate;

    protected static string $resource = ProductResource::class;
}

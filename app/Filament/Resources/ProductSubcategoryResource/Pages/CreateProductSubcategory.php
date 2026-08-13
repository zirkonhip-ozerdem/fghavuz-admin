<?php

namespace App\Filament\Resources\ProductSubcategoryResource\Pages;

use App\Filament\Resources\Concerns\PacksTranslatableFieldsOnCreate;
use App\Filament\Resources\ProductSubcategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductSubcategory extends CreateRecord
{
    use PacksTranslatableFieldsOnCreate;

    protected static string $resource = ProductSubcategoryResource::class;
}

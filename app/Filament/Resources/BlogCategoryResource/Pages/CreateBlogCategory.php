<?php

namespace App\Filament\Resources\BlogCategoryResource\Pages;

use App\Filament\Resources\BlogCategoryResource;
use App\Filament\Resources\Concerns\PacksTranslatableFieldsOnCreate;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogCategory extends CreateRecord
{
    use PacksTranslatableFieldsOnCreate;

    protected static string $resource = BlogCategoryResource::class;
}

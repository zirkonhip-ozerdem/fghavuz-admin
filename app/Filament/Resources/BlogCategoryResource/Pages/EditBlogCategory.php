<?php

namespace App\Filament\Resources\BlogCategoryResource\Pages;

use App\Filament\Resources\BlogCategoryResource;
use App\Filament\Resources\Concerns\PacksTranslatableFieldsOnEdit;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBlogCategory extends EditRecord
{
    use PacksTranslatableFieldsOnEdit;

    protected static string $resource = BlogCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}

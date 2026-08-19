<?php

namespace App\Filament\Resources\BlogPostResource\Pages;

use App\Filament\Resources\BlogPostResource;
use App\Filament\Resources\Concerns\PacksTranslatableFieldsOnCreate;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogPost extends CreateRecord
{
    use PacksTranslatableFieldsOnCreate;

    protected static string $resource = BlogPostResource::class;
}

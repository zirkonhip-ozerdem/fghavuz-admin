<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogCategoryResource\Pages;
use App\Models\BlogCategory;
use App\Support\Permissions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BlogCategoryResource extends Resource
{
    use Translatable;

    protected static ?string $model = BlogCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?string $navigationLabel = 'Blog Kategorileri';

    protected static ?string $modelLabel = 'Blog Kategorisi';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('İçerik')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->label('Ad')->required()->maxLength(150)->columnSpanFull(),
                    TextInput::make('slug')->label('Slug')->maxLength(180)->unique(ignoreRecord: true),
                    Textarea::make('description')->label('Açıklama')->rows(3)->columnSpanFull(),
                ]),
            Section::make('Yayın')
                ->columns(2)
                ->schema([
                    Toggle::make('is_active')->label('Aktif')->default(true),
                    TextInput::make('sort_order')->label('Sıra')->numeric()->default(1),
                ]),
            ...BlogCategory::seoFormSchema(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Ad')->searchable()->sortable(),
                TextColumn::make('posts_count')->label('Yazı')->counts('posts'),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([TernaryFilter::make('is_active')->label('Aktif mi?')]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogCategories::route('/'),
            'create' => Pages\CreateBlogCategory::route('/create'),
            'edit' => Pages\EditBlogCategory::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can(Permissions::MANAGE_BLOG) ?? false;
    }
}

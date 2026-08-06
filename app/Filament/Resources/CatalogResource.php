<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CatalogResource\Pages;
use App\Models\Catalog;
use App\Support\Permissions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CatalogResource extends Resource
{
    use Translatable;

    protected static ?string $model = Catalog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?string $navigationLabel = 'Kataloglar';

    protected static ?string $modelLabel = 'Katalog';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('İçerik')
                ->columns(2)
                ->schema([
                    TextInput::make('title')->label('Başlık')->required()->maxLength(180)->columnSpanFull(),
                    TextInput::make('slug')->label('Slug')->maxLength(200)->unique(ignoreRecord: true),
                    Textarea::make('description')->label('Açıklama')->rows(3)->columnSpanFull(),
                    FileUpload::make('file')
                        ->label('Katalog Dosyası (PDF)')
                        ->directory('catalogs')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize((int) env('MEDIA_MAX_DOCUMENT_SIZE', 20480))
                        ->required()
                        ->columnSpanFull(),
                    FileUpload::make('cover_image')->label('Kapak Görseli')->image()->directory('catalogs/cover')->columnSpanFull(),
                ]),
            Section::make('Yayın')
                ->columns(2)
                ->schema([
                    Toggle::make('is_active')->label('Aktif')->default(true),
                    TextInput::make('sort_order')->label('Sıra')->numeric()->default(0),
                ]),
            ...Catalog::seoFormSchema(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')->label('Kapak')->square(),
                TextColumn::make('title')->label('Başlık')->searchable()->sortable(),
                TextColumn::make('file_type')->label('Tür'),
                TextColumn::make('file_size')->label('Boyut')->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 0).' KB' : '-'),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([TernaryFilter::make('is_active')->label('Aktif mi?')]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCatalogs::route('/'),
            'create' => Pages\CreateCatalog::route('/create'),
            'edit' => Pages\EditCatalog::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can(Permissions::MANAGE_CATALOGS) ?? false;
    }
}

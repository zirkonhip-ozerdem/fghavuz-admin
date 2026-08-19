<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CatalogResource\Pages;
use App\Models\Catalog;
use App\Support\Permissions;
use App\Support\Traits\HasTranslatableTabs;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CatalogResource extends Resource
{
    use HasTranslatableTabs;

    protected static ?string $model = Catalog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?string $navigationLabel = 'Kataloglar';

    protected static ?string $modelLabel = 'Katalog';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Dil Bazlı İçerik')
                ->schema([
                    Tabs::make('Diller')
                        ->tabs(
                            collect(self::locales())
                                ->map(fn (string $label, string $locale) => Tab::make($locale)
                                    ->label($label)
                                    ->schema([
                                        TextInput::make("title_{$locale}")
                                            ->label('Başlık')
                                            ->required()
                                            ->maxLength(180)
                                            ->extraInputAttributes(fn (string $operation, ?\Illuminate\Database\Eloquent\Model $record) => static::slugGeneratorAttributes($locale, $operation, $record)),
                                        TextInput::make("slug_{$locale}")
                                            ->label('Slug')
                                            ->maxLength(200)
                                            ->unique(table: Catalog::class, column: "slug->{$locale}", ignoreRecord: true)
                                            ->helperText('Otomatik doldurulur, istenirse elle değiştirilebilir.'),
                                        RichEditor::make("description_{$locale}")->label('Açıklama'),
                                        Section::make('SEO')
                                            ->collapsible()
                                            ->collapsed()
                                            ->schema(Catalog::translatableSeoFormSchema($locale)),
                                    ]))
                                ->values()
                                ->all()
                        ),
                ]),
            Section::make('Ortak Bilgiler')
                ->columns(2)
                ->schema([
                    FileUpload::make('file')
                        ->label('Katalog Dosyası (PDF)')
                        ->directory('catalogs')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize((int) env('MEDIA_MAX_DOCUMENT_SIZE', 20480))
                        ->required()
                        ->columnSpanFull()
                        ->helperText('Sadece PDF formatında. Maksimum dosya boyutu: 20 MB.'),
                ]),
            Section::make('Yayın')
                ->columns(2)
                ->schema([
                    Toggle::make('is_active')->label('Aktif')->default(true),
                    TextInput::make('sort_order')->label('Sıra')->numeric()->default(1),
                ]),
            Section::make('SEO - Ortak Alanlar')
                ->description('Dile bağlı olmayan SEO ayarları.')
                ->collapsible()
                ->collapsed()
                ->columns(2)
                ->schema(Catalog::nonTranslatableSeoFormSchema()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Başlık')->searchable()->sortable(),
                TextColumn::make('file_type')->label('Tür'),
                TextColumn::make('file_size')->label('Boyut')->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 0).' KB' : '-'),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([TernaryFilter::make('is_active')->label('Aktif mi?')]);
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

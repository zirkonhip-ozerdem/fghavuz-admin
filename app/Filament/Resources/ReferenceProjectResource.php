<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferenceProjectResource\Pages;
use App\Models\ReferenceProject;
use App\Support\Permissions;
use App\Support\Traits\HasTranslatableTabs;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ReferenceProjectResource extends Resource
{
    use HasTranslatableTabs;

    protected static ?string $model = ReferenceProject::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?string $navigationLabel = 'Referans Projeler';

    protected static ?string $modelLabel = 'Referans Proje';

    protected static ?int $navigationSort = 4;

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
                                            ->unique(table: ReferenceProject::class, column: "slug->{$locale}", ignoreRecord: true)
                                            ->helperText('Otomatik doldurulur, istenirse elle değiştirilebilir.'),
                                        TextInput::make("location_{$locale}")->label('Konum'),
                                        Textarea::make("description_{$locale}")->label('Açıklama')->rows(4),
                                    ]))
                                ->values()
                                ->all()
                        ),
                ]),
            Section::make('Ortak Bilgiler')
                ->columns(2)
                ->schema([
                    FileUpload::make('image')->label('Görsel')->directory('references')->columnSpanFull()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize((int) env('MEDIA_MAX_IMAGE_SIZE', 5120))
                        ->helperText('JPG, PNG veya WEBP yükleyin. Maksimum dosya boyutu: 5 MB.')
                        ->live()
                        ->afterStateUpdated(static::imageAltAutoFillCallback('image')),
                    Grid::make(3)
                        ->columnSpanFull()
                        ->schema(static::imageAltFields('image')),
                ]),
            Section::make('Yayın')
                ->columns(3)
                ->schema([
                    Toggle::make('is_active')->label('Aktif')->default(true),
                    Toggle::make('is_featured')->label('Öne Çıkan'),
                    TextInput::make('sort_order')->label('Sıra')->numeric()->default(1),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->label('Görsel')->square(),
                TextColumn::make('title')->label('Başlık')->searchable()->sortable(),
                TextColumn::make('location')->label('Konum'),
                IconColumn::make('is_featured')->label('Öne Çıkan')->boolean(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                TernaryFilter::make('is_active')->label('Aktif mi?'),
                TernaryFilter::make('is_featured')->label('Öne Çıkan mı?'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReferenceProjects::route('/'),
            'create' => Pages\CreateReferenceProject::route('/create'),
            'edit' => Pages\EditReferenceProject::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can(Permissions::MANAGE_REFERENCES) ?? false;
    }
}

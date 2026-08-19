<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductCategoryResource\Pages;
use App\Models\ProductCategory;
use App\Support\Permissions;
use App\Support\Traits\HasTranslatableTabs;
use Filament\Forms\Components\FileUpload;
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
use Illuminate\Support\Facades\Storage;

class ProductCategoryResource extends Resource
{
    use HasTranslatableTabs;

    protected static ?string $model = ProductCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Ürün Kataloğu';

    protected static ?string $navigationLabel = 'Ürün Kategorileri';

    protected static ?string $modelLabel = 'Ürün Kategorisi';

    protected static ?int $navigationSort = 1;

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
                                        TextInput::make("name_{$locale}")
                                            ->label('Ad')
                                            ->required()
                                            ->maxLength(150)
                                            ->extraInputAttributes(fn (string $operation) => static::slugGeneratorAttributes($locale, $operation)),
                                        TextInput::make("slug_{$locale}")
                                            ->label('Slug')
                                            ->maxLength(180)
                                            ->unique(table: ProductCategory::class, column: "slug->{$locale}", ignoreRecord: true)
                                            ->helperText('Otomatik doldurulur, istenirse elle değiştirilebilir.'),
                                        Textarea::make("description_{$locale}")->label('Açıklama')->rows(4),
                                        Section::make('SEO')
                                            ->collapsible()
                                            ->collapsed()
                                            ->schema(ProductCategory::translatableSeoFormSchema($locale)),
                                    ]))
                                ->values()
                                ->all()
                        ),
                ]),
            Section::make('Ortak Bilgiler')
                ->columns(2)
                ->schema([
                    TextInput::make('icon')->label('İkon (opsiyonel)'),
                    FileUpload::make('image')->label('Görsel')->disk('public')->visibility('public')->directory('product-categories')->columnSpanFull()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->fetchFileInformation(false)
                        ->deletable()
                        ->openable()
                        ->downloadable()
                        ->previewable()
                        ->imagePreviewHeight('180')
                        ->panelLayout('grid')
                        ->deleteUploadedFileUsing(fn (string $file) => Storage::disk('public')->delete($file))
                        ->getUploadedFileUsing(static function (string $file): ?array {
                            $disk = Storage::disk('public');
                            $exists = $disk->exists($file);

                            return [
                                'name' => basename($file),
                                'size' => $exists ? $disk->size($file) : 0,
                                'type' => $exists ? $disk->mimeType($file) : 'image/svg+xml',
                                'url' => $exists ? static::storagePreviewUrl($file) : static::missingImagePreviewDataUri(),
                            ];
                        })
                        ->maxSize((int) env('MEDIA_MAX_IMAGE_SIZE', 5120))
                        ->helperText('JPG, PNG veya WEBP yükleyin. Maksimum dosya boyutu: 5 MB.'),
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
                ->schema(ProductCategory::nonTranslatableSeoFormSchema()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Görsel')
                    ->disk('public')
                    ->getStateUsing(fn (ProductCategory $record): ?string => $record->image && Storage::disk('public')->exists($record->image)
                        ? $record->image
                        : null)
                    ->defaultImageUrl(static::missingImagePreviewDataUri())
                    ->square(),
                TextColumn::make('name')->label('Ad')->searchable()->sortable(),
                TextColumn::make('subcategories_count')->label('Alt Kategori')->counts('subcategories'),
                TextColumn::make('products_count')->label('Ürün')->counts('products'),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                TernaryFilter::make('is_active')->label('Aktif mi?'),
            ])
            ->reorderable('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductCategories::route('/'),
            'create' => Pages\CreateProductCategory::route('/create'),
            'edit' => Pages\EditProductCategory::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can(Permissions::MANAGE_PRODUCTS) ?? false;
    }

    protected static function missingImagePreviewDataUri(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="320" height="320" viewBox="0 0 320 320"><rect width="320" height="320" fill="#f3f4f6"/><path d="M112 106h96a18 18 0 0 1 18 18v72a18 18 0 0 1-18 18h-96a18 18 0 0 1-18-18v-72a18 18 0 0 1 18-18zm12 84h72l-22-28-18 22-13-15-19 21z" fill="#9ca3af"/><text x="160" y="246" text-anchor="middle" font-family="Arial, sans-serif" font-size="18" fill="#6b7280">Dosya bulunamadi</text></svg>';

        return 'data:image/svg+xml;utf8,'.rawurlencode($svg);
    }

    protected static function storagePreviewUrl(string $path): string
    {
        return Storage::disk('public')->url(ltrim($path, '/'));
    }
}

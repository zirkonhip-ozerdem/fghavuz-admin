<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use App\Support\Permissions;
use App\Support\Traits\HasTranslatableTabs;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductResource extends Resource
{
    use HasTranslatableTabs;

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Ürün Kataloğu';

    protected static ?string $navigationLabel = 'Ürünler';

    protected static ?string $modelLabel = 'Ürün';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Kategori')
                ->columns(2)
                ->schema([
                    Select::make('product_category_id')
                        ->label('Ana Kategori')
                        ->options(fn () => ProductCategory::query()->ordered()->get()->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn ($set) => $set('product_subcategory_id', null)),
                    Select::make('product_subcategory_id')
                        ->label('Alt Kategori')
                        ->options(function ($get) {
                            $categoryId = $get('product_category_id');

                            return $categoryId
                                ? ProductSubcategory::query()->where('product_category_id', $categoryId)->ordered()->get()->pluck('name', 'id')
                                : [];
                        })
                        ->searchable(),
                ]),
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
                                            ->extraInputAttributes(fn (string $operation) => static::slugGeneratorAttributes($locale, $operation)),
                                        TextInput::make("slug_{$locale}")
                                            ->label('Slug')
                                            ->maxLength(200)
                                            ->unique(table: Product::class, column: "slug->{$locale}", ignoreRecord: true)
                                            ->helperText('Otomatik doldurulur, istenirse elle değiştirilebilir.'),
                                        Textarea::make("short_description_{$locale}")->label('Kısa Açıklama')->rows(2),
                                        Textarea::make("description_{$locale}")->label('Açıklama')->rows(5),
                                        Textarea::make("technical_description_{$locale}")->label('Teknik Açıklama')->rows(5),
                                        Section::make('SEO')
                                            ->collapsible()
                                            ->collapsed()
                                            ->schema(Product::translatableSeoFormSchema($locale)),
                                    ]))
                                ->values()
                                ->all()
                        ),
                ]),
            Section::make('Ortak Bilgiler')
                ->columns(2)
                ->schema([
                    TextInput::make('series')->label('Seri'),
                    TextInput::make('sku')->label('SKU / Stok Kodu'),
                    FileUpload::make('cover_image')->label('Kapak Görseli')->disk('public')->visibility('public')->directory('products/cover')->columnSpanFull()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->fetchFileInformation(false)
                        ->deletable()
                        ->openable()
                        ->downloadable()
                        ->previewable()
                        ->imagePreviewHeight('180')
                        ->panelLayout('grid')
                        ->deleteUploadedFileUsing(fn (string $file) => Storage::disk('public')->delete($file))
                        ->maxSize((int) env('MEDIA_MAX_IMAGE_SIZE', 5120))
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
                        ->helperText('JPG, PNG veya WEBP yükleyin. Ürün listelerinde ve kartlarda görünecek ana görsel. Önerilen maksimum dosya boyutu: 5 MB.'),
                ]),
            Section::make('Galeri ve Dokümanlar')
                ->columns(1)
                ->schema([
                    SpatieMediaLibraryFileUpload::make('gallery')
                        ->label('Galeri Görselleri')
                        ->collection('gallery')
                        ->multiple()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->reorderable()
                        ->deletable()
                        ->openable()
                        ->downloadable()
                        ->previewable()
                        ->imagePreviewHeight('180')
                        ->panelLayout('grid')
                        ->appendFiles()
                        ->getUploadedFileUsing(static function (SpatieMediaLibraryFileUpload $component, string $file): ?array {
                            if (! $record = $component->getRecord()) {
                                return null;
                            }

                            /** @var ?Media $media */
                            $media = $record->load('media')->getRelationValue('media')->firstWhere('uuid', $file);

                            if (! $media) {
                                return [
                                    'name' => 'Eksik galeri gorseli',
                                    'size' => 0,
                                    'type' => 'image/svg+xml',
                                    'url' => static::missingImagePreviewDataUri(),
                                ];
                            }

                            $exists = Storage::disk($media->disk)->exists($media->getPathRelativeToRoot());

                            return [
                                'name' => $media->getAttributeValue('name') ?? $media->getAttributeValue('file_name'),
                                'size' => $media->getAttributeValue('size') ?? 0,
                                'type' => $exists ? $media->getAttributeValue('mime_type') : 'image/svg+xml',
                                'url' => $exists ? static::storagePreviewUrl($media->getPathRelativeToRoot()) : static::missingImagePreviewDataUri(),
                            ];
                        })
                        ->helperText('Birden fazla görsel seçebilirsiniz (JPG, PNG, WEBP). Sürükleyerek sıralayabilirsiniz. Önerilen maksimum dosya boyutu: 5 MB.'),
                    SpatieMediaLibraryFileUpload::make('documents')
                        ->label('Dokümanlar (PDF, DOC, XLS)')
                        ->collection('documents')
                        ->multiple()
                        ->acceptedFileTypes([
                            'application/pdf', 'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->previewable(false)
                        ->openable()
                        ->downloadable()
                        ->deletable()
                        ->appendFiles()
                        ->helperText('PDF, Word veya Excel dosyası yükleyebilirsiniz (birden fazla seçilebilir). Önerilen maksimum dosya boyutu: 20 MB.'),
                ]),
            Section::make('Özellikler ve Teknik Değerler')
                ->schema([
                    Repeater::make('features')
                        ->label('Özellikler')
                        ->simple(TextInput::make('value')->label('Özellik')->required())
                        ->addActionLabel('Özellik Ekle')
                        ->columnSpanFull(),
                    KeyValue::make('technical_specs')
                        ->label('Teknik Özellikler')
                        ->keyLabel('Özellik')
                        ->valueLabel('Değer')
                        ->columnSpanFull(),
                ]),
            Section::make('Yayın')
                ->columns(3)
                ->schema([
                    Toggle::make('is_active')->label('Aktif')->default(true),
                    Toggle::make('is_featured')->label('Öne Çıkan'),
                    TextInput::make('sort_order')->label('Sıra')->numeric()->default(1),
                ]),
            Section::make('SEO - Ortak Alanlar')
                ->description('Dile bağlı olmayan SEO ayarları.')
                ->collapsible()
                ->collapsed()
                ->columns(2)
                ->schema(Product::nonTranslatableSeoFormSchema()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('Görsel')
                    ->disk('public')
                    ->getStateUsing(fn (Product $record): ?string => $record->cover_image && Storage::disk('public')->exists($record->cover_image)
                        ? $record->cover_image
                        : null)
                    ->defaultImageUrl(static::missingImagePreviewDataUri())
                    ->square(),
                TextColumn::make('title')->label('Başlık')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Kategori')->sortable(),
                TextColumn::make('subcategory.name')->label('Alt Kategori'),
                TextColumn::make('sku')->label('SKU'),
                IconColumn::make('is_featured')->label('Öne Çıkan')->boolean(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('product_category_id')->label('Kategori')
                    ->options(fn () => ProductCategory::query()->ordered()->get()->pluck('name', 'id')),
                TernaryFilter::make('is_active')->label('Aktif mi?'),
                TernaryFilter::make('is_featured')->label('Öne Çıkan mı?'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can(Permissions::MANAGE_PRODUCTS) ?? false;
    }

    protected static function missingImagePreviewDataUri(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="640" height="360" viewBox="0 0 640 360"><rect width="640" height="360" fill="#f3f4f6"/><path d="M272 126h96a24 24 0 0 1 24 24v60a24 24 0 0 1-24 24h-96a24 24 0 0 1-24-24v-60a24 24 0 0 1 24-24zm14 84h68l-20-26-16 20-12-14-20 20z" fill="#9ca3af"/><text x="320" y="270" text-anchor="middle" font-family="Arial, sans-serif" font-size="22" fill="#6b7280">Dosya bulunamadi</text></svg>';

        return 'data:image/svg+xml;utf8,'.rawurlencode($svg);
    }

    protected static function storagePreviewUrl(string $path): string
    {
        return Storage::disk('public')->url(ltrim($path, '/'));
    }
}

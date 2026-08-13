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
                    FileUpload::make('cover_image')->label('Kapak Görseli')->image()->directory('products/cover')->columnSpanFull()
                        ->maxSize((int) env('MEDIA_MAX_IMAGE_SIZE', 5120))
                        ->helperText('JPG, PNG veya WEBP yükleyin. Ürün listelerinde ve kartlarda görünecek ana görsel. Maksimum dosya boyutu: 5 MB.'),
                ]),
            Section::make('Galeri ve Dokümanlar')
                ->columns(1)
                ->schema([
                    SpatieMediaLibraryFileUpload::make('gallery')
                        ->label('Galeri Görselleri')
                        ->collection('gallery')
                        ->multiple()
                        ->image()
                        ->reorderable()
                        ->appendFiles()
                        ->maxSize((int) env('MEDIA_MAX_IMAGE_SIZE', 5120))
                        ->helperText('Birden fazla görsel seçebilirsiniz (JPG, PNG, WEBP). Sürükleyerek sıralayabilirsiniz. Her dosya en fazla 5 MB.'),
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
                        ->appendFiles()
                        ->maxSize((int) env('MEDIA_MAX_DOCUMENT_SIZE', 20480))
                        ->helperText('PDF, Word veya Excel dosyası yükleyebilirsiniz (birden fazla seçilebilir). Her dosya en fazla 20 MB.'),
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
                ImageColumn::make('cover_image')->label('Görsel')->square(),
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
}

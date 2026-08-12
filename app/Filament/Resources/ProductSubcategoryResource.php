<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductSubcategoryResource\Pages;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use App\Support\Permissions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductSubcategoryResource extends Resource
{
    use Translatable;

    protected static ?string $model = ProductSubcategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Ürün Kataloğu';

    protected static ?string $navigationLabel = 'Ürün Alt Kategorileri';

    protected static ?string $modelLabel = 'Ürün Alt Kategorisi';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('İçerik')
                ->columns(2)
                ->schema([
                    Select::make('product_category_id')
                        ->label('Ana Kategori')
                        ->options(fn () => ProductCategory::query()->ordered()->get()->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->columnSpanFull(),
                    TextInput::make('name')->label('Ad')->required()->maxLength(150)->columnSpanFull(),
                    TextInput::make('slug')->label('Slug')->maxLength(180)->unique(ignoreRecord: true)
                        ->helperText('Boş bırakılırsa addan otomatik oluşturulur.'),
                    Textarea::make('description')->label('Açıklama')->rows(4)->columnSpanFull(),
                    FileUpload::make('image')->label('Görsel')->image()->directory('product-subcategories')->columnSpanFull(),
                ]),
            Section::make('Yayın')
                ->columns(2)
                ->schema([
                    Toggle::make('is_active')->label('Aktif')->default(true),
                    TextInput::make('sort_order')->label('Sıra')->numeric()->default(1),
                ]),
            ...ProductSubcategory::seoFormSchema(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->label('Görsel')->square(),
                TextColumn::make('name')->label('Ad')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Ana Kategori')->sortable(),
                TextColumn::make('products_count')->label('Ürün')->counts('products'),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('product_category_id')->label('Ana Kategori')
                    ->options(fn () => ProductCategory::query()->ordered()->get()->pluck('name', 'id')),
                TernaryFilter::make('is_active')->label('Aktif mi?'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductSubcategories::route('/'),
            'create' => Pages\CreateProductSubcategory::route('/create'),
            'edit' => Pages\EditProductSubcategory::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can(Permissions::MANAGE_PRODUCTS) ?? false;
    }
}

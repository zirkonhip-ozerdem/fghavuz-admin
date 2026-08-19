<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Support\Permissions;
use App\Support\Traits\HasTranslatableTabs;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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

class BlogPostResource extends Resource
{
    use HasTranslatableTabs;

    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?string $navigationLabel = 'Blog Yazıları';

    protected static ?string $modelLabel = 'Blog Yazısı';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Ortak Bilgiler')
                ->columns(2)
                ->schema([
                    Select::make('blog_category_id')
                        ->label('Kategori')
                        ->options(fn () => BlogCategory::query()->ordered()->get()->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->columnSpanFull(),
                    TextInput::make('author_name')->label('Yazar')->default('Admin Fatih Gül'),
                    FileUpload::make('cover_image')->label('Kapak Görseli')->directory('blog/cover')->columnSpanFull()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize((int) env('MEDIA_MAX_IMAGE_SIZE', 5120))
                        ->helperText('JPG, PNG veya WEBP yükleyin. Önerilen en-boy oranı 16:9. Maksimum dosya boyutu: 5 MB.')
                        ->live()
                        ->afterStateUpdated(static::imageAltAutoFillCallback('cover_image')),
                    Grid::make(3)
                        ->columnSpanFull()
                        ->schema(static::imageAltFields('cover_image')),
                ]),
            Section::make('Dil Bazlı İçerik')
                ->description('Her dil kendi sekmesinde: başlık, özet, içerik ve o dile ait SEO alanları bir arada.')
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
                                            ->maxLength(200)
                                            ->extraInputAttributes(fn (string $operation, ?\Illuminate\Database\Eloquent\Model $record) => static::slugGeneratorAttributes($locale, $operation, $record)),
                                        TextInput::make("slug_{$locale}")
                                            ->label('Slug')
                                            ->maxLength(220)
                                            ->unique(table: BlogPost::class, column: "slug->{$locale}", ignoreRecord: true)
                                            ->helperText('Otomatik doldurulur, istenirse elle değiştirilebilir.'),
                                        Textarea::make("excerpt_{$locale}")->label('Özet')->rows(3),
                                        RichEditor::make("content_{$locale}")->label('İçerik'),
                                        Section::make('SEO')
                                            ->collapsible()
                                            ->collapsed()
                                            ->schema(BlogPost::translatableSeoFormSchema($locale)),
                                    ]))
                                ->values()
                                ->all()
                        ),
                ]),
            Section::make('Yayın')
                ->columns(3)
                ->schema([
                    DateTimePicker::make('published_at')->label('Yayın Tarihi'),
                    TextInput::make('sort_order')->label('Sıra')->numeric()->default(1),
                    Toggle::make('is_active')->label('Aktif')->default(true),
                    Toggle::make('is_featured')->label('Öne Çıkan'),
                ]),
            Section::make('SEO - Ortak Alanlar')
                ->description('Dile bağlı olmayan SEO ayarları.')
                ->collapsible()
                ->collapsed()
                ->columns(2)
                ->schema(BlogPost::nonTranslatableSeoFormSchema()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')->label('Görsel')->square(),
                TextColumn::make('title')->label('Başlık')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Kategori')->sortable(),
                TextColumn::make('author_name')->label('Yazar'),
                TextColumn::make('published_at')->label('Yayın Tarihi')->dateTime('d.m.Y H:i')->sortable(),
                IconColumn::make('is_featured')->label('Öne Çıkan')->boolean(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('blog_category_id')->label('Kategori')
                    ->options(fn () => BlogCategory::query()->ordered()->get()->pluck('name', 'id')),
                TernaryFilter::make('is_active')->label('Aktif mi?'),
                TernaryFilter::make('is_featured')->label('Öne Çıkan mı?'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can(Permissions::MANAGE_BLOG) ?? false;
    }
}

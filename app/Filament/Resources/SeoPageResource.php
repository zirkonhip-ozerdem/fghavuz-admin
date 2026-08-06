<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeoPageResource\Pages;
use App\Models\SeoPage;
use App\Support\Permissions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeoPageResource extends Resource
{
    protected static ?string $model = SeoPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static ?string $navigationGroup = 'Sistem';

    protected static ?string $navigationLabel = 'SEO Sayfaları';

    protected static ?string $modelLabel = 'SEO Sayfası';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('page_key')
                ->label('Sayfa Anahtarı')
                ->options(array_combine(SeoPage::PAGE_KEYS, SeoPage::PAGE_KEYS))
                ->required()
                ->searchable(),
            Select::make('locale')
                ->label('Dil')
                ->options(array_combine(active_locales(), active_locales()))
                ->required(),
            TextInput::make('title')->label('Başlık')->maxLength(70)->columnSpanFull(),
            Textarea::make('description')->label('Açıklama')->maxLength(160)->rows(3)->columnSpanFull(),
            TextInput::make('keywords')->label('Anahtar Kelimeler')->columnSpanFull(),
            TextInput::make('canonical_url')->label('Canonical URL')->url(),
            TextInput::make('robots')->label('Robots')->default('index, follow'),
            TextInput::make('og_title')->label('OG Başlık')->maxLength(70)->columnSpanFull(),
            Textarea::make('og_description')->label('OG Açıklama')->rows(2)->columnSpanFull(),
            FileUpload::make('og_image')->label('OG Görsel')->image()->directory('seo/og')->columnSpanFull(),
            Textarea::make('schema_json')
                ->label('Schema.org JSON-LD (opsiyonel)')
                ->rows(4)
                ->columnSpanFull()
                ->helperText('Gecerli bir JSON nesnesi girin.'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('page_key')->label('Sayfa')->badge()->sortable()->searchable(),
                TextColumn::make('locale')->label('Dil')->badge()->sortable(),
                TextColumn::make('title')->label('Başlık')->limit(40)->searchable(),
                TextColumn::make('updated_at')->label('Güncellendi')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->defaultSort('page_key')
            ->filters([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeoPages::route('/'),
            'create' => Pages\CreateSeoPage::route('/create'),
            'edit' => Pages\EditSeoPage::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can(Permissions::MANAGE_SEO) ?? false;
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CorporateResource\Pages;
use App\Models\Corporate;
use App\Support\Permissions;
use App\Support\Traits\HasTranslatableTabs;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CorporateResource extends Resource
{
    use HasTranslatableTabs;

    protected static ?string $model = Corporate::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?string $navigationLabel = 'Kurumsal';

    protected static ?string $modelLabel = 'Kurumsal İçerik';

    protected static ?int $navigationSort = 0;

    /**
     * "values" formda hic kullanilmiyor; sekmelere paketlenip mevcut
     * verinin (varsa) ustune bos yazilmasin diye haric tutuluyor.
     */
    protected static function translatableTabExclusions(): array
    {
        return ['values'];
    }

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
                                        TextInput::make("title_{$locale}")->label('Başlık')->required()->maxLength(180),
                                        TextInput::make("subtitle_{$locale}")->label('Alt Başlık'),
                                        Textarea::make("description_{$locale}")->label('Açıklama')->rows(4),
                                        Textarea::make("story_sections_{$locale}")->label('Hikaye')->rows(6)
                                            ->helperText('Kurumsal hikaye metni.'),
                                        Textarea::make("mission_{$locale}")->label('Misyon')->rows(4),
                                        Textarea::make("vision_{$locale}")->label('Vizyon')->rows(4),
                                        Section::make('Kilometre Taşları / Zaman Çizelgesi')
                                            ->schema([
                                                Repeater::make("milestones_{$locale}")
                                                    ->label('Kilometre Taşları')
                                                    ->schema([
                                                        TextInput::make('year')->label('Yıl')->required(),
                                                        TextInput::make('title')->label('Başlık')->required(),
                                                        Textarea::make('description')->label('Açıklama')->rows(2),
                                                    ])
                                                    ->columns(3)
                                                    ->addActionLabel('Kilometre Taşı Ekle'),
                                            ]),
                                        Section::make('SEO')
                                            ->collapsible()
                                            ->collapsed()
                                            ->schema(Corporate::translatableSeoFormSchema($locale)),
                                    ]))
                                ->values()
                                ->all()
                        ),
                ]),
            Section::make('Medya')
                ->columns(2)
                ->schema([
                    TextInput::make('video_url')->label('Video URL (Youtube/Vimeo)')->url()
                        ->helperText('Video bir bağlantıysa bunu kullanın; dosya olarak yüklemenize gerek kalmaz.'),
                    FileUpload::make('video_media')->label('Video Dosyası')->directory('corporate/video')->acceptedFileTypes(['video/mp4'])
                        ->maxSize((int) env('MEDIA_MAX_DOCUMENT_SIZE', 20480))
                        ->helperText('Sadece MP4 formatında. Maksimum dosya boyutu: 20 MB. Büyük videolar için Video URL alanını kullanmanız önerilir.'),
                    FileUpload::make('image')->label('Görsel')->directory('corporate')->columnSpanFull()
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
                ->schema(Corporate::nonTranslatableSeoFormSchema()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Başlık'),
                TextColumn::make('updated_at')->label('Güncellendi')->dateTime('d.m.Y H:i'),
            ])
            ->actions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCorporates::route('/'),
            'create' => Pages\CreateCorporate::route('/create'),
            'edit' => Pages\EditCorporate::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can(Permissions::MANAGE_CORPORATE) ?? false;
    }

    public static function canCreate(): bool
    {
        return static::getModel()::count() === 0;
    }
}

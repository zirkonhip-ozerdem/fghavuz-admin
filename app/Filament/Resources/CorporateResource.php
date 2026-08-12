<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CorporateResource\Pages;
use App\Models\Corporate;
use App\Support\Permissions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CorporateResource extends Resource
{
    use Translatable;

    protected static ?string $model = Corporate::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?string $navigationLabel = 'Kurumsal';

    protected static ?string $modelLabel = 'Kurumsal İçerik';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Genel')
                ->columns(2)
                ->schema([
                    TextInput::make('title')->label('Başlık')->required()->maxLength(180)->columnSpanFull(),
                    TextInput::make('subtitle')->label('Alt Başlık')->columnSpanFull(),
                    Textarea::make('description')->label('Açıklama')->rows(4)->columnSpanFull(),
                    Textarea::make('story_sections')->label('Hikaye')->rows(6)->columnSpanFull()
                        ->helperText('Kurumsal hikaye metni.'),
                ]),
            Section::make('Misyon / Vizyon')
                ->columns(2)
                ->schema([
                    Textarea::make('mission')->label('Misyon')->rows(4),
                    Textarea::make('vision')->label('Vizyon')->rows(4),
                ]),
            Section::make('Kilometre Taşları / Zaman Çizelgesi')
                ->schema([
                    Repeater::make('milestones')
                        ->label('Kilometre Taşları')
                        ->schema([
                            TextInput::make('year')->label('Yıl')->required(),
                            TextInput::make('title')->label('Başlık')->required(),
                            Textarea::make('description')->label('Açıklama')->rows(2),
                        ])
                        ->columns(3)
                        ->addActionLabel('Kilometre Taşı Ekle'),
                ]),
            Section::make('Medya')
                ->columns(2)
                ->schema([
                    TextInput::make('video_url')->label('Video URL (Youtube/Vimeo)')->url(),
                    FileUpload::make('video_media')->label('Video Dosyası')->directory('corporate/video')->acceptedFileTypes(['video/mp4']),
                    FileUpload::make('image')->label('Görsel')->image()->directory('corporate')->columnSpanFull(),
                ]),
            Section::make('Yayın')
                ->columns(2)
                ->schema([
                    Toggle::make('is_active')->label('Aktif')->default(true),
                    TextInput::make('sort_order')->label('Sıra')->numeric()->default(1),
                ]),
            ...Corporate::seoFormSchema(),
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

<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Support\Permissions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * Site geneli tekil ayar formu (Modul 7 - Site Genel Ayarlari).
 * Bir "Resource" degil, tek kayitlik bir "Page" olarak modellenir; ekleme/silme yoktur.
 */
class ManageSiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Sistem';

    protected static ?string $navigationLabel = 'Site Ayarları';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.manage-site-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can(Permissions::MANAGE_SITE_SETTINGS) ?? false;
    }

    public function mount(): void
    {
        $setting = SiteSetting::current();

        // toArray() translatable alanlarda sadece aktif locale'in string degerini dondurur;
        // KeyValue alani icin tum dillerin { "tr": "...", "en": "..." } halini istiyoruz.
        $data = $setting->toArray();
        $data['footer_text'] = $setting->getTranslations('footer_text');

        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Genel')
                ->columns(2)
                ->schema([
                    TextInput::make('site_name')->label('Site Adı')->required(),
                    TextInput::make('copyright_text')->label('Copyright Metni'),
                    FileUpload::make('logo')->label('Logo')->image()->directory('site/branding'),
                    FileUpload::make('dark_logo')->label('Koyu Tema Logo')->image()->directory('site/branding'),
                    FileUpload::make('favicon')->label('Favicon')->image()->directory('site/branding'),
                    FileUpload::make('footer_logo')->label('Footer Logo')->image()->directory('site/branding'),
                ]),
            Section::make('İletişim')
                ->columns(2)
                ->schema([
                    TextInput::make('phone')->label('Telefon')->tel(),
                    TextInput::make('email')->label('E-posta')->email(),
                    TextInput::make('whatsapp')->label('WhatsApp')->tel(),
                    TextInput::make('address')->label('Adres'),
                    Textarea::make('map_embed_url')->label('Harita Embed URL')->columnSpanFull(),
                ]),
            Section::make('Sosyal Medya')
                ->columns(3)
                ->schema([
                    TextInput::make('instagram_url')->label('Instagram')->url(),
                    TextInput::make('linkedin_url')->label('LinkedIn')->url(),
                    TextInput::make('facebook_url')->label('Facebook')->url(),
                ]),
            Section::make('Dil ve Bakım')
                ->columns(2)
                ->schema([
                    TextInput::make('default_locale')->label('Varsayılan Dil')->default('tr')->required(),
                    Toggle::make('maintenance_mode')->label('Bakım Modu'),
                    KeyValue::make('footer_text')
                        ->label('Footer Metni (dil bazlı)')
                        ->keyLabel('Dil (tr/en/ar)')
                        ->valueLabel('Metin')
                        ->columnSpanFull(),
                ]),
            Section::make('Diğer Bağlantılar')
                ->columns(2)
                ->schema([
                    TextInput::make('yengec_yazilim_url')->label('Yengeç Yazılım URL')->url(),
                    TextInput::make('yna_ekibi_url')->label('YNA Ekibi URL')->url(),
                ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $footerText = $state['footer_text'] ?? [];
        unset($state['footer_text']);

        $setting = SiteSetting::current();
        $setting->fill($state);
        $setting->setTranslations('footer_text', is_array($footerText) ? $footerText : []);
        $setting->save();

        Notification::make()->title('Ayarlar kaydedildi')->success()->send();
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeoPageResource\Pages;
use App\Models\SeoPage;
use App\Support\Permissions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
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
            Section::make('Hangi Sayfa, Hangi Dil')
                ->description('Bu ayarlar tek bir ürün/blog yazısı için değil, sitenin sabit sayfaları (Anasayfa, Kurumsal, Ürünler, Katalog, Blog, İletişim, Teklif Al) için geçerlidir. Her sayfanın her dil için AYRI bir kaydı olur — örneğin "Anasayfa" + "tr" bir kayıt, "Anasayfa" + "en" başka bir kayıttır.')
                ->columns(2)
                ->schema([
                    Select::make('page_key')
                        ->label('Sayfa Anahtarı')
                        ->options(array_combine(SeoPage::PAGE_KEYS, SeoPage::PAGE_KEYS))
                        ->required()
                        ->searchable()
                        ->helperText('SEO ayarını hangi sabit sayfaya uygulayacağınızı seçin. Ornek: urunler = /urunler listeleme sayfasi, corporate = Kurumsal sayfasi.'),
                    Select::make('locale')
                        ->label('Dil')
                        ->options(array_combine(active_locales(), active_locales()))
                        ->required()
                        ->helperText('Bu kayıt hangi dil versiyonu için geçerli olacak (tr/en/ar). Aynı sayfanın her dili için ayrı ayrı kayıt oluşturmanız gerekir.'),
                ]),
            Section::make('Arama Motoru Bilgileri')
                ->description('Bu sayfa Google arama sonuçlarında nasıl görünecek, onu belirler.')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Başlık')
                        ->maxLength(70)
                        ->helperText('Google arama sonucunda mavi baslik olarak cikar. Ornek: "Havuz Urunleri ve Ekipmanlari | FGPOOL"')
                        ->columnSpanFull(),
                    Textarea::make('description')
                        ->label('Açıklama')
                        ->maxLength(160)
                        ->rows(3)
                        ->helperText('Google sonucunda basligin altinda cikan kisa tanitim metni. 160 karakteri gecmesin, kullaniciyi tiklamaya tesvik etsin.')
                        ->columnSpanFull(),
                    TextInput::make('keywords')
                        ->label('Anahtar Kelimeler')
                        ->helperText('Bu sayfayla ilgili kelimeler, virgulle ayirin. Ornek: havuz pompasi, havuz ekipmani, havuz filtresi')
                        ->columnSpanFull(),
                    TextInput::make('canonical_url')
                        ->label('Canonical URL')
                        ->url()
                        ->helperText('Bu sayfaya birden fazla adresten ulasiliyorsa, Google\'a "asil sayfa budur" demek icin doldurun. Emin degilseniz bos birakin.'),
                    TextInput::make('robots')
                        ->label('Robots')
                        ->default('index, follow')
                        ->helperText('"index, follow" (varsayilan) = sayfa Google\'da gorunsun. "noindex, follow" = bu sayfa aramada GORUNMESIN. Ne yaptiginizdan emin degilseniz degistirmeyin.'),
                ]),
            Section::make('Sosyal Medya Paylaşım Görünümü')
                ->description('Bu sayfa Instagram gibi platformlarda paylasildiginda ne gorunecegini belirler. Bos birakilirsa yukaridaki "Baslik"/"Aciklama" kullanilir.')
                ->columns(2)
                ->schema([
                    TextInput::make('og_title')
                        ->label('OG Başlık')
                        ->maxLength(70)
                        ->helperText('Sosyal medya paylasiminda cikacak baslik.')
                        ->columnSpanFull(),
                    Textarea::make('og_description')
                        ->label('OG Açıklama')
                        ->rows(2)
                        ->helperText('Sosyal medya paylasiminda basligin altinda cikacak kisa aciklama.')
                        ->columnSpanFull(),
                    FileUpload::make('og_image')
                        ->label('OG Görsel')
                        ->image()
                        ->directory('seo/og')
                        ->helperText('Paylasimda cikacak onizleme gorseli. En iyi sonuc icin 1200x630 piksel boyutunda yukleyin.')
                        ->columnSpanFull(),
                ]),
            Section::make('Gelişmiş (İsteğe Bağlı)')
                ->description('Teknik bir alandır, genellikle dokunmanıza gerek yoktur.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Textarea::make('schema_json')
                        ->label('Schema.org JSON-LD (opsiyonel)')
                        ->rows(4)
                        ->columnSpanFull()
                        ->helperText('Google\'a sayfa icerigi hakkinda ekstra yapisal bilgi vermek icin kullanilir (orn. urun fiyati, yildiz puani gibi zengin sonuclar icin). Gecerli bir JSON nesnesi girin, ornek: {"@context":"https://schema.org","@type":"Organization","name":"FGPOOL"}. Bilmiyorsaniz bos birakin.'),
                ]),
        ]);
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

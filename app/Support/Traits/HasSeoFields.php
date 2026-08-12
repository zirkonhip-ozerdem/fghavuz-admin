<?php

namespace App\Support\Traits;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

/**
 * Tum icerik modellerinde (Corporate, ProductCategory, Product, BlogPost, ...)
 * tekrar eden SEO alanlarini tek yerden yonetmek icin.
 *
 * seo_title / seo_description / seo_keywords / og_title / og_description
 * cok dilli (translatable) tutulur; canonical_url / og_image / robots
 * dile bagli olmayan sabit alanlardir.
 *
 * Migration: $table->seoFields();  (bkz. AppServiceProvider Blueprint macro)
 */
trait HasSeoFields
{
    public static function seoTranslatableFields(): array
    {
        return ['seo_title', 'seo_description', 'seo_keywords', 'og_title', 'og_description'];
    }

    protected function initializeHasSeoFields(): void
    {
        $this->translatable = array_values(array_unique(array_merge(
            $this->translatable ?? [],
            static::seoTranslatableFields()
        )));
    }

    public static function seoFormSchema(): array
    {
        return [
            Section::make('SEO')
                ->description('Bu bolum, sayfanizin Google gibi arama motorlarinda ve WhatsApp/Facebook gibi sosyal medyada nasil gorunecegini belirler. Hicbir alani doldurmak zorunlu degildir; bos birakirsaniz sistem genel site ayarlarini veya sayfanin kendi basligini/aciklamasini otomatik kullanir. Emin olmadiginiz alanlari bos birakabilirsiniz.')
                ->collapsible()
                ->collapsed()
                ->columns(2)
                ->schema([
                    TextInput::make('seo_title')
                        ->label('SEO Basligi')
                        ->maxLength(70)
                        ->helperText('Google arama sonuclarinda mavi baslik olarak gorunecek metindir. Bos birakilirsa sayfanin kendi basligi (ad/baslik alani) kullanilir. Ornek: "Havuz Pompalari Fiyatlari | Firma Adi"')
                        ->columnSpanFull(),
                    Textarea::make('seo_description')
                        ->label('SEO Aciklamasi')
                        ->maxLength(160)
                        ->rows(3)
                        ->helperText('Google arama sonucunda baslik altinda cikan kisa tanitim metnidir. Kullanicinin sayfaya tiklayip tiklamayacagina bu metin karar verdirir. 160 karakteri gecmeyecek sekilde, kisa ve merak uyandirici yazin.')
                        ->columnSpanFull(),
                    TextInput::make('seo_keywords')
                        ->label('Anahtar Kelimeler')
                        ->helperText('Bu icerikle ilgili kelimeleri virgulle ayirarak yazin. Ornek: havuz pompasi, havuz ekipmani, filtre pompasi.')
                        ->columnSpanFull(),
                    TextInput::make('canonical_url')
                        ->label('Canonical URL')
                        ->url()
                        ->helperText('Sadece bu icerige birden fazla adresten (ornegin filtreli/parametreli linklerden) ulasilabiliyorsa doldurun. Google\'a "asil/orijinal sayfa budur" demek icin kullanilir. Cogu icerik icin BOS birakabilirsiniz.')
                        ->placeholder('https://siteadi.com/ornek-sayfa'),
                    TextInput::make('robots')
                        ->label('Robots')
                        ->default('index, follow')
                        ->helperText('Arama motorlarina bu sayfayla ne yapacaklarini soyler. "index, follow" (varsayilan/onerilen) = sayfa aramada gorunsun. "noindex, follow" = bu sayfa arama sonuclarinda GORUNMESIN (ornegin tesekkur sayfasi gibi). Ne yaptiginizdan emin degilseniz degistirmeyin.'),
                    TextInput::make('og_title')
                        ->label('OG Baslik')
                        ->maxLength(70)
                        ->helperText('Bu sayfa WhatsApp, Facebook, Instagram gibi platformlarda paylasildiginda cikacak baslik. Bos birakilirsa "SEO Basligi" kullanilir.')
                        ->columnSpanFull(),
                    Textarea::make('og_description')
                        ->label('OG Aciklama')
                        ->rows(2)
                        ->helperText('Sosyal medyada paylasildiginda basligin altinda cikacak kisa aciklama. Bos birakilirsa "SEO Aciklamasi" kullanilir.')
                        ->columnSpanFull(),
                    FileUpload::make('og_image')
                        ->label('OG Gorsel (1200x630)')
                        ->image()
                        ->directory('seo/og')
                        ->helperText('Sosyal medyada paylasildiginda cikacak onizleme gorseli. En iyi sonuc icin 1200x630 piksel boyutunda bir gorsel yukleyin. Bos birakilirsa paylasimda gorsel cikmayabilir.')
                        ->columnSpanFull(),
                ]),
        ];
    }
}

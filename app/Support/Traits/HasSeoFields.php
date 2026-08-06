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
                ->description('Arama motoru ve sosyal medya paylasim meta verileri. Bos birakilirsa genel site ayarlari kullanilir.')
                ->collapsible()
                ->collapsed()
                ->columns(2)
                ->schema([
                    TextInput::make('seo_title')
                        ->label('SEO Basligi')
                        ->maxLength(70)
                        ->columnSpanFull(),
                    Textarea::make('seo_description')
                        ->label('SEO Aciklamasi')
                        ->maxLength(160)
                        ->rows(3)
                        ->columnSpanFull(),
                    TextInput::make('seo_keywords')
                        ->label('Anahtar Kelimeler')
                        ->helperText('Virgulle ayirin')
                        ->columnSpanFull(),
                    TextInput::make('canonical_url')
                        ->label('Canonical URL')
                        ->url(),
                    TextInput::make('robots')
                        ->label('Robots')
                        ->default('index, follow'),
                    TextInput::make('og_title')
                        ->label('OG Baslik')
                        ->maxLength(70)
                        ->columnSpanFull(),
                    Textarea::make('og_description')
                        ->label('OG Aciklama')
                        ->rows(2)
                        ->columnSpanFull(),
                    FileUpload::make('og_image')
                        ->label('OG Gorsel (1200x630)')
                        ->image()
                        ->directory('seo/og')
                        ->columnSpanFull(),
                ]),
        ];
    }
}

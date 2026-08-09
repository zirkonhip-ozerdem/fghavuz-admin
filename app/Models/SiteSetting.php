<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * Tekil (singleton) ayar kaydi. Her zaman id=1 uzerinden okunur/yazilir.
 * Yonetimi: App\Filament\Pages\ManageSiteSettings
 */
class SiteSetting extends Model
{
    use HasTranslations;

    protected $fillable = [
        'site_name',
        'logo',
        'dark_logo',
        'favicon',
        'footer_logo',
        'phone',
        'email',
        'whatsapp',
        'address',
        'map_embed_url',
        'instagram_url',
        'linkedin_url',
        'facebook_url',
        'default_locale',
        'active_locales',
        'footer_text',
        'copyright_text',
        'yengec_yazilim_url',
        'yna_ekibi_url',
    ];

    public array $translatable = ['footer_text'];

    protected function casts(): array
    {
        return [
            'active_locales' => 'array',
        ];
    }

    /**
     * Ayar kaydini getirir, yoksa varsayilan degerlerle olusturur.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1], [
            'site_name' => 'FGPOOL',
            'default_locale' => 'tr',
            'active_locales' => ['tr', 'en', 'ar'],
        ]);
    }
}

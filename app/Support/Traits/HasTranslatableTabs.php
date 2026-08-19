<?php

namespace App\Support\Traits;

/**
 * Filament Resource siniflarinda kullanilir. Her translatable alan icin
 * TR/EN/AR sekmeleri kurmak, kaydetmeden once bu sekmelerin gecici
 * "{alan}_{locale}" state'lerini modelin translatable JSON alanlarina
 * paketlemek/acmak icin ortak mantigi tek yerde toplar.
 *
 * Kullanan Resource, Filament\Resources\Resource'un sagladigi getModel()
 * metoduna sahip olmalidir.
 */
trait HasTranslatableTabs
{
    public static function locales(): array
    {
        return [
            'tr' => 'TR',
            'en' => 'EN',
            'ar' => 'AR',
        ];
    }

    /**
     * Sekmelere bolunmeyip oldugu gibi birakilmasi gereken translatable
     * alanlar (ornek: Corporate.milestones bir Repeater'dir, dil bazli
     * ayri ayri yonetilmez). Ihtiyaci olan Resource override eder.
     */
    protected static function translatableTabExclusions(): array
    {
        return [];
    }

    /**
     * O dilin baslik/ad alanindan, sunucuya hic gitmeden (canli Livewire
     * istegi olmadan) o dilin KENDI slug alanini ureten Alpine ozniteliklerini
     * dondurur. Sekmeler arasi hizli gecislerde canli bir sunucu istegi, henuz
     * gonderilmemis diger sekme verilerinin kaybolmasina yol acabildigi
     * icin slug uretimi kasitli olarak tamamen istemci tarafinda yapilir.
     * Sadece kayit olustururken calisir; duzenlemede elle girilen slug
     * uzerine yazilmaz.
     */
    public static function slugGeneratorAttributes(string $locale, string $operation): array
    {
        if ($operation !== 'create') {
            return [];
        }

        return [
            'x-on:blur' => <<<JS
                \$wire.set('data.slug_{$locale}', \$el.value
                    .toString()
                    .toLowerCase()
                    .replace(/ç/g, 'c').replace(/ğ/g, 'g').replace(/ı/g, 'i')
                    .replace(/ö/g, 'o').replace(/ş/g, 's').replace(/ü/g, 'u')
                    .trim()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+\$/g, ''), false)
                JS,
        ];
    }

    /**
     * Bir gorsel yukleme alaninin (FileUpload) hemen yanina konulacak, dil
     * bazli "alt metin" TextInput'larini uretir (orn. cover_image ->
     * cover_image_alt_tr/en/ar). Sekmelerin disinda, FileUpload'a bitisik
     * yerlestirilmek uzere tasarlanmistir; paketleme/acma yine "{alan}_{locale}"
     * isimlendirme kuralina gore otomatik calisir, nerede render edildigi
     * onemli degildir.
     */
    public static function imageAltFields(string $imageField): array
    {
        return collect(self::locales())
            ->map(fn (string $label, string $locale) => \Filament\Forms\Components\TextInput::make("{$imageField}_alt_{$locale}")
                ->label("Alt Metin ({$label})")
                ->maxLength(200)
                ->helperText('Otomatik gelir, elle değiştirilebilir. Örn: "Filtre pompası".'))
            ->values()
            ->all();
    }

    /**
     * Bir FileUpload'a ->afterStateUpdated() olarak baglanir: gorsel
     * her degistiginde (ilk yukleme veya mevcut gorselin degistirilmesi),
     * dosya adindan basit bir varsayilan alt metin uretip TUM dil alanlarina
     * yazar. Alt metin o an ekrandaki gorseli tanimladigi icin, gorsel
     * degisince eski (elle yazilmis olsa bile) aciklama artik yanlis
     * olacagindan bilerek uzerine yazilir; istenirse tekrar elle duzenlenebilir.
     */
    public static function imageAltAutoFillCallback(string $imageField): \Closure
    {
        return function ($state, $set) use ($imageField) {
            if (! $state instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                return;
            }

            $name = ucfirst(trim(str_replace(['-', '_'], ' ', pathinfo($state->getClientOriginalName(), PATHINFO_FILENAME))));

            if ($name === '') {
                return;
            }

            foreach (self::locales() as $locale => $label) {
                $set("{$imageField}_alt_{$locale}", $name);
            }
        };
    }

    /**
     * Sekmelerdeki "{alan}_{locale}" gecici alanlarini, modelin translatable
     * JSON alanlarina paketler. Kayit oncesi (create/save) cagrilir.
     *
     * Bos degerler kasitli olarak array'e hic konulmuyor: spatie/laravel-translatable
     * ayni JSON'a birden fazla dili tek seferde yazarken, aralarda bos string olan
     * bir dili bir sonraki dil islenirken (getTranslations filtrelemesi yuzunden)
     * sessizce siliyor. Bos degeri hic gondermemek bu veri kaybini onluyor.
     *
     * "slug" alani icin, istemci tarafi JS bir sekilde calismadiysa (JS kapali,
     * beklenmedik durum vb.) sunucu tarafinda o dilin baslik/ad alanindan
     * (slugSourceField()) yedek bir slug uretilir; boylece dolu bir baslikla
     * bos slug asla kaydedilmez.
     */
    public static function packTranslatableFields(array $data): array
    {
        $model = static::getModel();
        $excluded = static::translatableTabExclusions();
        $original = $data;

        foreach ((new $model)->getTranslatableAttributes() as $field) {
            if (in_array($field, $excluded, true)) {
                continue;
            }

            $data[$field] = collect(self::locales())
                ->keys()
                ->mapWithKeys(function (string $locale) use ($original, $field, $model) {
                    $value = $original["{$field}_{$locale}"] ?? '';

                    if ($value === '' && $field === 'slug' && method_exists($model, 'slugSourceField')) {
                        $sourceField = $model::slugSourceField();
                        $value = \Illuminate\Support\Str::slug($original["{$sourceField}_{$locale}"] ?? '');
                    }

                    return [$locale => $value];
                })
                ->filter(fn (mixed $value) => filled($value))
                ->all();

            foreach (self::locales() as $locale => $label) {
                unset($data["{$field}_{$locale}"]);
            }
        }

        return $data;
    }

    /**
     * Kayittan gelen translatable JSON alanlarini, formdaki "{alan}_{locale}"
     * gecici alanlarina acar. Duzenleme formunu doldururken cagrilir.
     */
    public static function unpackTranslatableFields(array $data): array
    {
        $model = static::getModel();
        $excluded = static::translatableTabExclusions();

        foreach ((new $model)->getTranslatableAttributes() as $field) {
            if (in_array($field, $excluded, true)) {
                continue;
            }

            $translations = $data[$field] ?? [];

            foreach (self::locales() as $locale => $label) {
                $data["{$field}_{$locale}"] = $translations[$locale] ?? '';
            }
        }

        return $data;
    }
}

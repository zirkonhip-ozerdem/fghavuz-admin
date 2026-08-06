<?php

namespace Database\Seeders;

use App\Models\SeoPage;
use Illuminate\Database\Seeder;

class SeoPageSeeder extends Seeder
{
    public function run(): void
    {
        $titles = [
            'home' => ['tr' => 'FGPOOL | Havuz Ekipmanları', 'en' => 'FGPOOL | Pool Equipment', 'ar' => 'FGPOOL | معدات المسابح'],
            'corporate' => ['tr' => 'Kurumsal | FGPOOL', 'en' => 'Corporate | FGPOOL', 'ar' => 'الشركة | FGPOOL'],
            'products' => ['tr' => 'Ürünler | FGPOOL', 'en' => 'Products | FGPOOL', 'ar' => 'المنتجات | FGPOOL'],
            'catalog' => ['tr' => 'Kataloglar | FGPOOL', 'en' => 'Catalogs | FGPOOL', 'ar' => 'الكتالوجات | FGPOOL'],
            'blog' => ['tr' => 'Blog | FGPOOL', 'en' => 'Blog | FGPOOL', 'ar' => 'المدونة | FGPOOL'],
            'contact' => ['tr' => 'İletişim | FGPOOL', 'en' => 'Contact | FGPOOL', 'ar' => 'اتصل بنا | FGPOOL'],
            'quote' => ['tr' => 'Teklif Al | FGPOOL', 'en' => 'Get a Quote | FGPOOL', 'ar' => 'اطلب عرض سعر | FGPOOL'],
            'references' => ['tr' => 'Referanslar | FGPOOL', 'en' => 'References | FGPOOL', 'ar' => 'المراجع | FGPOOL'],
        ];

        foreach (SeoPage::PAGE_KEYS as $pageKey) {
            foreach (active_locales() as $locale) {
                SeoPage::query()->updateOrCreate(
                    ['page_key' => $pageKey, 'locale' => $locale],
                    [
                        'title' => $titles[$pageKey][$locale] ?? ucfirst($pageKey).' | FGPOOL',
                        'description' => 'FGPOOL / Poolux havuz ekipmanları.',
                        'robots' => 'index, follow',
                    ]
                );
            }
        }
    }
}

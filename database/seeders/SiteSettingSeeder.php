<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $setting = SiteSetting::current();

        $setting->update([
            'site_name' => 'FGPOOL',
            'phone' => '+90 000 000 00 00',
            'email' => 'info@fgpool.com',
            'whatsapp' => '+90 000 000 00 00',
            'address' => 'Türkiye',
            'default_locale' => 'tr',
            'active_locales' => ['tr', 'en', 'ar'],
            'footer_text' => [
                'tr' => 'FGPOOL / Poolux - Havuz ekipmanlarında güvenilir çözüm ortağınız.',
                'en' => 'FGPOOL / Poolux - Your trusted partner for pool equipment.',
                'ar' => 'FGPOOL / Poolux - شريكك الموثوق لمعدات المسابح.',
            ],
            'copyright_text' => '© '.date('Y').' FGPOOL. Tüm hakları saklıdır.',
        ]);
    }
}

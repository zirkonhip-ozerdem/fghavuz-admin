<?php

namespace Database\Seeders;

use App\Models\Corporate;
use Illuminate\Database\Seeder;

class CorporateSeeder extends Seeder
{
    public function run(): void
    {
        $corporate = Corporate::current();

        $corporate->setTranslations('title', [
            'tr' => 'Kurumsal',
            'en' => 'Corporate',
            'ar' => 'الشركة',
        ]);
        $corporate->setTranslations('subtitle', [
            'tr' => 'Havuz sektöründe güvenin adı',
            'en' => 'A trusted name in the pool industry',
            'ar' => 'اسم موثوق في صناعة المسابح',
        ]);
        $corporate->setTranslations('mission', [
            'tr' => 'Kaliteli ve güvenilir havuz ekipmanları üretmek.',
            'en' => 'Manufacturing quality and reliable pool equipment.',
            'ar' => 'تصنيع معدات مسابح عالية الجودة وموثوقة.',
        ]);
        $corporate->setTranslations('vision', [
            'tr' => 'Bölgesinde lider havuz ekipmanları markası olmak.',
            'en' => 'To be the leading pool equipment brand in the region.',
            'ar' => 'أن نكون العلامة التجارية الرائدة لمعدات المسابح في المنطقة.',
        ]);
        $corporate->setTranslations('values', [
            'tr' => ['Kalite', 'Güvenilirlik', 'Yenilikçilik', 'Müşteri Memnuniyeti'],
            'en' => ['Quality', 'Reliability', 'Innovation', 'Customer Satisfaction'],
            'ar' => ['الجودة', 'الموثوقية', 'الابتكار', 'رضا العملاء'],
        ]);
        $corporate->is_active = true;
        $corporate->save();
    }
}

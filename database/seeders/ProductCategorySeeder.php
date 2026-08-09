<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['tr' => 'Havuz Pompaları', 'en' => 'Pool Pumps', 'ar' => 'مضخات المسابح'],
            ['tr' => 'Kum Filtreleri', 'en' => 'Sand Filters', 'ar' => 'فلاتر رملية'],
            ['tr' => 'Havuz İçi Aydınlatmalar - Lambalar', 'en' => 'Pool Lighting - Lamps', 'ar' => 'إضاءة المسابح - مصابيح'],
            ['tr' => 'Havuz İçi ve Kenar Ekipmanları', 'en' => 'Pool & Poolside Equipment', 'ar' => 'معدات المسبح وحوافه'],
            ['tr' => 'Havuz İçi Temizlik Ekipmanları', 'en' => 'Pool Cleaning Equipment', 'ar' => 'معدات تنظيف المسبح'],
            ['tr' => 'Dezenfeksiyon Sistemleri', 'en' => 'Disinfection Systems', 'ar' => 'أنظمة التعقيم'],
            ['tr' => 'Havuz Kimyasalları', 'en' => 'Pool Chemicals', 'ar' => 'كيماويات المسابح'],
            ['tr' => 'Diğer Ürünler', 'en' => 'Other Products', 'ar' => 'منتجات أخرى'],
        ];

        foreach ($categories as $index => $names) {
            $category = ProductCategory::query()->firstOrNew(['slug' => \Illuminate\Support\Str::slug($names['tr'])]);
            $category->name = $names;
            $category->is_active = true;
            $category->is_featured = $index < 4;
            $category->sort_order = $index + 1;
            $category->save();
        }
    }
}

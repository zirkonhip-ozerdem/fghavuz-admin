<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['tr' => 'Havuz Bakımı', 'en' => 'Pool Maintenance', 'ar' => 'صيانة المسبح'],
            ['tr' => 'Sektör Haberleri', 'en' => 'Industry News', 'ar' => 'أخبار الصناعة'],
            ['tr' => 'Ürün Rehberleri', 'en' => 'Product Guides', 'ar' => 'أدلة المنتجات'],
        ];

        foreach ($categories as $index => $names) {
            $category = BlogCategory::query()->firstOrNew(['slug' => Str::slug($names['tr'])]);
            $category->name = $names;
            $category->is_active = true;
            $category->sort_order = $index + 1;
            $category->save();
        }
    }
}

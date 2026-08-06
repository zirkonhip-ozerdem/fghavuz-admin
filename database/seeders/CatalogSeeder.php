<?php

namespace Database\Seeders;

use App\Models\Catalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalogs = [
            ['tr' => '2026 Genel Ürün Kataloğu', 'en' => '2026 General Product Catalog', 'ar' => 'الكتالوج العام 2026'],
            ['tr' => 'Havuz Kimyasalları Kataloğu', 'en' => 'Pool Chemicals Catalog', 'ar' => 'كتالوج كيماويات المسابح'],
        ];

        foreach ($catalogs as $index => $names) {
            $slug = Str::slug($names['tr']);
            $path = "catalogs/{$slug}.pdf";

            if (! Storage::disk('public')->exists($path)) {
                // Gercek katalog panelden yuklenene kadar placeholder dosya.
                Storage::disk('public')->put($path, "%PDF-1.4\n% FGPOOL placeholder catalog - lutfen panelden gercek dosyayi yukleyin.\n");
            }

            $catalog = Catalog::query()->firstOrNew(['slug' => $slug]);
            $catalog->title = $names;
            $catalog->file = $path;
            $catalog->is_active = true;
            $catalog->sort_order = $index;
            $catalog->save();
        }
    }
}

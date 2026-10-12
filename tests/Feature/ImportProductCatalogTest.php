<?php

namespace Tests\Feature;

use App\Console\Commands\ImportProductCatalog;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Tests\TestCase;

class ImportProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    private array $files = [];

    protected function beforeRefreshingDatabase(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge();
    }

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            @unlink($file);
        }
        parent::tearDown();
    }

    public function test_preview_does_not_write_and_import_preserves_relationships_and_variants(): void
    {
        $category = ProductCategory::create([
            'name' => ['tr' => 'Havuz Pompaları', 'en' => 'Pool Pumps'],
            'slug' => ['tr' => 'custom-pumps'], 'image' => 'existing.jpg', 'sort_order' => 99,
        ]);
        $file = $this->catalog([
            ['Havuz Pompaları', 'Seri', 'Pompa', 'Açıklama', 'Debi 10', 'A100 25 EUR | A101 30 EUR', 10],
            ['Filtreler', 'Seri', 'Filtre', 'Detay', 'Çap 500', '', 11],
            ['Filtreler', 'Seri', 'Filtre', 'Farklı detay', 'Çap 620', 'B200 40 EUR', 12],
        ]);

        $this->artisan('products:import-catalog', ['file' => $file])->assertSuccessful();
        $this->assertSame(1, ProductCategory::count());
        $this->assertSame(0, Product::count());
        $this->assertSame(0, ProductSubcategory::count());

        $this->artisan('products:import-catalog', ['file' => $file, '--apply' => true])->assertSuccessful();
        $this->assertSame(2, ProductCategory::count());
        $this->assertSame(2, ProductSubcategory::count());
        $this->assertSame(3, Product::count());
        $product = Product::where('title->tr', 'Pompa')->firstOrFail();
        $this->assertSame($category->id, $product->product_category_id);
        $this->assertSame($category->id, $product->subcategory->product_category_id);
        $this->assertFalse($product->is_active);
        $this->assertSame(['tr' => 'Pompa'], $product->getTranslations('title'));
        $this->assertSame('A100 25 EUR | A101 30 EUR', $product->technical_specs['Kod / Fiyat / Seçenekler']);
        $this->assertStringContainsString('A101 30 EUR', $product->technical_description);
        $this->assertSame(2, Product::where('title->tr', 'Filtre')->count());
        $this->assertSame(3, Product::get()->pluck('slug')->unique()->count());
        $this->assertSame('existing.jpg', $category->fresh()->image);
        $this->assertSame('Pool Pumps', $category->fresh()->getTranslation('name', 'en', false));

        $product->update(['description' => ['tr' => 'Edited by admin'], 'is_active' => true]);
        $this->artisan('products:import-catalog', ['file' => $file, '--apply' => true])->assertSuccessful();
        $this->assertSame(3, Product::count());
        $this->assertSame('Edited by admin', $product->fresh()->description);
        $this->assertTrue($product->fresh()->is_active);
    }

    public function test_invalid_row_prevents_partial_import(): void
    {
        $file = $this->catalog([
            ['Filtreler', 'Alt', 'Geçerli', 'Detay', '', '', 2],
            ['Filtreler', '', 'Eksik alt kategori', '', '', '', 3],
        ]);
        $this->artisan('products:import-catalog', ['file' => $file, '--apply' => true])->assertFailed();
        $this->assertSame(0, ProductCategory::count());
        $this->assertSame(0, Product::count());
    }

    public function test_ambiguous_categories_abort_without_writes(): void
    {
        foreach (['filtreler', 'filtreler-2'] as $slug) {
            ProductCategory::create(['name' => ['tr' => 'Filtreler'], 'slug' => ['tr' => $slug]]);
        }
        $file = $this->catalog([['Filtreler', 'Alt', 'Ürün', 'Detay', '', '', 2]]);
        $this->artisan('products:import-catalog', ['file' => $file, '--apply' => true])->assertFailed();
        $this->assertSame(2, ProductCategory::count());
        $this->assertSame(0, ProductSubcategory::count());
        $this->assertSame(0, Product::count());
    }

    private function catalog(array $rows): string
    {
        $file = tempnam(sys_get_temp_dir(), 'fgpool-catalog-');
        $this->files[] = $file;
        $writer = new Writer();
        $writer->openToFile($file);
        $writer->getCurrentSheet()->setName('Ürünler');
        $writer->addRow(Row::fromValues(ImportProductCatalog::HEADERS));
        foreach ($rows as $row) {
            $writer->addRow(Row::fromValues($row));
        }
        $writer->close();

        return $file;
    }
}

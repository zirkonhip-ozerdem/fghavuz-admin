<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenSpout\Reader\XLSX\Reader;
use RuntimeException;
use Throwable;

class ImportProductCatalog extends Command
{
    protected $signature = 'products:import-catalog {file : Excel catalog path} {--apply : Save the import; otherwise preview only}';

    protected $description = 'Import Turkish catalog categories, subcategories and draft products without overwriting existing records';

    public const HEADERS = [
        'Ana Kategori', 'Alt Kategori', 'Ürün Adı', 'Açıklama / Ürün Detayı',
        'Teknik Bilgiler', 'Kod / Fiyat / Seçenekler', 'Katalog Sayfası',
    ];

    public function handle(): int
    {
        try {
            $rows = $this->readRows((string) $this->argument('file'));
            DB::transaction(function () use ($rows): void {
                $categories = [];
                $subcategories = [];
                $products = [];
                $slugs = [];
                $existingProducts = 0;

                // Build the full plan before saving any record.
                foreach ($rows as $row) {
                    [$categoryName, $subcategoryName, $title, $description, $technical, $options, $page] = $row;
                    $categoryKey = $categoryName;
                    $subcategoryKey = json_encode([$categoryName, $subcategoryName], JSON_THROW_ON_ERROR);

                    if (! isset($categories[$categoryKey])) {
                        $categories[$categoryKey] = $this->findOrPrepare(
                            ProductCategory::class, $categoryName, [], count($categories) + 1, $slugs
                        );
                    }
                    $category = $categories[$categoryKey];

                    if (! isset($subcategories[$subcategoryKey])) {
                        $scope = $category->exists ? ['product_category_id' => $category->id] : null;
                        $subcategories[$subcategoryKey] = $this->findOrPrepare(
                            ProductSubcategory::class, $subcategoryName, $scope, count($subcategories) + 1, $slugs
                        );
                    }
                    $subcategory = $subcategories[$subcategoryKey];
                    $key = json_encode([$categoryName, $subcategoryName, $title, $page], JSON_THROW_ON_ERROR);

                    if (isset($products[$key])) {
                        throw new RuntimeException("Duplicate product and catalog page: {$title} ({$page}).");
                    }

                    $matches = $category->exists && $subcategory->exists
                        ? Product::withTrashed()->where('product_category_id', $category->id)
                            ->where('product_subcategory_id', $subcategory->id)->where('title->tr', $title)->get()
                        : collect();
                    $existing = $matches->first(fn (Product $product): bool =>
                        (string) ($product->technical_specs['Katalog Sayfası'] ?? '') === $page
                    );
                    if (! $existing && $matches->count() === 1 && empty($matches->first()->technical_specs['Katalog Sayfası'])) {
                        $existing = $matches->first();
                    }
                    if (! $existing && $matches->contains(fn (Product $product): bool => empty($product->technical_specs['Katalog Sayfası']))) {
                        throw new RuntimeException("Ambiguous existing product: {$title}.");
                    }
                    if ($existing?->trashed()) {
                        throw new RuntimeException("Product is in trash; resolve before import: {$title}.");
                    }

                    $specs = ['Katalog Sayfası' => $page];
                    if ($options !== '') {
                        $specs['Kod / Fiyat / Seçenekler'] = $options;
                    }
                    $product = $existing ?? new Product([
                        'title' => ['tr' => $title],
                        'slug' => ['tr' => $this->uniqueSlug(Product::class, $title, $slugs)],
                        'description' => $description !== '' ? ['tr' => $description] : null,
                        'technical_description' => ['tr' => implode("\n\n", array_filter([
                            $technical, $options !== '' ? "Kod / Fiyat / Seçenekler:\n".$options : '',
                        ], fn (string $value): bool => $value !== ''))],
                        'technical_specs' => $specs,
                        'is_active' => false,
                        'is_featured' => false,
                        'sort_order' => count($products) + 1,
                    ]);
                    $existingProducts += (int) ($existing !== null);
                    $products[$key] = [$product, $categoryKey, $subcategoryKey];
                }

                $this->table(['Record', 'New', 'Existing (preserved)'], [
                    ['Categories', count(array_filter($categories, fn ($model) => ! $model->exists)), count(array_filter($categories, fn ($model) => $model->exists))],
                    ['Subcategories', count(array_filter($subcategories, fn ($model) => ! $model->exists)), count(array_filter($subcategories, fn ($model) => $model->exists))],
                    ['Products', count($products) - $existingProducts, $existingProducts],
                ]);

                if (! $this->option('apply')) {
                    $this->info('Preview only. No records saved. Use --apply to import draft products.');

                    return;
                }

                $backup = 'catalog-import-backups/'.now()->format('Ymd-His').'-'.Str::uuid().'.json';
                $snapshot = [];
                foreach ([ProductCategory::class, ProductSubcategory::class, Product::class] as $class) {
                    $snapshot[(new $class())->getTable()] = $class::withTrashed()->get()->map->getAttributes()->all();
                }
                if (! Storage::disk('local')->put($backup, json_encode($snapshot, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
                    throw new RuntimeException('Could not save catalog backup. Import cancelled.');
                }
                $this->line('Backup: '.Storage::disk('local')->path($backup));

                foreach ($categories as $category) {
                    if (! $category->exists) {
                        $category->save();
                    }
                }
                foreach ($products as [$product, $categoryKey, $subcategoryKey]) {
                    $subcategory = $subcategories[$subcategoryKey];
                    if (! $subcategory->exists) {
                        $subcategory->product_category_id = $categories[$categoryKey]->id;
                        $subcategory->save();
                    }
                    if (! $product->exists) {
                        $product->product_category_id = $categories[$categoryKey]->id;
                        $product->product_subcategory_id = $subcategory->id;
                        $product->save();
                    }
                }
                $this->info('Import complete. New products are inactive; existing records are unchanged.');
            });

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    private function readRows(string $file): array
    {
        $reader = new Reader();
        $reader->open($file);
        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                if ($sheet->getName() !== 'Ürünler') {
                    continue;
                }
                $rows = [];
                foreach ($sheet->getRowIterator() as $number => $row) {
                    $values = array_map(function ($value) use ($number): string {
                        if ($value !== null && ! is_string($value) && ! is_numeric($value)) {
                            throw new RuntimeException("Unsupported cell value in row {$number}.");
                        }

                        return trim((string) $value);
                    }, $row->toArray());
                    if ($number === 1) {
                        if ($values !== self::HEADERS) {
                            throw new RuntimeException('Catalog headers do not match the expected seven columns.');
                        }
                        continue;
                    }
                    if (! array_filter($values, fn (string $value): bool => $value !== '')) {
                        continue;
                    }
                    $values = array_pad($values, 7, '');
                    if (count($values) !== 7 || in_array('', array_slice($values, 0, 3), true)
                        || ! ctype_digit($values[6]) || (int) $values[6] < 1
                        || mb_strlen($values[0]) > 180 || mb_strlen($values[1]) > 180 || mb_strlen($values[2]) > 180) {
                        throw new RuntimeException("Invalid category, title or catalog page in row {$number}.");
                    }
                    $rows[] = $values;
                }
                if ($rows === []) {
                    throw new RuntimeException('The catalog contains no products.');
                }

                return $rows;
            }
            throw new RuntimeException('The Ürünler worksheet is missing.');
        } finally {
            $reader->close();
        }
    }

    private function findOrPrepare(string $class, string $name, ?array $scope, int $order, array &$slugs): Model
    {
        $matches = $scope !== null ? $class::withTrashed()->where($scope)->where('name->tr', $name)->get() : collect();
        if ($matches->count() > 1 || $matches->first()?->trashed()) {
            throw new RuntimeException("Ambiguous or deleted category: {$name}.");
        }

        return $matches->first() ?? new $class([
            'name' => ['tr' => $name],
            'slug' => ['tr' => $this->uniqueSlug($class, $name, $slugs)],
            'is_active' => true,
            'sort_order' => $order,
        ]);
    }

    private function uniqueSlug(string $class, string $title, array &$reserved): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            throw new RuntimeException("Cannot generate a slug for {$title}.");
        }
        $slug = $base;
        $suffix = 2;
        while (isset($reserved[$class][$slug]) || $class::withTrashed()->where('slug->tr', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }
        $reserved[$class][$slug] = true;

        return $slug;
    }
}

<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeCategory(): ProductCategory
    {
        return ProductCategory::create([
            'name' => ['tr' => 'Havuz Pompaları', 'en' => 'Pool Pumps', 'ar' => 'مضخات المسابح'],
            'description' => ['tr' => 'Açıklama', 'en' => 'Description', 'ar' => 'وصف'],
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 0,
        ]);
    }

    public function test_lists_active_categories_with_translated_name(): void
    {
        $this->makeCategory();

        $response = $this->getJson('/api/v1/products/categories?locale=en');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.name', 'Pool Pumps');
    }

    public function test_lists_products_and_filters_by_category_slug(): void
    {
        $category = $this->makeCategory();

        $product = Product::create([
            'product_category_id' => $category->id,
            'title' => ['tr' => 'Test Pompa', 'en' => 'Test Pump', 'ar' => 'مضخة اختبار'],
            'short_description' => ['tr' => 'Kısa', 'en' => 'Short', 'ar' => 'قصير'],
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 0,
        ]);

        $response = $this->getJson("/api/v1/products?category={$category->slug}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.items.0.slug', $product->slug);
    }

    public function test_shows_product_detail_by_slug_with_default_tr_locale(): void
    {
        $category = $this->makeCategory();

        $product = Product::create([
            'product_category_id' => $category->id,
            'title' => ['tr' => 'Test Pompa', 'en' => 'Test Pump', 'ar' => 'مضخة اختبار'],
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $response = $this->getJson("/api/v1/products/{$product->slug}");

        $response->assertOk()->assertJsonPath('data.title', 'Test Pompa');
    }

    public function test_returns_404_envelope_for_missing_product(): void
    {
        $response = $this->getJson('/api/v1/products/does-not-exist');

        $response->assertStatus(404)->assertJsonPath('success', false);
    }
}

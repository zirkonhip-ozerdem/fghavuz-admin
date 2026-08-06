<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductCategoryResource;
use App\Models\ProductCategory;
use App\Support\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

class ProductCategoryController extends Controller
{
    use ApiResponses;

    /**
     * GET /api/v1/products/categories
     * Urunler sayfasindaki kategori grid'i icin: alt kategori + urun sayisiyla birlikte.
     */
    public function index(): JsonResponse
    {
        $categories = ProductCategory::query()
            ->active()
            ->ordered()
            ->withCount('products')
            ->with(['subcategories' => fn ($q) => $q->active()->ordered()])
            ->get();

        return $this->success(ProductCategoryResource::collection($categories));
    }

    /**
     * GET /api/v1/products/categories/{slug}
     */
    public function show(string $slug): JsonResponse
    {
        $category = ProductCategory::query()
            ->active()
            ->where('slug', $slug)
            ->withCount('products')
            ->with(['subcategories' => fn ($q) => $q->active()->ordered()->withCount('products')])
            ->first();

        if (! $category) {
            return $this->fail('Ürün kategorisi bulunamadı.', 404);
        }

        return $this->success(new ProductCategoryResource($category));
    }
}

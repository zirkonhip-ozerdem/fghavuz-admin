<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductResource;
use App\Models\Product;
use App\Support\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponses;

    /**
     * GET /api/v1/products?category=slug&subcategory=slug&featured=1&q=&per_page=12
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()->active()->ordered()->with(['category', 'subcategory']);

        if ($categorySlug = $request->query('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        if ($subcategorySlug = $request->query('subcategory')) {
            $query->whereHas('subcategory', fn ($q) => $q->where('slug', $subcategorySlug));
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        if ($search = $request->query('q')) {
            $locale = current_api_locale();
            $query->where("title->{$locale}", 'ilike', "%{$search}%");
        }

        $perPage = min((int) $request->query('per_page', 12), 60);

        $products = $query->paginate($perPage)->appends($request->query());

        return $this->success([
            'items' => ProductResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/products/{slug}
     */
    public function show(string $slug): JsonResponse
    {
        $product = Product::query()
            ->active()
            ->where('slug', $slug)
            ->with(['category', 'subcategory'])
            ->first();

        if (! $product) {
            return $this->fail('Ürün bulunamadı.', 404);
        }

        return $this->success(new ProductResource($product));
    }

    /**
     * GET /api/v1/home/featured-products
     * Ana sayfa "Öne Çıkan Ürünlerimiz" bölümü: featured kategoriler + featured ürünler.
     */
    public function featured(): JsonResponse
    {
        $categories = \App\Models\ProductCategory::query()->active()->featured()->ordered()->get();
        $products = Product::query()->active()->featured()->ordered()->with(['category', 'subcategory'])->limit(12)->get();

        return $this->success([
            'featured_categories' => \App\Http\Resources\Api\ProductCategoryResource::collection($categories),
            'featured_products' => ProductResource::collection($products),
        ]);
    }
}

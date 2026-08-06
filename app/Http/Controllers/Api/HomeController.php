<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BlogPostResource;
use App\Http\Resources\Api\CatalogResource;
use App\Http\Resources\Api\CorporateResource;
use App\Http\Resources\Api\ProductCategoryResource;
use App\Http\Resources\Api\ProductResource;
use App\Http\Resources\Api\ReferenceProjectResource;
use App\Http\Resources\Api\SeoPageResource;
use App\Http\Resources\Api\SiteSettingResource;
use App\Models\BlogPost;
use App\Models\Catalog;
use App\Models\Corporate;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ReferenceProject;
use App\Models\SeoPage;
use App\Models\SiteSetting;
use App\Support\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    use ApiResponses;

    /**
     * GET /api/v1/home
     * Ana sayfanin ihtiyac duydugu tum bloklari tek istekte doner
     * (Modul 9 - Ana Sayfa Icerik API'si).
     */
    public function index(): JsonResponse
    {
        $locale = current_api_locale();

        $seo = SeoPage::query()->where('page_key', 'home')->where('locale', $locale)->first();

        return $this->success([
            'site_settings' => new SiteSettingResource(SiteSetting::current()),
            'corporate_highlight' => new CorporateResource(Corporate::current()),
            'featured_product_categories' => ProductCategoryResource::collection(
                ProductCategory::query()->active()->featured()->ordered()->get()
            ),
            'featured_products' => ProductResource::collection(
                Product::query()->active()->featured()->ordered()->with(['category', 'subcategory'])->limit(12)->get()
            ),
            'catalog_showcase' => CatalogResource::collection(
                Catalog::query()->active()->ordered()->limit(6)->get()
            ),
            'featured_blog_posts' => BlogPostResource::collection(
                BlogPost::query()->active()->published()->featured()->with('category')->limit(6)->get()
            ),
            'references' => ReferenceProjectResource::collection(
                ReferenceProject::query()->active()->featured()->ordered()->limit(8)->get()
            ),
            'seo' => $seo ? new SeoPageResource($seo) : null,
        ]);
    }
}

<?php

use App\Http\Controllers\Api\BlogCategoryController;
use App\Http\Controllers\Api\BlogPostController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\CorporateController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\QuoteRequestController;
use App\Http\Controllers\Api\ReferenceProjectController;
use App\Http\Controllers\Api\SeoPageController;
use App\Http\Controllers\Api\SiteSettingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1 - FGPOOL / Poolux Next.js frontend
|--------------------------------------------------------------------------
| Bu dosya routes/api.php icinden prefix('v1') + name('api.v1.') grubu
| altinda yuklenir. Tum locale destegi ?locale=tr|en|ar query param'i ile
| SetApiLocale middleware'i (bkz. bootstrap/app.php) tarafindan saglanir.
*/

Route::get('locales', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'default' => config('app.locale'),
            'active' => active_locales(),
        ],
        'message' => null,
    ]);
})->name('locales');

// --- Ana sayfa ---
Route::get('home', [HomeController::class, 'index'])->name('home.index');
Route::get('home/featured-products', [ProductController::class, 'featured'])->name('home.featured-products');
Route::get('home/featured-blog-posts', [BlogPostController::class, 'featured'])->name('home.featured-blog-posts');
Route::get('home/references', [ReferenceProjectController::class, 'featured'])->name('home.references');

// --- Kurumsal ---
Route::get('corporate', [CorporateController::class, 'show'])->name('corporate.show');

// --- Urunler ---
Route::get('products/categories', [ProductCategoryController::class, 'index'])->name('products.categories.index');
Route::get('products/categories/{slug}', [ProductCategoryController::class, 'show'])->name('products.categories.show');
Route::get('products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('products', [ProductController::class, 'index'])->name('products.index');

// --- Blog ---
Route::get('blog/categories', [BlogCategoryController::class, 'index'])->name('blog.categories.index');
Route::get('blog/posts/{slug}', [BlogPostController::class, 'show'])->name('blog.posts.show');
Route::get('blog/posts', [BlogPostController::class, 'index'])->name('blog.posts.index');

// --- Katalog ---
Route::get('catalogs/{slugOrId}/download', [CatalogController::class, 'download'])->name('catalogs.download');
Route::get('catalogs/{slugOrId}', [CatalogController::class, 'show'])->name('catalogs.show');
Route::get('catalogs', [CatalogController::class, 'index'])->name('catalogs.index');

// --- Referanslar ---
Route::get('references', [ReferenceProjectController::class, 'index'])->name('references.index');

// --- Site ayarlari & SEO ---
Route::get('site-settings', [SiteSettingController::class, 'show'])->name('site-settings.show');
Route::get('seo/{page_key}', [SeoPageController::class, 'show'])->name('seo.show');

// --- Formlar (rate limit uygulanir, Madde 15) ---
Route::post('contact/messages', [ContactMessageController::class, 'store'])
    ->middleware('throttle:contact-form')
    ->name('contact.messages.store');

Route::post('quote-requests', [QuoteRequestController::class, 'store'])
    ->middleware('throttle:quote-form')
    ->name('quote-requests.store');

// --- Sanctum korumali (ileride headless admin/entegrasyon ihtiyaci icin) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', fn () => response()->json([
        'success' => true,
        'data' => Auth::user(),
        'message' => null,
    ]))->name('me');
});

# FGPOOL / Poolux — Laravel API + Filament Admin

FGPOOL / Poolux kurumsal web sitesi için Laravel tabanlı API-first backend ve Filament admin panel. Next.js frontend'e JSON API sağlar; içerik yönetimi Filament admin panel üzerinden yapılır. Çok dilli yapı (tr / en / ar), öncelik Türkçe içerik.

> **Not:** Proje PHP 8.3 ile çalışacak şekilde kilitlenmiştir. Composer bağımlılıklarını kurmadan önce `intl`, `zip` ve `exif` PHP eklentilerinin aktif olduğundan emin olun.

## Teknoloji Yığını

- Laravel 12 (PHP ^8.3)
- PostgreSQL
- Filament 3 (admin panel) + özel TR/EN/AR sekmeli formlar + `filament/spatie-laravel-media-library-plugin`
- Laravel Sanctum (panel oturumu `web` guard ile session tabanlı; Sanctum SPA/token altyapısı ileride headless entegrasyon için hazır — `/api/v1/me`)
- Spatie Laravel Permission (rol/yetki)
- Spatie Media Library (ürün galeri/doküman yönetimi)
- Spatie Translatable + Spatie Sluggable (çok dilli içerik + merkezi slug üretimi)

## Kurulum

```bash
composer install
cp .env.example .env
php artisan key:generate

# PostgreSQL veritabanını oluşturun (ör. createdb fgpool_admin)
# .env içindeki DB_* değerlerini güncelleyin

php artisan migrate
php artisan storage:link
php artisan db:seed
```

Seeder sonrası panel girişi: `.env` içindeki `ADMIN_SEED_EMAIL` / `ADMIN_SEED_PASSWORD` (varsayılan `admin@fgpool.com` / `password`) — **ilk girişten sonra şifreyi değiştirin.**

Panel: `http://localhost:8000/admin`
API kökü: `http://localhost:8000/api/v1`
Health check: `http://localhost:8000/api/v1/health`

## fgpool-web Bağlantı Akışı

Bu repo backend/admin tarafıdır. `fgpool-web` frontend tarafı bu backend'i `/api/v1` üzerinden tüketir.

### 1. Lokal test

Backend:

```env
APP_URL=http://localhost:8000
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:3001
CORS_ALLOWED_ORIGIN_PATTERNS=
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,localhost:3001,127.0.0.1,127.0.0.1:3000,127.0.0.1:3001
SESSION_DOMAIN=null
```

`fgpool-web` `.env.local`:

```env
NEXT_PUBLIC_SITE_URL=http://localhost:3000
NEXT_PUBLIC_API_BASE_URL=http://localhost:8000/api/v1
NEXT_PUBLIC_BACKEND_URL=http://localhost:8000
```

### 2. Railway backend + Vercel frontend

Railway backend:

```env
RAILPACK_PHP_EXTENSIONS=intl,zip,exif
APP_ENV=production
APP_DEBUG=false
APP_URL=https://RAILWAY-BACKEND-DOMAIN
CORS_ALLOWED_ORIGINS=https://VERCEL-FRONTEND-DOMAIN,https://fgpool.com,https://www.fgpool.com
CORS_ALLOWED_ORIGIN_PATTERNS=^https://fgpool-web-.*\.vercel\.app$
SANCTUM_STATEFUL_DOMAINS=VERCEL-FRONTEND-DOMAIN,fgpool.com,www.fgpool.com
SESSION_DOMAIN=null
```

Vercel `fgpool-web`:

```env
NEXT_PUBLIC_SITE_URL=https://VERCEL-FRONTEND-DOMAIN
NEXT_PUBLIC_API_BASE_URL=https://RAILWAY-BACKEND-DOMAIN/api/v1
NEXT_PUBLIC_BACKEND_URL=https://RAILWAY-BACKEND-DOMAIN
```

Railway ilk deploy veya migration değişikliklerinden sonra:

```bash
php artisan migrate --force
```

İlk bağlantı testi için `/api/v1/health`, içerik testi için `/api/v1/site-settings?locale=tr` kullanılabilir.

Test için (PostgreSQL test veritabanı gerekir, bkz. `phpunit.xml`):

```bash
createdb fgpool_admin_testing
php artisan test
```

## Mimari

```
app/
  Models/               Eloquent modelleri
  Http/Controllers/Api/  Public API controller'ları (ince, iş mantığı yok)
  Http/Controllers/Admin/ (rezerve — şu an tüm admin işlemleri Filament üzerinden)
  Http/Requests/Api/     Public POST endpoint'leri için FormRequest validasyonu
  Http/Resources/Api/    JsonResource çıktı sınıfları (locale'e duyarlı)
  Http/Middleware/       SetApiLocale
  Filament/Resources/    Admin panel modülleri (CRUD)
  Filament/Pages/        Singleton sayfalar (Site Ayarları)
  Policies/               Rol/yetki bazlı erişim kontrolü
  Support/Traits/         HasSeoFields, HasActiveSortable, HasCentralizedSlug, ApiResponses
  Support/Permissions.php Rol/izin sabitleri tek merkezde
  Enums/                  ContactMessageStatus, QuoteRequestStatus
database/
  migrations/, seeders/, factories/
routes/
  api.php -> api_v1.php (v1 endpoint grubu), web.php, console.php
```

**Teknik borç kuralları:** Controller'lar ince (validasyon FormRequest'te, çıktı Resource'ta); slug üretimi `HasCentralizedSlug` trait'i üzerinden merkezi; SEO alanları `HasSeoFields` trait + `Blueprint::seoFields()` migration makrosu ile tekrarsız; medya yükleme Spatie Media Library (`Product` galeri/doküman) veya doğrudan `FileUpload` (tekil görseller) ile; durum alanları PHP native enum (`ContactMessageStatus`, `QuoteRequestStatus`).

## Çok Dilli Yapı

İçerik alanları (`title`, `description`, `name`, vb.) veritabanında JSON/JSONB kolon olarak tutulur: `{"tr": "...", "en": "...", "ar": "..."}` — `spatie/laravel-translatable` ile yönetilir. Admin panelde formlar özel TR/EN/AR sekmeleriyle düzenlenir. Public API `?locale=tr|en|ar` query parametresi ile istenen dili döner (`SetApiLocale` middleware, varsayılan `tr`, `.env` → `ACTIVE_LOCALES`).

SEO alanları (`seo_title`, `seo_description`, `seo_keywords`, `og_title`, `og_description`) da çok dillidir; `canonical_url`, `og_image`, `robots` dile bağlı değildir.

## Roller ve İzinler

| Rol | Yetkiler |
|---|---|
| `super_admin` | Tüm izinler (Gate::before ile otomatik geçer) |
| `admin` | Tüm izinler |
| `editor` | Kurumsal, ürün, blog, katalog, SEO, referans, medya yönetimi |
| `sales` | İletişim mesajları ve teklif talepleri görüntüleme/yönetme |

İzin sabitleri: `App\Support\Permissions`. Yeni izin eklerken `roleMatrix()` metodunu güncelleyin ve `php artisan db:seed --class=RolePermissionSeeder` çalıştırın.

## API Endpoint Listesi

Tüm cevaplar `{ "success": bool, "data": ..., "message": string|null }` zarfında döner. Hatalar `{ "success": false, "message": "...", "errors": {...}|null }`. Doğrulama hataları HTTP 422, bulunamadı 404.

| Metot | Endpoint | Açıklama |
|---|---|---|
| GET | `/api/v1/locales` | Aktif diller + varsayılan dil |
| GET | `/api/v1/home` | Ana sayfa için tek istekte tüm bloklar (hero/settings, öne çıkan kategori/ürün, katalog vitrin, öne çıkan blog, referanslar, SEO) |
| GET | `/api/v1/home/featured-products` | Öne çıkan kategoriler + ürünler |
| GET | `/api/v1/home/featured-blog-posts` | Öne çıkan blog yazıları |
| GET | `/api/v1/home/references` | Öne çıkan referans projeler |
| GET | `/api/v1/corporate` | Kurumsal sayfa içeriği |
| GET | `/api/v1/products/categories` | Kategori grid (alt kategori + ürün sayısıyla) |
| GET | `/api/v1/products/categories/{slug}` | Kategori detayı + alt kategoriler |
| GET | `/api/v1/products` | Ürün listesi (`?category=`, `?subcategory=`, `?featured=1`, `?q=`, `?per_page=`) |
| GET | `/api/v1/products/{slug}` | Ürün detayı (galeri, dokümanlar, teknik özellikler dahil) |
| GET | `/api/v1/blog/categories` | Blog kategorileri |
| GET | `/api/v1/blog/posts` | Blog yazı listesi (`?category=`, `?featured=1`, `?q=`, `?per_page=`) |
| GET | `/api/v1/blog/posts/{slug}` | Blog yazı detayı |
| GET | `/api/v1/catalogs` | Katalog listesi |
| GET | `/api/v1/catalogs/{slugOrId}` | Katalog detayı |
| GET | `/api/v1/catalogs/{slugOrId}/download` | Katalog PDF indirme |
| GET | `/api/v1/references` | Referans proje listesi |
| GET | `/api/v1/site-settings` | Site geneli ayarlar |
| GET | `/api/v1/seo/{page_key}?locale=tr` | Sayfa SEO meta verisi (`home`, `corporate`, `products`, `catalog`, `blog`, `contact`, `quote`, `references`) |
| POST | `/api/v1/contact/messages` | İletişim formu gönderimi (rate limit: 5/dk/IP) |
| POST | `/api/v1/quote-requests` | Teklif talebi gönderimi + dosya eki (rate limit: 5/dk/IP) |
| GET | `/api/v1/me` | *(Sanctum korumalı)* Giriş yapmış kullanıcı bilgisi |

Admin panel (`/admin`) tüm CRUD işlemlerini Filament üzerinden yapar; ayrı bir admin API yoktur.

## Güvenlik

- Panel erişimi `is_active=true` + en az bir rol gerektirir (`User::canAccessPanel`)
- `contact-form`, `quote-form`, `login` için ayrı rate limiter (`AppServiceProvider::registerRateLimiters`)
- Dosya yükleme: pdf, doc, docx, xls, xlsx, jpg, png; boyut `.env` → `MEDIA_MAX_DOCUMENT_SIZE` / `MEDIA_MAX_IMAGE_SIZE` (KB)
- Public form girdilerinde `strip_tags` ile temel XSS temizliği (`StoreContactMessageRequest`, `StoreQuoteRequestRequest`)
- CORS: `.env` → `CORS_ALLOWED_ORIGINS` (Next.js origin'leri)
- Policy bazlı yetkilendirme: her modül için `App\Policies\*Policy`

## Bilinen Sınırlamalar / Sonraki Adımlar

- `Product.features` / `technical_specs` dile bağlı değildir (basitlik için); ihtiyaç halinde translatable'a çevrilebilir.
- `Corporate.story_sections` tek parça metin olarak modellenmiştir; çok bölümlü hikaye gerekiyorsa Repeater'a çevrilebilir.
- Bu ortamda `composer install` / `php artisan test` / `php artisan migrate` çalıştırılamadı — ilk kurulumda küçük paket sürüm uyuşmazlıkları çıkabilir, `composer.json` içindeki sürüm kısıtlarını gerekirse gevşetin.
# fghavuz-admin

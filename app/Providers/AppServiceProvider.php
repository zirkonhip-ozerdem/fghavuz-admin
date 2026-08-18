<?php

namespace App\Providers;

use App\Support\Permissions;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        config([
            'livewire.temporary_file_upload.disk' => env('LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK', 'local'),
            'livewire.temporary_file_upload.rules' => env('LIVEWIRE_TEMPORARY_FILE_UPLOAD_RULES', 'required|file'),
            'livewire.temporary_file_upload.directory' => env('LIVEWIRE_TEMPORARY_FILE_UPLOAD_DIRECTORY', 'livewire-tmp'),
            'media-library.generate_thumbnails_for_temporary_uploads' => false,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Production'da https link uretimini zorla (proxy/load balancer arkasinda calisirken onemli)
        URL::forceScheme($this->app->environment('production') ? 'https' : (parse_url(config('app.url'), PHP_URL_SCHEME) ?: 'http'));

        // super_admin her zaman her izni gecer (yeni izin eklendiginde seeder'i unutsak bile kilitlenmeyiz)
        Gate::before(function ($user, string $ability) {
            return $user->hasRole(Permissions::ROLE_SUPER_ADMIN) ? true : null;
        });

        // Modul migration'larinda tekrar eden SEO kolonlarini tek satirda eklemek icin
        // ornek kullanim: $table->seoFields();
        Blueprint::macro('seoFields', function () {
            /** @var Blueprint $this */
            $this->string('seo_title')->nullable();
            $this->text('seo_description')->nullable();
            $this->string('seo_keywords')->nullable();
            $this->string('canonical_url')->nullable();
            $this->string('og_title')->nullable();
            $this->string('og_description')->nullable();
            $this->string('og_image')->nullable();
            $this->string('robots')->nullable()->default('index, follow');
        });

        // Yayin/siralama alanlarini tek satirda eklemek icin
        Blueprint::macro('publishable', function () {
            /** @var Blueprint $this */
            $this->boolean('is_active')->default(true);
            $this->unsignedInteger('sort_order')->default(0);
        });

        $this->registerRateLimiters();
    }

    /**
     * Guvenlik gereksinimi (Madde 15): iletisim formu, teklif formu ve login
     * icin ayri rate limit'ler. IP bazli, routes/api_v1.php icinde
     * throttle:contact-form / throttle:quote-form olarak kullanilir.
     */
    private function registerRateLimiters(): void
    {
        RateLimiter::for('contact-form', function ($request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('quote-form', function ($request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('login', function ($request) {
            return Limit::perMinute(5)->by($request->ip().'|'.$request->input('email'));
        });
    }
}

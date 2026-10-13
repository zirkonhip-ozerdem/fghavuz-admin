<?php

namespace Tests\Feature;

use App\Http\Middleware\PreventAdminIndexing;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AdminIndexingTest extends TestCase
{
    public function test_login_has_noindex_header_and_meta_tag(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_root_and_guest_admin_redirects_have_noindex_headers(): void
    {
        $this->get('/')
            ->assertRedirect('/admin')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

        $this->get('/admin')
            ->assertRedirect('/admin/login')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_missing_admin_page_has_noindex_header(): void
    {
        $this->get('/admin/missing-page')
            ->assertNotFound()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_custom_panel_path_is_protected(): void
    {
        Filament::getPanel('admin')->path('management');

        $response = (new PreventAdminIndexing())->handle(
            Request::create('/management/login'),
            fn () => new Response('Login'),
        );

        $this->assertSame('noindex, nofollow', $response->headers->get('X-Robots-Tag'));
    }

    public function test_public_api_and_product_images_remain_indexable(): void
    {
        foreach (['/api/v1/products', '/storage/products/cover/pump.jpg', '/admin-assets/app.css'] as $path) {
            $response = (new PreventAdminIndexing())->handle(
                Request::create($path),
                fn () => new Response('Public content'),
            );

            $this->assertFalse($response->headers->has('X-Robots-Tag'), $path);
        }
    }
}

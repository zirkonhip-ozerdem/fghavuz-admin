<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BlogPostResource;
use App\Models\BlogPost;
use App\Support\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    use ApiResponses;

    /**
     * GET /api/v1/blog/posts?category=slug&featured=1&q=&per_page=10
     */
    public function index(Request $request): JsonResponse
    {
        $query = BlogPost::query()->active()->published()->with('category')->orderByDesc('published_at');

        if ($categorySlug = $request->query('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        if ($search = $request->query('q')) {
            $locale = current_api_locale();
            $query->where("title->{$locale}", 'ilike', "%{$search}%");
        }

        $perPage = min((int) $request->query('per_page', 10), 50);
        $posts = $query->paginate($perPage)->appends($request->query());

        return $this->success([
            'items' => BlogPostResource::collection($posts->items()),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/blog/posts/{slug}
     */
    public function show(string $slug): JsonResponse
    {
        $post = BlogPost::query()->active()->published()->where('slug', $slug)->with('category')->first();

        if (! $post) {
            return $this->fail('Blog yazısı bulunamadı.', 404);
        }

        return $this->success(new BlogPostResource($post));
    }

    /**
     * GET /api/v1/home/featured-blog-posts
     */
    public function featured(): JsonResponse
    {
        $posts = BlogPost::query()->active()->published()->featured()->with('category')->limit(6)->get();

        return $this->success(BlogPostResource::collection($posts));
    }
}

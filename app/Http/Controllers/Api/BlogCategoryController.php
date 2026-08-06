<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BlogCategoryResource;
use App\Models\BlogCategory;
use App\Support\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

class BlogCategoryController extends Controller
{
    use ApiResponses;

    /**
     * GET /api/v1/blog/categories
     */
    public function index(): JsonResponse
    {
        $categories = BlogCategory::query()->active()->ordered()->withCount('posts')->get();

        return $this->success(BlogCategoryResource::collection($categories));
    }
}

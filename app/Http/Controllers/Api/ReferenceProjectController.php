<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ReferenceProjectResource;
use App\Models\ReferenceProject;
use App\Support\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

class ReferenceProjectController extends Controller
{
    use ApiResponses;

    /**
     * GET /api/v1/references
     */
    public function index(): JsonResponse
    {
        $references = ReferenceProject::query()->active()->ordered()->get();

        return $this->success(ReferenceProjectResource::collection($references));
    }

    /**
     * GET /api/v1/home/references
     */
    public function featured(): JsonResponse
    {
        $references = ReferenceProject::query()->active()->featured()->ordered()->limit(8)->get();

        return $this->success(ReferenceProjectResource::collection($references));
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CorporateResource;
use App\Models\Corporate;
use App\Support\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

class CorporateController extends Controller
{
    use ApiResponses;

    /**
     * GET /api/v1/corporate
     */
    public function show(): JsonResponse
    {
        return $this->success(new CorporateResource(Corporate::current()));
    }
}

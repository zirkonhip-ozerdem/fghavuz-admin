<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SiteSettingResource;
use App\Models\SiteSetting;
use App\Support\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

class SiteSettingController extends Controller
{
    use ApiResponses;

    /**
     * GET /api/v1/site-settings
     */
    public function show(): JsonResponse
    {
        return $this->success(
            new SiteSettingResource(SiteSetting::current())
        );
    }
}

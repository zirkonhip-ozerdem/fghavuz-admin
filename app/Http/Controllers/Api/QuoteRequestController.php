<?php

namespace App\Http\Controllers\Api;

use App\Enums\QuoteRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreQuoteRequestRequest;
use App\Models\QuoteRequest;
use App\Services\MediaService;
use App\Support\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

class QuoteRequestController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly MediaService $mediaService)
    {
    }

    /**
     * POST /api/v1/quote-requests
     * Rate limit: throttle:quote-form (bkz. routes/api_v1.php)
     */
    public function store(StoreQuoteRequestRequest $request): JsonResponse
    {
        $filePath = null;

        if ($request->hasFile('uploaded_file')) {
            $filePath = $this->mediaService->store($request->file('uploaded_file'), 'quote-requests');
        }

        $quote = QuoteRequest::create([
            ...$request->safe()->except('uploaded_file'),
            'uploaded_file' => $filePath,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'status' => QuoteRequestStatus::New,
        ]);

        return $this->created(['id' => $quote->id], 'Teklif talebiniz alındı, en kısa sürede size dönüş yapacağız.');
    }
}

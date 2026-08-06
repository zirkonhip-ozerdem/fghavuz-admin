<?php

namespace App\Http\Controllers\Api;

use App\Enums\ContactMessageStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Support\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

class ContactMessageController extends Controller
{
    use ApiResponses;

    /**
     * POST /api/v1/contact/messages
     * Rate limit: throttle:contact-form (bkz. routes/api_v1.php)
     */
    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        $message = ContactMessage::create([
            ...$request->validated(),
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'status' => ContactMessageStatus::New,
        ]);

        return $this->created(['id' => $message->id], 'Mesajınız alındı, en kısa sürede dönüş yapacağız.');
    }
}

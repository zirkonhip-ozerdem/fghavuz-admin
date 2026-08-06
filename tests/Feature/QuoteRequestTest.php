<?php

namespace Tests\Feature;

use App\Enums\QuoteRequestStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuoteRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_submits_quote_request_with_attachment(): void
    {
        Storage::fake('public');

        $payload = [
            'name' => 'Mehmet Demir',
            'company' => 'XYZ İnşaat',
            'email' => 'mehmet@example.com',
            'phone' => '+90 555 111 22 33',
            'country' => 'Türkiye',
            'city' => 'Antalya',
            'product_interest' => 'Havuz Pompaları',
            'message' => 'Otel projesi için teklif almak istiyorum.',
            'uploaded_file' => UploadedFile::fake()->create('sartname.pdf', 500, 'application/pdf'),
        ];

        $response = $this->postJson('/api/v1/quote-requests', $payload);

        $response->assertCreated()->assertJsonPath('success', true);

        $this->assertDatabaseHas('quote_requests', [
            'email' => 'mehmet@example.com',
            'status' => QuoteRequestStatus::New->value,
        ]);

        Storage::disk('public')->assertExists(
            \App\Models\QuoteRequest::first()->uploaded_file
        );
    }

    public function test_rejects_disallowed_file_type(): void
    {
        Storage::fake('public');

        $response = $this->postJson('/api/v1/quote-requests', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'phone' => '+90 555 000 00 00',
            'message' => 'Test mesajı',
            'uploaded_file' => UploadedFile::fake()->create('virus.exe', 100, 'application/x-msdownload'),
        ]);

        $response->assertStatus(422)->assertJsonPath('success', false);
    }
}

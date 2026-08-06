<?php

namespace Tests\Feature;

use App\Enums\ContactMessageStatus;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_submits_contact_message_successfully(): void
    {
        $payload = [
            'name' => 'Ayşe Yılmaz',
            'email' => 'ayse@example.com',
            'phone' => '+90 555 000 00 00',
            'company' => 'ABC Ltd',
            'subject' => 'Bilgi Talebi',
            'message' => 'Merhaba, ürünleriniz hakkında bilgi almak istiyorum.',
            'source_page' => '/iletisim',
        ];

        $response = $this->postJson('/api/v1/contact/messages', $payload);

        $response->assertCreated()->assertJsonPath('success', true);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'ayse@example.com',
            'status' => ContactMessageStatus::New->value,
        ]);
    }

    public function test_validation_fails_without_required_fields(): void
    {
        $response = $this->postJson('/api/v1/contact/messages', []);

        $response->assertStatus(422)->assertJsonPath('success', false);
    }

    public function test_strips_html_tags_from_message(): void
    {
        $this->postJson('/api/v1/contact/messages', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'message' => '<script>alert(1)</script>Merhaba',
        ])->assertCreated();

        $message = ContactMessage::first();

        $this->assertStringNotContainsString('<script>', $message->message);
    }
}

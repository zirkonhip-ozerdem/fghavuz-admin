<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:150'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
            'source_page' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Basit XSS koruması: metin alanlarindaki HTML/script etiketlerini temizle.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(collect($this->only(['name', 'company', 'subject', 'message', 'source_page']))
            ->map(fn ($value) => is_string($value) ? strip_tags($value) : $value)
            ->toArray());
    }

    public function messages(): array
    {
        return [
            'name.required' => 'İsim alanı zorunludur.',
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi girin.',
            'message.required' => 'Mesaj alanı zorunludur.',
        ];
    }
}

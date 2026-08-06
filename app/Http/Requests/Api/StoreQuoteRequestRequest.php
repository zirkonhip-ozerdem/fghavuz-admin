<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'company' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'product_interest' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
            'uploaded_file' => [
                'nullable', 'file', 'max:'.env('MEDIA_MAX_DOCUMENT_SIZE', 20480),
                'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(collect($this->only(['name', 'company', 'country', 'city', 'product_interest', 'message']))
            ->map(fn ($value) => is_string($value) ? strip_tags($value) : $value)
            ->toArray());
    }

    public function messages(): array
    {
        return [
            'name.required' => 'İsim alanı zorunludur.',
            'email.required' => 'E-posta alanı zorunludur.',
            'phone.required' => 'Telefon alanı zorunludur.',
            'message.required' => 'Mesaj alanı zorunludur.',
            'uploaded_file.mimes' => 'Dosya türü desteklenmiyor. İzin verilenler: pdf, doc, docx, xls, xlsx, jpg, png.',
            'uploaded_file.max' => 'Dosya boyutu çok büyük.',
        ];
    }
}

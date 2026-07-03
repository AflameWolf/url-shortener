<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LinkStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'original_url' => [
                'required',
                'string',
                'max:2048',
                'url',
            ],
            'title' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',
        ];
    }

    public function messages(): array
    {
        return [
            'original_url.required' => 'Введите URL',
            'original_url.url' => 'Введите корректный URL',
            'original_url.max' => 'URL слишком длинный',
            'expires_at.after' => 'Дата истечения должна быть в будущем',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LinkUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->id() === $this->route('link')->user_id;
    }

    public function rules(): array
    {
        return [
            'original_url' => 'nullable|string|max:2048|url',
            'title' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',
            'is_active' => 'nullable|boolean',
        ];
    }
}

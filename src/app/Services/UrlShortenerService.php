<?php

namespace App\Services;

use Illuminate\Support\Str;

class UrlShortenerService
{
    private const CHARACTERS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const LENGTH = 6;

    public function generateUniqueCode(): string
    {
        do {
            $code = $this->generateCode();
        } while ($this->codeExists($code));

        return $code;
    }

    private function generateCode(): string
    {
        return Str::random(self::LENGTH);
    }

    private function codeExists(string $code): bool
    {
        return \App\Models\Link::where('short_code', $code)->exists();
    }

}

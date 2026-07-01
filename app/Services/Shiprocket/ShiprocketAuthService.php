<?php

namespace App\Services\Shiprocket;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ShiprocketAuthService
{
    public function getToken(): string
    {
        $cacheKey = 'shiprocket.auth_token.'.md5((string) config('shiprocket.email'));

        $cached = Cache::get($cacheKey);

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $email = config('shiprocket.email');
        $password = config('shiprocket.password');

        if (! is_string($email) || $email === '' || ! is_string($password) || $password === '') {
            throw new RuntimeException('Shiprocket API credentials are not configured.');
        }

        $response = Http::acceptJson()
            ->post($this->baseUrl().'/v1/external/auth/login', [
                'email' => $email,
                'password' => $password,
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Shiprocket authentication failed: '.$response->body());
        }

        $token = $response->json('token');

        if (! is_string($token) || $token === '') {
            throw new RuntimeException('Shiprocket authentication returned no token.');
        }

        // Shiprocket tokens are valid for 10 days; refresh early.
        Cache::put($cacheKey, $token, now()->addDays(9));

        return $token;
    }

    public function clearCachedToken(): void
    {
        Cache::forget('shiprocket.auth_token.'.md5((string) config('shiprocket.email')));
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('shiprocket.base_url', 'https://apiv2.shiprocket.in'), '/');
    }
}

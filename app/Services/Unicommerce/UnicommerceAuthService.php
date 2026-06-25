<?php

namespace App\Services\Unicommerce;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class UnicommerceAuthService
{
    public function getAccessToken(): string
    {
        $cacheKey = 'unicommerce.access_token.'.config('unicommerce.tenant');

        $cached = Cache::get($cacheKey);

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $response = Http::acceptJson()
            ->get($this->baseUrl().'/oauth/token', [
                'grant_type' => 'password',
                'client_id' => config('unicommerce.client_id'),
                'username' => config('unicommerce.username'),
                'password' => config('unicommerce.password'),
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Unicommerce authentication failed: '.$response->body(),
            );
        }

        $accessToken = $response->json('access_token');
        $expiresIn = (int) $response->json('expires_in', 3600);

        if (! is_string($accessToken) || $accessToken === '') {
            throw new RuntimeException('Unicommerce authentication returned no access token.');
        }

        Cache::put($cacheKey, $accessToken, max(60, $expiresIn - 60));

        return $accessToken;
    }

    public function clearCachedToken(): void
    {
        Cache::forget('unicommerce.access_token.'.config('unicommerce.tenant'));
    }

    private function baseUrl(): string
    {
        $tenant = config('unicommerce.tenant');

        if (! is_string($tenant) || $tenant === '') {
            throw new RuntimeException('Unicommerce tenant is not configured.');
        }

        return 'https://'.$tenant.'.unicommerce.com';
    }
}

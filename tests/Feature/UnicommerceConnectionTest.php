<?php

namespace Tests\Feature;

use App\Services\Unicommerce\UnicommerceClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UnicommerceConnectionTest extends TestCase
{
    public function test_it_fetches_an_access_token_from_unicommerce_oauth(): void
    {
        config([
            'unicommerce.enabled' => true,
            'unicommerce.tenant' => 'staging',
            'unicommerce.facility_code' => '01',
            'unicommerce.username' => 'api@oakter.com',
            'unicommerce.password' => 'secret',
            'unicommerce.client_id' => 'my-trusted-client',
        ]);

        Http::fake([
            'https://staging.unicommerce.com/oauth/token*' => Http::response([
                'access_token' => 'test-access-token',
                'token_type' => 'bearer',
                'expires_in' => 3600,
            ], 200),
        ]);

        $result = app(UnicommerceClient::class)->testConnection();

        $this->assertTrue($result['successful']);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/oauth/token')
                && $request['grant_type'] === 'password'
                && $request['client_id'] === 'my-trusted-client';
        });
    }
}

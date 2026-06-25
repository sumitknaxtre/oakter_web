<?php

namespace App\Services\Unicommerce;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class UnicommerceClient
{
    public function __construct(
        private readonly UnicommerceAuthService $authService,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function createSaleOrder(array $payload): array
    {
        $response = $this->request('post', '/services/rest/v1/oms/saleOrder/create', $payload);

        return $response->json() ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    public function testConnection(): array
    {
        $this->authService->getAccessToken();

        return [
            'successful' => true,
            'message' => 'Unicommerce OAuth connection successful.',
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function request(string $method, string $path, array $payload = []): Response
    {
        $response = Http::acceptJson()
            ->withHeaders($this->headers())
            ->{$method}($this->baseUrl().$path, $payload);

        if ($response->status() === 401) {
            $this->authService->clearCachedToken();

            $response = Http::acceptJson()
                ->withHeaders($this->headers())
                ->{$method}($this->baseUrl().$path, $payload);
        }

        if ($response->failed()) {
            throw new RuntimeException(
                'Unicommerce API request failed ['.$path.']: '.$response->body(),
            );
        }

        return $response;
    }

    /**
     * @return array<string, string>
     */
    private function headers(): array
    {
        $facilityCode = config('unicommerce.facility_code');

        if (! is_string($facilityCode) || $facilityCode === '') {
            throw new RuntimeException('Unicommerce facility code is not configured.');
        }

        return [
            'Authorization' => 'bearer '.$this->authService->getAccessToken(),
            'Facility' => $facilityCode,
            'Content-Type' => 'application/json',
        ];
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

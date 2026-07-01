<?php

namespace App\Services\Shiprocket;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ShiprocketClient
{
    public function __construct(
        private readonly ShiprocketAuthService $authService,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function createAdhocOrder(array $payload): array
    {
        $response = $this->request('post', '/v1/external/orders/create/adhoc', $payload);

        return $response->json() ?? [];
    }

    /**
     * @param  list<int>  $shiprocketOrderIds
     */
    public function cancelOrders(array $shiprocketOrderIds): void
    {
        $response = $this->request('post', '/v1/external/orders/cancel', [
            'ids' => array_values($shiprocketOrderIds),
        ]);

        if ($response->status() !== 200 && $response->status() !== 204) {
            throw new RuntimeException('Shiprocket order cancellation failed: '.$response->body());
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function testConnection(): array
    {
        $this->authService->getToken();

        return [
            'successful' => true,
            'message' => 'Shiprocket authentication successful.',
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function request(string $method, string $path, array $payload = []): Response
    {
        $response = Http::acceptJson()
            ->withToken($this->authService->getToken())
            ->{$method}($this->baseUrl().$path, $payload);

        if ($response->status() === 401) {
            $this->authService->clearCachedToken();

            $response = Http::acceptJson()
                ->withToken($this->authService->getToken())
                ->{$method}($this->baseUrl().$path, $payload);
        }

        if ($response->failed()) {
            throw new RuntimeException(
                'Shiprocket API request failed ['.$path.']: '.$response->body(),
            );
        }

        return $response;
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('shiprocket.base_url', 'https://apiv2.shiprocket.in'), '/');
    }
}

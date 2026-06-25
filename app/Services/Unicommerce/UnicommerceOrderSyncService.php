<?php

namespace App\Services\Unicommerce;

use App\Models\Order;
use App\Support\UnicommerceSyncStatus;
use RuntimeException;

class UnicommerceOrderSyncService
{
    public function __construct(
        private readonly UnicommerceClient $client,
        private readonly UnicommerceOrderMapper $mapper,
    ) {}

    public function sync(Order $order): void
    {
        if ($order->unicommerce_sync_status === UnicommerceSyncStatus::Synced) {
            return;
        }

        $saleOrderCode = $order->unicommerce_sale_order_code
            ?: $this->mapper->saleOrderCode($order);

        $order->update([
            'unicommerce_sale_order_code' => $saleOrderCode,
            'unicommerce_sync_status' => UnicommerceSyncStatus::Pending,
        ]);

        $payload = $this->mapper->toCreateSaleOrderPayload($order->fresh());
        $response = $this->client->createSaleOrder($payload);

        if (! ($response['successful'] ?? false)) {
            $errorMessage = $this->formatErrorMessage($response);

            $order->update([
                'unicommerce_sync_status' => UnicommerceSyncStatus::Failed,
                'unicommerce_last_error' => $errorMessage,
            ]);

            throw new RuntimeException($errorMessage);
        }

        $order->update([
            'unicommerce_sale_order_code' => $response['saleOrderDetailDTO']['code'] ?? $saleOrderCode,
            'unicommerce_sync_status' => UnicommerceSyncStatus::Synced,
            'unicommerce_synced_at' => now(),
            'unicommerce_last_error' => null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function formatErrorMessage(array $response): string
    {
        $message = $response['message'] ?? 'Unicommerce sale order creation failed.';

        $errors = $response['errors'] ?? [];

        if (! is_array($errors) || $errors === []) {
            return (string) $message;
        }

        $details = collect($errors)
            ->map(function ($error) {
                if (! is_array($error)) {
                    return null;
                }

                return trim(($error['message'] ?? $error['description'] ?? '').' '.($error['fieldName'] ?? ''));
            })
            ->filter()
            ->implode(' | ');

        return $details !== '' ? $message.' '.$details : (string) $message;
    }
}

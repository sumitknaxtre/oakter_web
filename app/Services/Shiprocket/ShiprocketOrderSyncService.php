<?php

namespace App\Services\Shiprocket;

use App\Models\Order;
use App\Support\ShiprocketSyncStatus;
use RuntimeException;

class ShiprocketOrderSyncService
{
    public function __construct(
        private readonly ShiprocketClient $client,
        private readonly ShiprocketOrderMapper $mapper,
    ) {}

    public function sync(Order $order): void
    {
        if ($order->shiprocket_sync_status === ShiprocketSyncStatus::Synced) {
            return;
        }

        if ($order->shiprocket_sync_status === ShiprocketSyncStatus::Cancelled) {
            return;
        }

        $reference = $this->mapper->referenceOrderId($order);

        $order->update([
            'shiprocket_reference' => $reference,
            'shiprocket_sync_status' => ShiprocketSyncStatus::Pending,
        ]);

        $payload = $this->mapper->toCreateAdhocOrderPayload($order->fresh());
        $response = $this->client->createAdhocOrder($payload);

        $shiprocketOrderId = $response['order_id'] ?? null;

        if (! is_numeric($shiprocketOrderId)) {
            $errorMessage = (string) ($response['message'] ?? 'Shiprocket did not return an order ID.');

            $order->update([
                'shiprocket_sync_status' => ShiprocketSyncStatus::Failed,
                'shiprocket_last_error' => $errorMessage,
            ]);

            throw new RuntimeException($errorMessage);
        }

        $order->update([
            'shiprocket_reference' => $reference,
            'shiprocket_order_id' => (int) $shiprocketOrderId,
            'shiprocket_shipment_id' => isset($response['shipment_id']) ? (int) $response['shipment_id'] : null,
            'shiprocket_sync_status' => ShiprocketSyncStatus::Synced,
            'shiprocket_synced_at' => now(),
            'shiprocket_last_error' => null,
        ]);
    }
}

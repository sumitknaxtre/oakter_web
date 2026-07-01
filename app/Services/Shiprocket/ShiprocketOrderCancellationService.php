<?php

namespace App\Services\Shiprocket;

use App\Models\Order;
use App\Support\ShiprocketSyncStatus;

class ShiprocketOrderCancellationService
{
    public function __construct(
        private readonly ShiprocketClient $client,
    ) {}

    public function cancel(Order $order): void
    {
        if ($order->shiprocket_sync_status === ShiprocketSyncStatus::Cancelled) {
            return;
        }

        if ($order->shiprocket_order_id === null) {
            return;
        }

        $this->client->cancelOrders([(int) $order->shiprocket_order_id]);

        $order->update([
            'shiprocket_sync_status' => ShiprocketSyncStatus::Cancelled,
            'shiprocket_cancelled_at' => now(),
            'shiprocket_last_error' => null,
        ]);
    }
}

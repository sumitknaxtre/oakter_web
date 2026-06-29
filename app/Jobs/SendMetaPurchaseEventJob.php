<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Meta\MetaPurchaseEventService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Sends Meta CAPI Purchase asynchronously so payment verification stays fast.
 * Failures are logged inside MetaConversionApiService and do not affect the order.
 */
class SendMetaPurchaseEventJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly int $orderId,
        public readonly ?string $clientIp,
        public readonly ?string $userAgent,
        public readonly ?string $fbp,
        public readonly ?string $fbc,
        public readonly ?string $eventSourceUrl = null,
    ) {}

    public function handle(MetaPurchaseEventService $purchaseEventService): void
    {
        $order = Order::query()->find($this->orderId);

        if ($order === null) {
            return;
        }

        $purchaseEventService->sendPurchase(
            $order,
            $this->clientIp,
            $this->userAgent,
            $this->fbp,
            $this->fbc,
            $this->eventSourceUrl,
        );
    }
}

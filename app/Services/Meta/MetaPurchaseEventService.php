<?php

namespace App\Services\Meta;

use App\Jobs\SendMetaPurchaseEventJob;
use App\Models\Order;

/**
 * Orchestrates Meta Purchase tracking: event_id generation context and CAPI dispatch.
 */
class MetaPurchaseEventService
{
    public function __construct(
        private readonly MetaConversionApiService $conversionApi,
    ) {}

    /**
     * Queue a server-side Purchase event. Never throws; checkout must not fail if Meta is down.
     */
    public function dispatchPurchase(
        Order $order,
        ?string $clientIp,
        ?string $userAgent,
        ?string $fbp,
        ?string $fbc,
        ?string $eventSourceUrl = null,
    ): void {
        if (! $this->conversionApi->isEnabled()) {
            return;
        }

        if ($order->meta_purchase_sent_at !== null) {
            return;
        }

        if (! is_string($order->meta_event_id) || $order->meta_event_id === '') {
            return;
        }

        SendMetaPurchaseEventJob::dispatch(
            $order->id,
            $clientIp,
            $userAgent,
            $fbp,
            $fbc,
            $eventSourceUrl,
        );
    }

    /**
     * Build and send Purchase to Meta CAPI. Called from the queued job.
     */
    public function sendPurchase(
        Order $order,
        ?string $clientIp,
        ?string $userAgent,
        ?string $fbp,
        ?string $fbc,
        ?string $eventSourceUrl = null,
    ): void {
        if (! $this->conversionApi->isEnabled()) {
            return;
        }

        $order->refresh();

        if ($order->meta_purchase_sent_at !== null) {
            return;
        }

        if (! $order->isPaid() || ! is_string($order->meta_event_id) || $order->meta_event_id === '') {
            return;
        }

        $order->loadMissing('user');

        $event = $this->buildPurchaseEvent(
            $order,
            $clientIp,
            $userAgent,
            $fbp,
            $fbc,
            $eventSourceUrl,
        );

        $sent = $this->conversionApi->sendEvent($event);

        if ($sent) {
            $order->update(['meta_purchase_sent_at' => now()]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function buildPurchaseEvent(
        Order $order,
        ?string $clientIp,
        ?string $userAgent,
        ?string $fbp,
        ?string $fbc,
        ?string $eventSourceUrl = null,
    ): array {
        $shipping = $order->shipping_snapshot ?? [];
        $user = $order->user;

        $userData = array_filter([
            'em' => array_filter([MetaHasher::email($user?->email)]),
            'ph' => array_filter([MetaHasher::phone($shipping['phone'] ?? $user?->phone)]),
            'fn' => array_filter([MetaHasher::name($shipping['first_name'] ?? $user?->first_name)]),
            'ln' => array_filter([MetaHasher::name($shipping['last_name'] ?? $user?->last_name)]),
            'ct' => array_filter([MetaHasher::city($shipping['city'] ?? null)]),
            'st' => array_filter([MetaHasher::state($shipping['state'] ?? null)]),
            'zp' => array_filter([MetaHasher::zip($shipping['pincode'] ?? null)]),
            'country' => array_filter([MetaHasher::country($shipping['country'] ?? 'India')]),
            'client_ip_address' => $clientIp,
            'client_user_agent' => $userAgent,
            'fbp' => $fbp,
            'fbc' => $fbc,
        ], fn ($value) => $value !== null && $value !== []);

        return [
            'event_name' => 'Purchase',
            'event_time' => $order->paid_at?->timestamp ?? now()->timestamp,
            'event_id' => $order->meta_event_id,
            'action_source' => 'website',
            'event_source_url' => $eventSourceUrl ?? url('/'),
            'user_data' => $userData,
            'custom_data' => MetaProductPayload::capiCustomData($order),
        ];
    }
}

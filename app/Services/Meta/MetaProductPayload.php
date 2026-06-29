<?php

namespace App\Services\Meta;

use App\Models\Order;
use App\Models\Product;

/**
 * Builds consistent product payloads for Meta Pixel (browser) and CAPI (server).
 */
class MetaProductPayload
{
    /**
     * @return array<string, mixed>|null
     */
    public static function fromConfigKey(string $configKey): ?array
    {
        $product = Product::query()->where('config_key', $configKey)->first();

        if ($product === null) {
            return null;
        }

        return self::fromProduct($product);
    }

    /**
     * @param  array<string, mixed>  $checkoutProduct
     * @return array<string, mixed>
     */
    public static function fromCheckoutProduct(array $checkoutProduct): array
    {
        $product = Product::query()->find($checkoutProduct['id'] ?? null);

        if ($product instanceof Product) {
            return self::fromProduct($product);
        }

        $value = isset($checkoutProduct['amount_paise'])
            ? round(((int) $checkoutProduct['amount_paise']) / 100, 2)
            : 0.0;

        $contentId = $checkoutProduct['slug'] ?? $checkoutProduct['key'] ?? 'product';

        return self::pixelData(
            contentId: (string) $contentId,
            contentName: (string) ($checkoutProduct['order_name'] ?? $contentId),
            value: $value,
            currency: 'INR',
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function fromOrder(Order $order): array
    {
        $snapshot = $order->product_snapshot ?? [];
        $contentId = $snapshot['sku'] ?? $snapshot['slug'] ?? (string) $order->product_id;
        $contentName = $order->product_name;

        return self::pixelData(
            contentId: (string) $contentId,
            contentName: $contentName,
            value: round($order->amount_paise / 100, 2),
            currency: $order->currency ?: 'INR',
            orderId: (string) $order->id,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function fromProduct(Product $product): array
    {
        $contentId = $product->sku ?: $product->slug;

        return self::pixelData(
            contentId: $contentId,
            contentName: $product->name,
            value: round($product->amount_paise / 100, 2),
            currency: $product->currency ?: 'INR',
        );
    }

    /**
     * Browser Pixel payload (fbq track parameters).
     *
     * @return array<string, mixed>
     */
    public static function pixelData(
        string $contentId,
        string $contentName,
        float $value,
        string $currency = 'INR',
        ?string $orderId = null,
    ): array {
        $payload = [
            'content_ids' => [$contentId],
            'content_name' => $contentName,
            'content_type' => 'product',
            'contents' => [
                [
                    'id' => $contentId,
                    'quantity' => 1,
                    'item_price' => $value,
                ],
            ],
            'currency' => strtoupper($currency),
            'value' => $value,
            'num_items' => 1,
        ];

        if ($orderId !== null) {
            $payload['order_id'] = $orderId;
        }

        return $payload;
    }

    /**
     * CAPI custom_data block for Purchase events.
     *
     * @return array<string, mixed>
     */
    public static function capiCustomData(Order $order): array
    {
        $pixel = self::fromOrder($order);

        return [
            'currency' => $pixel['currency'],
            'value' => $pixel['value'],
            'content_type' => $pixel['content_type'],
            'contents' => $pixel['contents'],
            'num_items' => $pixel['num_items'],
            'order_id' => (string) $order->id,
        ];
    }
}

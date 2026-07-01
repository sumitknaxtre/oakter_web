<?php

namespace App\Services\Shiprocket;

use App\Models\Order;
use RuntimeException;

class ShiprocketOrderMapper
{
    /**
     * @return array<string, mixed>
     */
    public function toCreateAdhocOrderPayload(Order $order): array
    {
        $order->loadMissing(['user', 'product']);

        $pickupLocation = config('shiprocket.pickup_location');

        if (! is_string($pickupLocation) || $pickupLocation === '') {
            throw new RuntimeException('Shiprocket pickup location is not configured.');
        }

        $billing = $order->billing_same_as_shipping
            ? ($order->shipping_snapshot ?? [])
            : ($order->billing_snapshot ?? []);

        $shipping = $order->shipping_snapshot ?? [];
        $email = $order->user?->email;

        if (! is_string($email) || $email === '') {
            throw new RuntimeException('Customer email is required for Shiprocket order #'.$order->id.'.');
        }

        $reference = $this->referenceOrderId($order);
        $sku = $order->product_snapshot['sku'] ?? $order->product?->sku;
        $sku = is_string($sku) && $sku !== '' ? $sku : 'OAKTER-'.$order->product_id;
        $package = $this->resolvePackageDetails($order);

        $payload = [
            'order_id' => $reference,
            'order_date' => ($order->paid_at ?? now())->format('Y-m-d H:i'),
            'pickup_location' => $pickupLocation,
            'comment' => 'Oakter website order #'.$order->id,
            'billing_customer_name' => $this->firstName($billing, $order),
            'billing_last_name' => $this->lastName($billing),
            'billing_address' => $billing['address_line1'] ?? '',
            'billing_address_2' => $billing['address_line2'] ?? '',
            'billing_city' => $billing['city'] ?? '',
            'billing_pincode' => (int) ($billing['pincode'] ?? 0),
            'billing_state' => $billing['state'] ?? '',
            'billing_country' => $billing['country'] ?? 'India',
            'billing_email' => $email,
            'billing_phone' => $this->phoneDigits($billing['phone'] ?? $order->phone),
            'shipping_is_billing' => $order->billing_same_as_shipping,
            'order_items' => [
                [
                    'name' => $order->product_name,
                    'sku' => $sku,
                    'units' => 1,
                    'selling_price' => (int) round($order->subtotal_paise / 100),
                    'discount' => (int) round($order->discount_paise / 100),
                ],
            ],
            'payment_method' => 'Prepaid',
            'shipping_charges' => (int) round($order->shipping_charges / 100),
            'giftwrap_charges' => 0,
            'transaction_charges' => 0,
            'total_discount' => (int) round($order->discount_paise / 100),
            'sub_total' => (int) round($order->amount_paise / 100),
            'length' => $package['length'],
            'breadth' => $package['breadth'],
            'height' => $package['height'],
            'weight' => $package['weight'],
        ];

        $channelId = config('shiprocket.channel_id');

        if (is_string($channelId) && $channelId !== '') {
            $payload['channel_id'] = (int) $channelId;
        }

        if (! $order->billing_same_as_shipping) {
            $payload['shipping_customer_name'] = $this->firstName($shipping, $order);
            $payload['shipping_last_name'] = $this->lastName($shipping);
            $payload['shipping_address'] = $shipping['address_line1'] ?? '';
            $payload['shipping_address_2'] = $shipping['address_line2'] ?? '';
            $payload['shipping_city'] = $shipping['city'] ?? '';
            $payload['shipping_pincode'] = (int) ($shipping['pincode'] ?? 0);
            $payload['shipping_state'] = $shipping['state'] ?? '';
            $payload['shipping_country'] = $shipping['country'] ?? 'India';
            $payload['shipping_email'] = $email;
            $payload['shipping_phone'] = $this->phoneDigits($shipping['phone'] ?? $order->phone);
        }

        return $payload;
    }

    public function referenceOrderId(Order $order): string
    {
        $prefix = config('shiprocket.order_id_prefix', 'OAKTER');

        return $prefix.'-'.$order->id;
    }

    /**
     * @return array{weight: float, length: float, breadth: float, height: float}
     */
    public function resolvePackageDetails(Order $order): array
    {
        $snapshot = $order->product_snapshot ?? [];

        $weight = $snapshot['package_weight_kg'] ?? $order->product?->package_weight_kg;
        $length = $snapshot['package_length_cm'] ?? $order->product?->package_length_cm;
        $breadth = $snapshot['package_breadth_cm'] ?? $order->product?->package_breadth_cm;
        $height = $snapshot['package_height_cm'] ?? $order->product?->package_height_cm;

        if ($weight === null || $length === null || $breadth === null || $height === null) {
            throw new RuntimeException(
                'Package weight and dimensions are not configured for '.$order->product_name.'. Update the product in admin.',
            );
        }

        return [
            'weight' => (float) $weight,
            'length' => (float) $length,
            'breadth' => (float) $breadth,
            'height' => (float) $height,
        ];
    }

    /**
     * @param  array<string, mixed>  $address
     */
    private function firstName(array $address, Order $order): string
    {
        $name = trim((string) ($address['first_name'] ?? ''));

        if ($name !== '') {
            return $name;
        }

        return strtok(trim($order->customer_name), ' ') ?: 'Customer';
    }

    /**
     * @param  array<string, mixed>  $address
     */
    private function lastName(array $address): string
    {
        $lastName = trim((string) ($address['last_name'] ?? ''));

        return $lastName !== '' ? $lastName : '.';
    }

    private function phoneDigits(?string $phone): int
    {
        $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';

        if (strlen($digits) > 10) {
            $digits = substr($digits, -10);
        }

        if (strlen($digits) !== 10) {
            throw new RuntimeException('A valid 10-digit phone number is required for Shiprocket.');
        }

        return (int) $digits;
    }
}

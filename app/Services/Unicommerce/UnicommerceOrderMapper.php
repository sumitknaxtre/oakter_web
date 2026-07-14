<?php

namespace App\Services\Unicommerce;

use App\Models\Order;
use RuntimeException;

class UnicommerceOrderMapper
{
    private const SHIPPING_ADDRESS_ID = 'shipping';

    private const BILLING_ADDRESS_ID = 'billing';

    /**
     * @return array{saleOrder: array<string, mixed>}
     */
    public function toCreateSaleOrderPayload(Order $order): array
    {
        $order->loadMissing(['user', 'product']);

        $sku = $order->product?->sku;

        if (! is_string($sku) || $sku === '') {
            throw new RuntimeException('Product SKU is not configured for order #'.$order->id.'.');
        }

        $shipping = $order->shipping_snapshot ?? [];
        $billing = $order->billing_same_as_shipping
            ? $shipping
            : ($order->billing_snapshot ?? []);

        $saleOrderCode = $this->saleOrderCode($order);
        $amount = $this->rupees($order->amount_paise);
        $subtotal = $this->rupees($order->subtotal_paise);
        $discount = $this->rupees($order->discount_paise);
        $shippingCharges = $this->rupees($order->shipping_charges);

        return [
            'saleOrder' => [
                'code' => $saleOrderCode,
                'displayOrderCode' => $this->displayOrderCode($order),
                'displayOrderDateTime' => $order->paid_at?->toIso8601String() ?? now()->toIso8601String(),
                'customerName' => $order->customer_name,
                'channel' => config('unicommerce.channel'),
                'notificationEmail' => $order->user?->email,
                'notificationMobile' => $order->phone,
                'cashOnDelivery' => false,
                'paymentInstrument' => $this->paymentInstrument($order->payment_method),
                'currencyCode' => $order->currency,
                'addresses' => [
                    $this->addressPayload(self::SHIPPING_ADDRESS_ID, $shipping, $order),
                    $this->addressPayload(self::BILLING_ADDRESS_ID, $billing, $order),
                ],
                'shippingAddress' => [
                    'referenceId' => self::SHIPPING_ADDRESS_ID,
                ],
                'billingAddress' => [
                    'referenceId' => self::BILLING_ADDRESS_ID,
                ],
                'saleOrderItems' => [
                    [
                        'code' => $saleOrderCode.'-1',
                        'itemSku' => $sku,
                        'shippingMethodCode' => config('unicommerce.shipping_method', 'STD'),
                        'packetNumber' => 1,
                        'giftWrap' => false,
                        'facilityCode' => '',
                        'totalPrice' => $amount,
                        'sellingPrice' => $subtotal,
                        'prepaidAmount' => $amount,
                        'discount' => $discount,
                        'shippingCharges' => $shippingCharges,
                    ],
                ],
            ],
        ];
    }

    public function saleOrderCode(Order $order): string
    {
        $prefix = config('unicommerce.order_code_prefix', 'OAKTER');

        return $prefix.'-'.$order->id;
    }

    public function displayOrderCode(Order $order): string
    {
        $prefix = config('unicommerce.display_order_code_prefix', 'NEW');

        return $prefix.$order->id;
    }

    /**
     * @param  array<string, mixed>  $address
     * @return array<string, mixed>
     */
    private function addressPayload(string $id, array $address, Order $order): array
    {
        return [
            'id' => $id,
            'name' => trim(($address['first_name'] ?? '').' '.($address['last_name'] ?? '')),
            'addressLine1' => $address['address_line1'] ?? '',
            'addressLine2' => $address['address_line2'] ?? '',
            'city' => $address['city'] ?? '',
            'state' => $address['state'] ?? '',
            'country' => $address['country'] ?? 'India',
            'pincode' => $address['pincode'] ?? '',
            'phone' => $address['phone'] ?? $order->phone ?? '',
            'email' => $order->user?->email,
        ];
    }

    private function paymentInstrument(?string $paymentMethod): string
    {
        $method = strtolower((string) $paymentMethod);

        if (str_contains($method, 'card')) {
            return 'CREDIT_CARD';
        }

        if ($method === 'upi' || str_contains($method, 'wallet')) {
            return 'WALLET';
        }

        if (str_contains($method, 'net banking') || str_contains($method, 'nach')) {
            return 'NET_BANKING';
        }

        return 'NET_BANKING';
    }

    private function rupees(int $paise): float
    {
        return round($paise / 100, 2);
    }
}

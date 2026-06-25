<?php

namespace App\Exports;

use App\Models\Order;
use App\Support\OrderPaymentStatus;

class AbandonedOrdersExport
{
    /**
     * @return list<string>
     */
    public static function headers(): array
    {
        return [
            'Attempt ID',
            'Attempt date',
            'Customer name',
            'Email',
            'Phone',
            'Product',
            'Subtotal (INR)',
            'Discount (INR)',
            'Coupon',
            'Amount (INR)',
            'Payment status',
            'Razorpay order ID',
            'Shipping address line 1',
            'Shipping address line 2',
            'City',
            'State',
            'PIN code',
            'Country',
        ];
    }

    /**
     * @return list<int|float|string|null>
     */
    public static function row(Order $order): array
    {
        $shipping = $order->shipping_snapshot ?? [];

        return [
            $order->id,
            $order->created_at?->format('d M Y, h:i A'),
            $order->customer_name,
            $order->user?->email,
            $order->phone,
            $order->product_name,
            number_format($order->subtotal_paise / 100, 2, '.', ''),
            number_format($order->discount_paise / 100, 2, '.', ''),
            $order->coupon_code,
            number_format($order->amount_paise / 100, 2, '.', ''),
            OrderPaymentStatus::label((int) $order->payment_status),
            $order->razorpay_order_id,
            $shipping['address_line1'] ?? null,
            $shipping['address_line2'] ?? null,
            $shipping['city'] ?? null,
            $shipping['state'] ?? null,
            $shipping['pincode'] ?? null,
            $shipping['country'] ?? null,
        ];
    }
}

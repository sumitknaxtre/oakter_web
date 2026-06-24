<?php

namespace App\Exports;

use App\Models\Order;
use App\Support\OrderFulfillmentStatus;
use App\Support\OrderPaymentStatus;

class OrdersExport
{
    /**
     * @return list<string>
     */
    public static function headers(): array
    {
        return [
            'Order ID',
            'Order date',
            'Paid at',
            'Customer name',
            'Email',
            'Phone',
            'Product',
            'Subtotal (INR)',
            'Discount (INR)',
            'Coupon',
            'Shipping (INR)',
            'Tax (INR)',
            'Amount (INR)',
            'Payment status',
            'Payment method',
            'Fulfillment status',
            'Razorpay order ID',
            'Payment ID',
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
            $order->paid_at?->format('d M Y, h:i A'),
            $order->customer_name,
            $order->user?->email,
            $order->phone,
            $order->product_name,
            number_format($order->subtotal_paise / 100, 2, '.', ''),
            number_format($order->discount_paise / 100, 2, '.', ''),
            $order->coupon_code,
            number_format($order->shipping_charges / 100, 2, '.', ''),
            number_format($order->tax_amount / 100, 2, '.', ''),
            number_format($order->amount_paise / 100, 2, '.', ''),
            OrderPaymentStatus::label((int) $order->payment_status),
            $order->payment_method,
            OrderFulfillmentStatus::label((string) $order->fulfillment_status),
            $order->razorpay_order_id,
            $order->razorpay_payment_id,
            $shipping['address_line1'] ?? null,
            $shipping['address_line2'] ?? null,
            $shipping['city'] ?? null,
            $shipping['state'] ?? null,
            $shipping['pincode'] ?? null,
            $shipping['country'] ?? null,
        ];
    }
}

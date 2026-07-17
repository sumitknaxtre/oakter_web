<?php

namespace App\Exports;

use App\Models\Order;

class OrderPaymentIdsExport
{
    /**
     * @return list<string>
     */
    public static function headers(): array
    {
        return [
            'Order ID',
            'Customer name',
            'Email',
            'Phone',
            'Razorpay payment ID',
        ];
    }

    /**
     * @return list<string|null>
     */
    public static function row(Order $order): array
    {
        return [
            $order->displayOrderCode(),
            $order->customer_name,
            $order->user?->email,
            $order->phone,
            $order->razorpay_payment_id,
        ];
    }
}

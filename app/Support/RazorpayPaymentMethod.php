<?php

namespace App\Support;

final class RazorpayPaymentMethod
{
    /**
     * @param  array<string, mixed>  $payment
     */
    public static function labelFromPayment(array $payment): string
    {
        $method = strtolower((string) ($payment['method'] ?? ''));

        return match ($method) {
            'card' => self::cardLabel($payment),
            'upi' => 'UPI',
            'netbanking' => 'Net Banking',
            'wallet' => 'Wallet',
            'emi' => 'EMI',
            'paylater' => 'Pay Later',
            'nach' => 'NACH',
            'cardless_emi' => 'Cardless EMI',
            default => $method !== '' ? ucwords(str_replace('_', ' ', $method)) : 'Razorpay',
        };
    }

    /**
     * @param  array<string, mixed>  $payment
     */
    private static function cardLabel(array $payment): string
    {
        $network = $payment['card']['network'] ?? null;

        if (is_string($network) && $network !== '') {
            return ucfirst(strtolower($network)).' Card';
        }

        return 'Card';
    }
}

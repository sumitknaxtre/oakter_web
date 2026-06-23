<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class PendingCheckoutService
{
    private const CACHE_PREFIX = 'checkout.pending.';

    private const TTL_HOURS = 2;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function store(string $razorpayOrderId, array $payload): void
    {
        Cache::put(
            self::CACHE_PREFIX.$razorpayOrderId,
            $payload,
            now()->addHours(self::TTL_HOURS),
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    public function pull(string $razorpayOrderId): ?array
    {
        $payload = Cache::get(self::CACHE_PREFIX.$razorpayOrderId);

        if ($payload === null) {
            return null;
        }

        Cache::forget(self::CACHE_PREFIX.$razorpayOrderId);

        return $payload;
    }
}

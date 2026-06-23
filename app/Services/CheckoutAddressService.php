<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAddress;

class CheckoutAddressService
{
    /**
     * @return array{
     *     shipping_address_id: int,
     *     shipping_snapshot: array<string, mixed>,
     *     billing_address_id: int,
     *     billing_snapshot: array<string, mixed>,
     *     billing_same_as_shipping: bool
     * }
     */
    public function persistForCheckout(User $user, array $validated): array
    {
        $billingSame = (bool) $validated['billing_same_as_shipping'];
        $shippingPayload = $this->shippingPayload($validated);
        $shippingAddress = $this->upsertAddress($user, $shippingPayload);
        $shippingAddress->update([
            'last_used_at' => now(),
            'is_default' => true,
        ]);

        $user->addresses()
            ->whereKeyNot($shippingAddress->id)
            ->update(['is_default' => false]);

        if ($billingSame) {
            return [
                'shipping_address_id' => $shippingAddress->id,
                'shipping_snapshot' => $shippingPayload,
                'billing_address_id' => $shippingAddress->id,
                'billing_snapshot' => $shippingPayload,
                'billing_same_as_shipping' => true,
            ];
        }

        $billingPayload = $this->billingPayload($validated);
        $billingAddress = $this->upsertAddress($user, $billingPayload);

        return [
            'shipping_address_id' => $shippingAddress->id,
            'shipping_snapshot' => $shippingPayload,
            'billing_address_id' => $billingAddress->id,
            'billing_snapshot' => $billingPayload,
            'billing_same_as_shipping' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function shippingPayload(array $validated): array
    {
        return [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
            'address_line1' => $validated['address_line1'],
            'address_line2' => $validated['address_line2'] ?? null,
            'city' => $validated['city'],
            'state' => $validated['state'],
            'pincode' => $validated['pincode'],
            'country' => $validated['country'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function billingPayload(array $validated): array
    {
        return [
            'first_name' => $validated['billing_first_name'],
            'last_name' => $validated['billing_last_name'],
            'phone' => $validated['phone'],
            'address_line1' => $validated['billing_address_line1'],
            'address_line2' => $validated['billing_address_line2'] ?? null,
            'city' => $validated['billing_city'],
            'state' => $validated['billing_state'],
            'pincode' => $validated['billing_pincode'],
            'country' => $validated['billing_country'],
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function upsertAddress(User $user, array $payload): UserAddress
    {
        $existing = $user->addresses()
            ->where('address_line1', $payload['address_line1'])
            ->where('pincode', $payload['pincode'])
            ->first();

        if ($existing !== null) {
            $existing->update($payload);

            return $existing;
        }

        return $user->addresses()->create($payload);
    }
}

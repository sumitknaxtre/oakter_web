<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\UserAddress;

class AdminOrderCustomerService
{
    /**
     * @return array<string, mixed>
     */
    public function formDefaults(Order $order): array
    {
        $order->loadMissing('user');

        $shipping = $order->shipping_snapshot ?? [];
        $billing = $order->billing_snapshot ?? [];

        return [
            'email' => old('email', $order->user?->email ?? ''),
            'phone' => old('phone', $shipping['phone'] ?? $order->user?->phone ?? ''),
            'first_name' => old('first_name', $shipping['first_name'] ?? $order->user?->first_name ?? ''),
            'last_name' => old('last_name', $shipping['last_name'] ?? $order->user?->last_name ?? ''),
            'address_line1' => old('address_line1', $shipping['address_line1'] ?? ''),
            'address_line2' => old('address_line2', $shipping['address_line2'] ?? ''),
            'city' => old('city', $shipping['city'] ?? ''),
            'state' => old('state', $shipping['state'] ?? ''),
            'pincode' => old('pincode', $shipping['pincode'] ?? ''),
            'country' => old('country', $shipping['country'] ?? 'India'),
            'billing_same_as_shipping' => old('billing_same_as_shipping', $order->billing_same_as_shipping),
            'billing_first_name' => old('billing_first_name', $billing['first_name'] ?? ''),
            'billing_last_name' => old('billing_last_name', $billing['last_name'] ?? ''),
            'billing_address_line1' => old('billing_address_line1', $billing['address_line1'] ?? ''),
            'billing_address_line2' => old('billing_address_line2', $billing['address_line2'] ?? ''),
            'billing_city' => old('billing_city', $billing['city'] ?? ''),
            'billing_state' => old('billing_state', $billing['state'] ?? ''),
            'billing_pincode' => old('billing_pincode', $billing['pincode'] ?? ''),
            'billing_country' => old('billing_country', $billing['country'] ?? 'India'),
        ];
    }

    public function update(Order $order, array $validated): void
    {
        $order->loadMissing(['user', 'shippingAddress', 'billingAddress']);

        $billingSame = (bool) ($validated['billing_same_as_shipping'] ?? true);
        $shippingSnapshot = $this->shippingSnapshot($validated);
        $billingSnapshot = $billingSame
            ? $shippingSnapshot
            : $this->billingSnapshot($validated);

        $order->update([
            'shipping_snapshot' => $shippingSnapshot,
            'billing_snapshot' => $billingSnapshot,
            'billing_same_as_shipping' => $billingSame,
        ]);

        if ($order->user instanceof User) {
            $order->user->update([
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
            ]);
        }

        $this->syncLinkedAddress($order->shippingAddress, $shippingSnapshot);

        if ($billingSame) {
            if (
                $order->billing_address_id !== null
                && $order->billing_address_id !== $order->shipping_address_id
                && $order->billingAddress instanceof UserAddress
            ) {
                $order->billingAddress->update($this->addressAttributes($shippingSnapshot));
            }

            if ($order->billing_address_id !== $order->shipping_address_id) {
                $order->update(['billing_address_id' => $order->shipping_address_id]);
            }

            return;
        }

        $this->syncLinkedAddress($order->billingAddress, $billingSnapshot);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function shippingSnapshot(array $validated): array
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
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function billingSnapshot(array $validated): array
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
     * @param  array<string, mixed>  $snapshot
     * @return array<string, mixed>
     */
    private function addressAttributes(array $snapshot): array
    {
        return [
            'first_name' => $snapshot['first_name'],
            'last_name' => $snapshot['last_name'],
            'address_line1' => $snapshot['address_line1'],
            'address_line2' => $snapshot['address_line2'],
            'city' => $snapshot['city'],
            'state' => $snapshot['state'],
            'pincode' => $snapshot['pincode'],
            'country' => $snapshot['country'],
        ];
    }

    /**
     * @param  array<string, mixed>  $snapshot
     */
    private function syncLinkedAddress(?UserAddress $address, array $snapshot): void
    {
        if (! $address instanceof UserAddress) {
            return;
        }

        $address->update($this->addressAttributes($snapshot));
    }
}

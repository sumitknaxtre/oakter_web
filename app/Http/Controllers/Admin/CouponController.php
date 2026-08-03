<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Http\Requests\Admin\UpdateCouponRequest;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        $coupons = Coupon::query()
            ->with('products')
            ->withCount('orders')
            ->latest()
            ->paginate(15);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create(): View
    {
        return view('admin.coupons.create', [
            'coupon' => new Coupon(['is_active' => true]),
            'products' => Product::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreCouponRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $coupon = Coupon::query()->create([
            'code' => $validated['code'],
            'discount_amount_paise' => (int) round($validated['discount_amount'] * 100),
            'is_active' => $validated['is_active'] ?? false,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'usage_limit' => $validated['usage_limit'] ?? null,
        ]);

        $coupon->products()->sync($validated['product_ids']);

        return redirect()
            ->route('admin.coupons.index')
            ->with('status', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon): View
    {
        $coupon->load('products');

        return view('admin.coupons.edit', [
            'coupon' => $coupon,
            'products' => Product::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $validated = $request->validated();

        $coupon->update([
            'code' => $validated['code'],
            'discount_amount_paise' => (int) round($validated['discount_amount'] * 100),
            'is_active' => $validated['is_active'] ?? false,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'usage_limit' => $validated['usage_limit'] ?? null,
        ]);

        $coupon->products()->sync($validated['product_ids']);

        return redirect()
            ->route('admin.coupons.index')
            ->with('status', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('status', 'Coupon deleted successfully.');
    }
}

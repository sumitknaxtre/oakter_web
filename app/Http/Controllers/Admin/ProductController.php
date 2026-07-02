<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->withCount(['orders as orders_count' => fn ($query) => $query->paid()])
            ->orderBy('name')
            ->get();

        return view('admin.products.index', compact('products'));
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        $product->updatePrices(
            (float) $validated['price'],
            (float) $validated['mrp'],
        );

        $product->update([
            'is_in_stock' => $request->boolean('is_in_stock'),
            'hide_buy_button' => $request->boolean('hide_buy_button'),
            'sku' => filled($validated['sku'] ?? null) ? $validated['sku'] : null,
            'package_weight_kg' => $validated['package_weight_kg'],
            'package_length_cm' => $validated['package_length_cm'],
            'package_breadth_cm' => $validated['package_breadth_cm'],
            'package_height_cm' => $validated['package_height_cm'],
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Product updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopifyOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopifyOrderController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim($request->string('q')->toString());
        $customerId = $request->integer('customer');

        $orders = ShopifyOrder::query()
            ->with('customer')
            ->when($customerId > 0, fn ($query) => $query->where('shopify_customer_id', $customerId))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('shopify_id', 'like', '%'.$q.'%')
                        ->orWhere('order_number', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%')
                        ->orWhere('lineitem_name', 'like', '%'.$q.'%')
                        ->orWhere('payment_reference', 'like', '%'.$q.'%')
                        ->orWhere('discount_code', 'like', '%'.$q.'%');
                });
            })
            ->orderByDesc('shopify_created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.shopify_orders.index', [
            'orders' => $orders,
            'q' => $q,
            'customerId' => $customerId > 0 ? $customerId : null,
        ]);
    }

    public function show(ShopifyOrder $shopifyOrder): View
    {
        $shopifyOrder->load('customer');

        return view('admin.shopify_orders.show', [
            'order' => $shopifyOrder,
        ]);
    }
}

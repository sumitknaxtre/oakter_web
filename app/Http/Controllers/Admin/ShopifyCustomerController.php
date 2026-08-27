<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopifyCustomer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopifyCustomerController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim($request->string('q')->toString());

        $customers = ShopifyCustomer::query()
            ->withCount('orders')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('shopify_id', 'like', '%'.$q.'%')
                        ->orWhere('name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%')
                        ->orWhere('phones', 'like', '%'.$q.'%');
                });
            })
            ->orderByDesc('total_orders')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.shopify_customers.index', [
            'customers' => $customers,
            'q' => $q,
        ]);
    }

    public function show(ShopifyCustomer $shopifyCustomer): View
    {
        $shopifyCustomer->load([
            'orders' => fn ($query) => $query->orderByDesc('shopify_created_at'),
        ]);

        return view('admin.shopify_customers.show', [
            'customer' => $shopifyCustomer,
        ]);
    }
}

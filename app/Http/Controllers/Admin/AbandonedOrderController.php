<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\AdminAbandonedOrderFilters;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbandonedOrderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = AdminAbandonedOrderFilters::normalize(
            AdminAbandonedOrderFilters::makeValidator($request)->validate(),
        );

        $orders = Order::query()
            ->with('user')
            ->pendingPayment()
            ->tap(fn ($query) => AdminAbandonedOrderFilters::apply($query, $filters))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.abandoned_orders.index', [
            'orders' => $orders,
            'filters' => $filters,
            'hasActiveFilters' => AdminAbandonedOrderFilters::hasActiveFilters($filters),
        ]);
    }
}

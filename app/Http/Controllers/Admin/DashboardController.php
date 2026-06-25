<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'total_orders' => Order::query()->paid()->count(),
            'revenue' => Order::query()->paid()->sum('amount_paise') / 100,
        ];

        $recentOrders = Order::query()
            ->with('user')
            ->paid()
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}

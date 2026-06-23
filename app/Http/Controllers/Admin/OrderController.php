<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\AdminOrderFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $validator = AdminOrderFilters::makeValidator($request);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.orders.index', array_filter([
                    'q' => $request->string('q')->toString(),
                ]))
                ->withErrors($validator)
                ->withInput();
        }

        $filters = AdminOrderFilters::normalize($validator->validated());

        $orders = Order::query()
            ->with('user')
            ->tap(fn ($query) => AdminOrderFilters::apply($query, $filters))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'filters' => $filters,
            'hasActiveFilters' => AdminOrderFilters::hasActiveFilters($filters),
            'exportUrl' => route('admin.orders.export', AdminOrderFilters::queryParameters($filters)),
            'maxFilterDate' => AdminOrderFilters::maxFilterDate(),
        ]);
    }

    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        $validator = AdminOrderFilters::makeValidator($request);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.orders.index', array_filter([
                    'q' => $request->string('q')->toString(),
                ]))
                ->withErrors($validator)
                ->withInput();
        }

        $filters = AdminOrderFilters::normalize($validator->validated());

        $filename = 'oakter-orders-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($filters) {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, OrdersExport::headers());

            $query = Order::query()
                ->with('user')
                ->tap(fn ($builder) => AdminOrderFilters::apply($builder, $filters))
                ->latest();

            foreach ($query->cursor() as $order) {
                fputcsv($handle, OrdersExport::row($order));
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function show(Order $order): View
    {
        $order->load('user');

        return view('admin.orders.show', compact('order'));
    }
}

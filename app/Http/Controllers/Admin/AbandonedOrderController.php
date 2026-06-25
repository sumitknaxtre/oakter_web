<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AbandonedOrdersExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\AdminAbandonedOrderFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AbandonedOrderController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $validator = AdminAbandonedOrderFilters::makeValidator($request);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.abandoned-orders.index', array_filter([
                    'q' => $request->string('q')->toString(),
                ]))
                ->withErrors($validator)
                ->withInput();
        }

        $filters = AdminAbandonedOrderFilters::normalize($validator->validated());

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
            'exportUrl' => route('admin.abandoned-orders.export', AdminAbandonedOrderFilters::queryParameters($filters)),
        ]);
    }

    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        $validator = AdminAbandonedOrderFilters::makeValidator($request);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.abandoned-orders.index', array_filter([
                    'q' => $request->string('q')->toString(),
                ]))
                ->withErrors($validator)
                ->withInput();
        }

        $filters = AdminAbandonedOrderFilters::normalize($validator->validated());

        $filename = 'oakter-abandoned-orders-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($filters) {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, AbandonedOrdersExport::headers());

            $query = Order::query()
                ->with('user')
                ->pendingPayment()
                ->tap(fn ($builder) => AdminAbandonedOrderFilters::apply($builder, $filters))
                ->latest();

            foreach ($query->cursor() as $order) {
                fputcsv($handle, AbandonedOrdersExport::row($order));
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

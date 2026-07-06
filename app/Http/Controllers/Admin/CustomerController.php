<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CustomersExport;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminCustomerFilters;
use App\Support\OrderPaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $validator = AdminCustomerFilters::makeValidator($request);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.customers.index', array_filter([
                    'q' => $request->string('q')->toString(),
                    'sort' => $request->string('sort')->toString(),
                ]))
                ->withErrors($validator)
                ->withInput();
        }

        $filters = AdminCustomerFilters::normalize($validator->validated());

        $customers = $this->customerQuery($filters)
            ->tap(fn ($query) => AdminCustomerFilters::applySort($query, $filters))
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'filters' => $filters,
            'hasActiveFilters' => AdminCustomerFilters::hasActiveFilters($filters),
            'exportUrl' => route('admin.customers.export', AdminCustomerFilters::queryParameters($filters)),
        ]);
    }

    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        $validator = AdminCustomerFilters::makeValidator($request);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.customers.index', array_filter([
                    'q' => $request->string('q')->toString(),
                    'sort' => $request->string('sort')->toString(),
                ]))
                ->withErrors($validator)
                ->withInput();
        }

        $filters = AdminCustomerFilters::normalize($validator->validated());

        $filename = 'oakter-customers-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($filters) {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, CustomersExport::headers());

            $query = $this->customerQuery($filters);
            AdminCustomerFilters::applySort($query, $filters);

            foreach ($query->cursor() as $customer) {
                fputcsv($handle, CustomersExport::row($customer));
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @param  array{q: string}  $filters
     * @return Builder<User>
     */
    private function customerQuery(array $filters): Builder
    {
        return User::query()
            ->whereHas('role', fn ($query) => $query->where('name', Role::CUSTOMER))
            ->withCount([
                'orders as abandoned_orders_count' => fn ($query) => $query->where('payment_status', OrderPaymentStatus::Pending),
            ])
            ->tap(fn ($query) => AdminCustomerFilters::apply($query, $filters));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminCustomerFilters;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $filters = AdminCustomerFilters::normalize(
            AdminCustomerFilters::makeValidator($request)->validate(),
        );

        $customers = User::query()
            ->whereHas('role', fn ($query) => $query->where('name', Role::CUSTOMER))
            ->tap(fn ($query) => AdminCustomerFilters::apply($query, $filters))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'filters' => $filters,
            'hasActiveFilters' => AdminCustomerFilters::hasActiveFilters($filters),
        ]);
    }
}

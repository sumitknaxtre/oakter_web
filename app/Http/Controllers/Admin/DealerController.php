<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDealerRequest;
use App\Http\Requests\Admin\UpdateDealerRequest;
use App\Models\Dealer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DealerController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim($request->string('q')->toString());

        $dealers = Dealer::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', '%'.$q.'%')
                        ->orWhere('state', 'like', '%'.$q.'%')
                        ->orWhere('district', 'like', '%'.$q.'%')
                        ->orWhere('phone', 'like', '%'.$q.'%');
                });
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('admin.dealers.index', [
            'dealers' => $dealers,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('admin.dealers.create', [
            'dealer' => new Dealer([
                'is_active' => true,
                'sort_order' => 0,
            ]),
            'states' => config('dealer_regions'),
        ]);
    }

    public function store(StoreDealerRequest $request): RedirectResponse
    {
        Dealer::query()->create($request->validated());

        return redirect()
            ->route('admin.dealers.index')
            ->with('status', 'Dealer created successfully.');
    }

    public function edit(Dealer $dealer): View
    {
        return view('admin.dealers.edit', [
            'dealer' => $dealer,
            'states' => config('dealer_regions'),
        ]);
    }

    public function update(UpdateDealerRequest $request, Dealer $dealer): RedirectResponse
    {
        $dealer->update($request->validated());

        return redirect()
            ->route('admin.dealers.index')
            ->with('status', 'Dealer updated successfully.');
    }

    public function destroy(Dealer $dealer): RedirectResponse
    {
        $dealer->delete();

        return redirect()
            ->route('admin.dealers.index')
            ->with('status', 'Dealer deleted successfully.');
    }
}

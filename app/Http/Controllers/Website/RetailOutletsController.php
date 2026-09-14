<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RetailOutletsController extends Controller
{
    public function index(): View
    {
        return view('website.retail_outlets', [
            'states' => Dealer::activeStates(),
            'dealersUrl' => route('website.retail_outlets.dealers'),
        ]);
    }

    public function dealers(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'state' => ['required', 'string', Rule::in(Dealer::activeStates())],
        ]);

        $state = $validated['state'];

        $dealers = Dealer::query()
            ->active()
            ->where('state', $state)
            ->ordered()
            ->get(['name', 'address', 'phone', 'map_url', 'district']);

        $districts = [];

        foreach ($dealers as $dealer) {
            $district = $dealer->district;

            if (! isset($districts[$district])) {
                $districts[$district] = [
                    'name' => $district,
                    'outlets' => [],
                ];
            }

            $districts[$district]['outlets'][] = [
                'name' => $dealer->name,
                'address' => $dealer->address,
                'phone' => $dealer->phone,
                'map_url' => $dealer->map_url,
            ];
        }

        return response()->json([
            'state' => $state,
            'districts' => array_values($districts),
        ]);
    }
}

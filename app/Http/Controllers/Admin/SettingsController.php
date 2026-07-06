<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdminSettingsRequest;
use App\Support\AdminSettingKeys;
use App\Support\AdminSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'shippingDaysEstimate' => AdminSettings::get(AdminSettingKeys::SHIPPING_DAYS_ESTIMATE),
        ]);
    }

    public function update(UpdateAdminSettingsRequest $request): RedirectResponse
    {
        AdminSettings::set(
            AdminSettingKeys::SHIPPING_DAYS_ESTIMATE,
            $request->validated('shipping_days_estimate'),
        );

        return back()->with('status', 'Settings saved successfully.');
    }
}

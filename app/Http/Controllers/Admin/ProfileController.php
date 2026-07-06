<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Http\Requests\Admin\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $errors = session('errors');

        $activeTab = request('tab') === 'password' ? 'password' : 'profile';

        if ($errors !== null && $errors->hasAny(['current_password', 'password', 'password_confirmation'])) {
            $activeTab = 'password';
        }

        return view('admin.profile.edit', [
            'admin' => Auth::user(),
            'activeTab' => $activeTab,
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        Auth::user()->update($request->validated());

        return back()->with('status', 'Profile updated successfully.');
    }

    public function editPassword(): RedirectResponse
    {
        return redirect()->route('admin.profile.edit', ['tab' => 'password']);
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        Auth::user()->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return redirect()
            ->route('admin.profile.edit', ['tab' => 'password'])
            ->with('status', 'Password updated successfully.');
    }
}

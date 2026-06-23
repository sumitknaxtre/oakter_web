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
        return view('admin.profile.edit', [
            'admin' => Auth::user(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        Auth::user()->update($request->validated());

        return back()->with('status', 'Profile updated successfully.');
    }

    public function editPassword(): View
    {
        return view('admin.profile.password');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        Auth::user()->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return back()->with('status', 'Password updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubAdminRequest;
use App\Http\Requests\Admin\UpdateSubAdminRequest;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminPermissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubAdminController extends Controller
{
    public function index(): View
    {
        $subAdmins = User::query()
            ->whereHas('role', fn ($query) => $query->where('name', Role::SUB_ADMIN))
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('admin.sub_admins.index', compact('subAdmins'));
    }

    public function create(): View
    {
        return view('admin.sub_admins.create', [
            'subAdmin' => new User([
                'admin_permissions' => [],
            ]),
            'permissionOptions' => AdminPermissions::sidebarOptions(),
        ]);
    }

    public function store(StoreSubAdminRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::query()->create([
            'role_id' => $this->subAdminRoleId(),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'admin_permissions' => AdminPermissions::normalize($validated['permissions'] ?? []),
        ]);

        return redirect()
            ->route('admin.sub-admins.index')
            ->with('status', 'Sub admin created successfully.');
    }

    public function edit(User $sub_admin): View
    {
        $this->ensureSubAdmin($sub_admin);

        return view('admin.sub_admins.edit', [
            'subAdmin' => $sub_admin,
            'permissionOptions' => AdminPermissions::sidebarOptions(),
        ]);
    }

    public function update(UpdateSubAdminRequest $request, User $sub_admin): RedirectResponse
    {
        $this->ensureSubAdmin($sub_admin);

        $validated = $request->validated();

        $payload = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'admin_permissions' => AdminPermissions::normalize($validated['permissions'] ?? []),
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        $sub_admin->update($payload);

        return redirect()
            ->route('admin.sub-admins.index')
            ->with('status', 'Sub admin updated successfully.');
    }

    public function destroy(User $sub_admin): RedirectResponse
    {
        $this->ensureSubAdmin($sub_admin);

        $sub_admin->delete();

        return redirect()
            ->route('admin.sub-admins.index')
            ->with('status', 'Sub admin deleted successfully.');
    }

    private function subAdminRoleId(): int
    {
        return Role::query()->firstOrCreate(
            ['name' => Role::SUB_ADMIN],
            ['label' => 'Sub Admin'],
        )->id;
    }

    private function ensureSubAdmin(User $user): void
    {
        abort_unless($user->isSubAdmin(), 404);
    }
}

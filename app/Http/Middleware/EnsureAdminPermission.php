<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isStaff()) {
            return redirect()->route('admin.login');
        }

        if ($user->hasAdminPermission($permission)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(403, 'You do not have permission to access this section.');
        }

        return redirect()
            ->route('admin.profile.edit')
            ->with('status', 'You do not have permission to access that section.');
    }
}

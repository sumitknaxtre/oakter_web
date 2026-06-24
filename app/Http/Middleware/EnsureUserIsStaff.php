<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isStaff()) {
            if ($user !== null) {
                auth()->logout();
            }

            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}

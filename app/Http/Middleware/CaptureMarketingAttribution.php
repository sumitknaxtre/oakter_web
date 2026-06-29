<?php

namespace App\Http\Middleware;

use App\Support\OrderAttribution;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stores first-touch UTM tags and referrer in session for later order attribution.
 */
class CaptureMarketingAttribution
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->is('admin', 'admin/*') && ! $request->session()->has(OrderAttribution::SESSION_KEY)) {
            $request->session()->put(
                OrderAttribution::SESSION_KEY,
                OrderAttribution::captureFromRequest($request),
            );
        }

        return $next($request);
    }
}

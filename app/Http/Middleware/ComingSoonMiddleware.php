<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Set the "Coming Soon" page
 * - If Coming Soon mode is completely disabled, proceed normally
 * - Always allow access to the paths relating to Lunar, Stripe and Livewire components
 * - Allow Lunar staff members to bypass the "coming soon" page on public webpages
 * - Render the coming-soon view directly on the root URL
 * - Redirect any other route attempts back to the splash page
 */
class ComingSoonMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('app.coming_soon', false)) {
            return $next($request);
        }

        if ($request->is(
            'google*',
            'hub*',
            'livewire*',
            'lunar*',
            'stripe*',
            'r.stripe*'
        )) {
            return $next($request);
        }

        if (Auth::guard('staff')->check()) {
            return $next($request);
            // $staff = Auth::guard('staff')->user();
            // if ($staff->admin) {}
        }

        if ($request->is('/')) {
            return response()->view('coming-soon');
        }

        return redirect('/');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lunar\Admin\Models\Staff;
use Symfony\Component\HttpFoundation\Response;

class ComingSoonMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. If Coming Soon mode is completely disabled, proceed normally
        if (!config('app.coming_soon', false)) {
            return $next($request);
        }

        // 2. ALWAYS allow access to the Lunar backend paths and Livewire components
        // Lunar typically routes through '/lunar' or '/hub' depending on your setup
        if ($request->is('lunar*') || $request->is('hub*') || $request->is('livewire*')) {
            return $next($request);
        }

        // 3. ALLOW Lunar Admins to bypass coming soon on public store pages
        // Lunar uses the 'lunar' or 'lunar:hub' auth guard depending on your package version
        if (auth()->user() instanceof Staff) {
            return $next($request);
        }

        // 4. Render the coming-soon view directly on the root URL
        if ($request->is('/')) {
            return response()->view('coming-soon');
        }

        // 5. Redirect any other route attempts back to the splash page
        return redirect('/');
    }
}

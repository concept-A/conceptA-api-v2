<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsSubscribedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Ensure the user is authenticated
        if (!$user) {
            abort(403, 'Unauthorized: User not authenticated.');
        }

        // Check if the user has a profile
        if (!$user->profile) {
            abort(403, 'Unauthorized: Profile not found.');
        }

        // Check if the subscription is positive
        if ($user->profile->subscription <= 0) {
            abort(403, 'Unauthorized: Subscription is inactive or expired.');
        }

        return $next($request);
    }
}

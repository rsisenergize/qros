<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        try {
            $user = auth()->user();
        } catch (\Exception $e) {

            return $next($request);
        }

        if (is_null($user?->restaurant_id) && is_null($user?->branch_id)) {
            return $next($request);
        }

        if (!$user->isRestaurantApproved() && Route::currentRouteName() !== 'account_unverified') {
            return redirect()->route('account_unverified');
        }

        if (!$user?->isRestaurantActive()) {
            Auth::logout();
            session()->flush();
            return redirect()->route('login')->withErrors(['email' => __('Restaurant is inactive. Contact admin.')]);
        }

        return $next($request);
    }
}

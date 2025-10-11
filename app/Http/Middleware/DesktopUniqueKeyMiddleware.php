<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Branch;
use Illuminate\Support\Facades\Log;

class DesktopUniqueKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {

        $key = $request->header('X-TABLETRACK-KEY');
        $dev = app()->environment('development');

        if (!$key && !$dev) {
            return response()->json(['message' => 'No authentication key found'], 401);
        }

        $branch = $dev ? Branch::first() : Branch::where('unique_hash', $key)->first();


        if (!$branch) {
            return response()->json(['message' => 'Invalid authentication key'], 401);
        }

        $request->merge(['branch' => $branch]);
        return $next($request);
    }
}

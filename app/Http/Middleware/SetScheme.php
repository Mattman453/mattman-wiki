<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetScheme
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if ($request->isSecure()) URL::forceScheme('https');
            else URL::forceScheme('http');
        } catch (\Exception $e) {
            Log::error('SetScheme error: ' . $e->getMessage());
        }
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as MiddlewareAuthenticate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate extends MiddlewareAuthenticate
{
    protected function redirectTo(Request $request)
    {
        return $request->expectsJson() ? null : route('auth.login');
    }
}

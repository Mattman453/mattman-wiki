<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as MiddlewareAuthenticate;
use Illuminate\Http\Request;

class Authenticate extends MiddlewareAuthenticate
{
    protected function redirectTo(Request $request)
    {
        return $request->expectsJson() ? null : route('auth.login');
    }
}
